<?php
isset($PDE) or die('Nope');
session_start();

require_once 'config.php';
require_once 'classes/Filter.php';
require_once 'classes/Database.php';
require_once 'classes/HTTPResponse.php';
require_once 'classes/HTMLUtil.php';

Database::startInstance($CONFIG_PDO_CONN, $CONFIG_PDO_USER, $CONFIG_PDO_PASS);

$version = (new DateTimeImmutable())->format('YmdH');

$form_clear = array();
$page_title = 'Sistema de controle de empréstimos';
$is_logged = isset($_SESSION['operator']) || is_null($CONFIG_AUTH_MODE);
$is_searching = false;

function password_pepper($password){
    global $CONFIG_SECRET_PEPPER;
    return hash_hmac("sha256", $password, $CONFIG_SECRET_PEPPER);
}
function pde_password_hash($password_hash){
    return password_hash(password_pepper($password_hash),PASSWORD_BCRYPT);
}

foreach($_GET as $key => $value){
    if (is_array($value)){
        $form_clear[$key] = $value;
    } else {
        $form_clear[$key] = @htmlspecialchars(trim($value));
    }    
}
$is_posting = FALSE;
foreach($_POST as $key => $value){
    if (is_array($value)){
        $form_clear[$key] = $value;
    } else {
        $form_clear[$key] = @htmlspecialchars(trim($value));
    }
    $is_posting = TRUE;
}
if ($is_posting){
    if (!isset($form_clear['csrf_token'])|| !isset($_SESSION['csrf_token'])){
        HTTPResponse::forbidden('CSRF protection.');
    }
    if ($form_clear['csrf_token'] != $_SESSION['csrf_token']){
        HTTPResponse::forbidden('CSRF invalid.');
    }
}

$_SESSION['csrf_token'] = password_hash(microtime(),PASSWORD_DEFAULT);

if(isset($form_clear['logoff'])){
    unset($_SESSION['operator']);
    HTTPResponse::redirect("?");
}

if (!$is_logged){
    if (isset($form_clear['login']) && isset($form_clear['password'])){
        $query_login = "SELECT name, login, password FROM user WHERE login = ?";
        $params = array($form_clear['login']);      
        $user = Database::fetch($query_login, $params);         
        if ($user){
            $pwd_peppered = password_pepper($form_clear['password']);
            $pwd_hashed = $user['password'];   
            $verified = password_verify($pwd_peppered, $pwd_hashed);

            if ($verified) {
                $_SESSION['operator'] = $user;
                HTTPResponse::redirect("?");
            }
        }       
    }
} else {
    $response = array();
    $response_json = FALSE;

    $rct = @$_SERVER['CONTENT_TYPE'];
    $request_content_type = explode(";", $rct)[0];
    if ($request_content_type == "application/json"){
        $form_clear = get_object_vars(json_decode(file_get_contents('php://input'))); 
        $response_json = TRUE;   
    }


    if (@$form_clear['return_to'] == 'json'){
        $response_json = TRUE;
    }

    $search_results = array();

    $search_query_focus = FALSE;

    $search_one_item = FALSE;

    $current_query_string = @$form_clear['q'];
    $current_query_type_string = @$form_clear['t'];

    $is_search_type_user = @$form_clear['t'] == 'user' ;
    $is_search_type_item = !$is_search_type_user;

    $is_install = isset($form_clear['install']) ;

    try{
        Database::fetchOne("SELECT id FROM user WHERE id = ? ", array(1));
    } catch (Exception $e){
        include_once 'install.php';
        HTTPResponse::redirect("?install");
    }

    $is_inventory = isset($form_clear['inventory']);
    $is_update_from_worksheet = isset($form_clear['act']) && $form_clear['act'] == 'update';
    $is_insert_from_worksheet = isset($form_clear['act']) && $form_clear['act'] == 'insert';
    $is_show_patrimony = isset($form_clear['pid']) && @$form_clear['pid'] != '';

    $is_show_item = isset($form_clear['iid']) & @$form_clear['iid'] != '' && @$form_clear['redirect_to'] != 'user';

    $is_searching = isset($_GET['q']) && $current_query_string != '';
    $is_selecting_user = isset($form_clear['uid']) && @$form_clear['uid'] != '';

    $is_returning_item = isset($form_clear['nid']) && @$form_clear['act'] == 'ret';

    $option_search_user_checked = !$is_inventory && (!isset($form_clear['t'])) || 
                                (@$form_clear['t'] == 'user') ? 
                                'checked' : '';

    $option_search_item_checked = (@$form_clear['t'] == 'item') || $is_inventory || 
                                ( @$form_clear['t'] != 'user' && isset($form_clear['uid']) ) ? 
                                'checked' : '' ;

    $last_user_selected = @$_SESSION['last_user_selected'];                            
    $is_changed_user = @$form_clear['uid'] != '' &&((is_null($last_user_selected)) || $last_user_selected['id'] != $form_clear['uid']);

    $current_date_now = (new DateTimeImmutable())->format('Y-m-d');

    $all_details_separator_items = ":--:";
    $all_details_separator_cols = "::::";
    $all_details_sql_concat = "group_concat(
                                        concat(nn.id, '$all_details_separator_cols' , 
                                            nn.tstamp, '$all_details_separator_cols' , 
                                            details), 
                                '$all_details_separator_items'
                                )";

    $action_save_new_user = isset($_POST['save_new_user']);
    $action_save_edit_user = isset($_POST['save_edit_user']);

    $action_save_new_model = isset($_POST['save_new_model']);
    $action_save_edit_model = isset($_POST['save_edit_model']);

    $action_save_new_patrimony = isset($_POST['save_new_patrimony']);
    $action_save_edit_patrimony = isset($_POST['save_edit_patrimony']);

    $is_deleting = @$form_clear['act'] == 'delete';


    if (isset($form_clear['after'])){
        unset($_SESSION['default_date_after']);
    }
    if (isset($form_clear['before'])){
        unset($_SESSION['default_date_before']);
    }

    if (isset($_SESSION['default_date_after'])){
        $default_date_after = $_SESSION['default_date_after'];
    }  else {
        $default_date_after = $current_date_now;
        $_SESSION['default_date_after'] = $default_date_after;
    } 

    if (isset($_SESSION['default_date_before'])){
        $default_date_before = $_SESSION['default_date_before'];
    }  else {
        $default_date_before = (new DateTimeImmutable("-1 month"))->format('Y-m-d');
        $_SESSION['default_date_before'] = $default_date_before;
    }                             
                    
    $current_date_after = isset($form_clear['after']) ? 
                            (new DateTimeImmutable($form_clear['after']))->format('Y-m-d') :
                                $default_date_after;

    $current_date_before = isset($form_clear['before']) ? 
                            (new DateTimeImmutable($form_clear['before']))->format('Y-m-d') :
                            $default_date_before;


    $is_loaning = @$form_clear['act'] == 'loan';



    if ($is_deleting){
        if (isset($form_clear['nnid'])){
            $ret = Database::execute("DELETE FROM log_loan WHERE id = ? AND diff = 0", array($form_clear['nnid']));
            HTTPResponse::JSON($ret);
        }
    } else if ($action_save_new_user) {
        require_once 'include/form_user.php';
        form_user_save_new($form_clear);
        HTTPResponse::redirect('?'.$redirect_url);

    } else if ($action_save_edit_user) {
        $redirect_url = http_build_query(array(
            'uid' => $form_clear['uid'],
            'q'=> $current_query_string,
            't' => $current_query_type_string,
            'before' => $current_date_before,
            'after' => $current_date_after,
        ));
        require_once 'include/form_user.php';
        form_user_save_edit($form_clear);
        HTTPResponse::redirect('?'.$redirect_url);

    } else if ($action_save_new_model || $action_save_edit_model){    
        require_once 'include/form_model.php';
        form_model_save($form_clear);
    } else if ($action_save_new_patrimony || $action_save_edit_patrimony){    
        require_once 'include/form_patrimony.php';
        form_patrimony_save($form_clear);
    } else if ($is_update_from_worksheet){
        $table_name = htmlspecialchars($form_clear['table_name']);
        $column_name = htmlspecialchars($form_clear['column_name']);
        $query = "UPDATE $table_name SET $column_name = ? WHERE id = ?";
        $params = array(@$form_clear['value'], @$form_clear['id']);
        try{
            $ret = Database::execute($query, $params);
            $query = "SELECT * FROM $table_name WHERE id = ?";
            $params = array(@$form_clear['id']);
            $ret = Database::fetch($query, $params);
        } catch(Exception $e){
            $ret = array('error'=>$e, 'query'=>$query);
        }        
        HTTPResponse::JSON($ret);             
    } else if ($is_insert_from_worksheet){
        $table_name = $form_clear['table_name'];
        $columns_name = implode(',',$form_clear['columns_name']);
        $question = implode(',', array_fill(0, count($form_clear['columns_name']), '?'));
        $query = "INSERT INTO $table_name ($columns_name) VALUES ($question)";
        $params = $form_clear['values'];
        //$ret = array('query'=> $query, 'columns_name'=> $columns_name, 'params' => $params);
        try{
            $ret = Database::execute($query, $params);
            $query = "SELECT * FROM $table_name WHERE id = (SELECT max(id) FROM $table_name)";
            $params = array();
            $ret = Database::fetch($query, $params);
        } catch(Exception $e){
            $ret = array('error'=>$e, 'query'=>$query);
        }        
        HTTPResponse::JSON($ret);
    }



    if (!isset($form_clear['uid'])){
        $last_user_selected = NULL;
        $_SESSION['last_user_selected'] = NULL;
    } else if ($is_changed_user){
        $query = "SELECT * FROM user WHERE id = ?";
        $params = array($form_clear['uid']);
        $search_user_values = Database::fetchAll($query, $params);
        if (count($search_user_values) == 1) {
            $_SESSION['last_user_selected'] = $search_user_values[0];
        } else {
            $_SESSION['last_user_selected'] = NULL;        
        }
        $last_user_selected = $_SESSION['last_user_selected'];
    }

    $current_user_id = @$last_user_selected['id'];

    $query_units = 1;


    $selected_user_loans = NULL;



    if ($is_loaning){
        header('Content-Type: text/plain');    
        $user_id = @$form_clear['uid'];
        $model_id = @$form_clear['iid'];
        $patrimony_id = @$form_clear['pid'] ;
        if ($user_id < 0 || $model_id < 0 || $patrimony_id < 0){
            HTTPResponse::forbidden('A requisição é inválida.');
        }
        $original_count = @$form_clear['units'] > 0 ? @$form_clear['units'] : 1;   
        
        // *** Impedir empréstimo quando já estiver emprestado: ***
        $protection_query = "SELECT original_count AS quantidade_emprestada,
                                original_count - sum(diff) as devolvidos,
                                sum(diff) as restantes,
                                quantity - sum(diff) as quantidades_possiveis_para_emprestimo,
                                quantity,
                                quantity_strict,
                                n.id
                                FROM model m
                                INNER JOIN loan n ON (m.id = n.model_id)
                                INNER JOIN patrimony p ON (p.id = n.patrimony_id AND m.id = p.model_id)
                                INNER JOIN log_loan nn ON (nn.loan_id = n.id )
                                WHERE m.id = ? AND p.id = ? AND has_patrimony = 1
                                GROUP BY m.id
                                HAVING n.id = max(n.id) OR n.id IS NULL
                            UNION SELECT original_count AS quantidade_emprestada,
                                original_count - sum(diff) as devolvidos,
                                sum(diff) as restantes,
                                quantity - sum(diff) as quantidades_possiveis_para_emprestimo,
                                quantity,
                                quantity_strict,
                                n.id
                                FROM model m
                                INNER JOIN loan n ON (m.id = n.model_id)
                                INNER JOIN log_loan nn ON (nn.loan_id = n.id )
                                WHERE m.id = ? AND has_patrimony = 0
                                GROUP BY m.id
                                HAVING n.id = max(n.id) OR n.id IS NULL
                            ";
        $params = array($model_id, $patrimony_id, $model_id);
        $result = Database::fetch($protection_query,$params);
        
        if (@$result['has_patrimony'] == 1){ 
            if ($result['devolvidos'] < $result['quantidade_emprestada']){
               HTTPResponse::forbidden("Este item já foi emprestado e não foi devolvido.");
            }
        } else if (@$result['quantity_strict'] == 1){   
            if ($original_count > $result['quantidades_possiveis_para_emprestimo']){
                HTTPResponse::forbidden("Não há quantidades suficientes deste item para empréstimo.");
            }
        }
        // *******

        
        $query = "INSERT INTO loan (user_id, model_id, patrimony_id, original_count) VALUES (?,?,?,?)";
        $params = array($user_id, $model_id, $patrimony_id, $original_count);
    
        Database::execute($query, $params);
        $query = "SELECT max(id) FROM loan";
        $loan_id = Database::fetchOne($query, array());
        $query = "INSERT INTO log_loan (loan_id, diff) VALUES (?,?)";
        $params = array($loan_id, $original_count);
        Database::execute($query, $params);
        $redirect_url = http_build_query(array(
            'uid' => $current_user_id,
            'q'=> $current_query_string,
            't' => $current_query_type_string,
            'before' => $current_date_before,
            'after' => $current_date_after,
            'act' => 'success_loan'
        ));
        HTTPResponse::redirect("?$redirect_url");
    } 
    if ($is_returning_item){
        if (!isset($form_clear['nid'])){
            HTTPResponse::forbidden("Ação inválida.");
        }
        $loan_id = $form_clear['nid'];    
        $diff = isset($form_clear['diff']) ? $form_clear['diff'] * 1 : -1;

        // *** Impedir devolução quando já estiver concluído: ***
        $protection_query = "SELECT original_count, 
                                sum(diff) as count_remaining, 
                                n.id AS last_loan_id,
                                has_patrimony,
                                patrimony_id,
                                m.id as iid
                                FROM loan n 
                                INNER JOIN model m ON (m.id = n.model_id)
                                LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
                                WHERE n.id = ?
                                GROUP BY n.id
                                HAVING n.id = max(n.id) OR n.id IS NULL";
        $params = array($loan_id);
        $result = Database::fetch($protection_query,$params);
        if ($diff < $result['count_remaining'] * -1){
            HTTPResponse::forbidden("Este item já foi devolvido.");
        }
        // *** Impedir devolução quando o "dever" fica maior que o que "foi pego": ***
        if ($result['count_remaining'] == $result['original_count'] && $diff > 0 ){
            HTTPResponse::forbidden("Valor menor que zero.");
        }
        if ($result['has_patrimony']){
            // *** Impedir movimentação quando já tiver outro posterior: ***
            $protection_query = "SELECT max(id) FROM loan n WHERE patrimony_id = ?";
            $last_loan_id = Database::fetchOne($protection_query,array($result['patrimony_id']));
            if ($diff > 0 && $loan_id != $last_loan_id){
                HTTPResponse::forbidden("Ação indisponível. Um outro empréstimo posterior deste mesmo item já foi registrado.");
            }
        }

        // *******

        $model_id = $result['iid'];
        $details = @$form_clear['details'];
        $query = "INSERT INTO log_loan (loan_id, diff, details) VALUES (?,?,?)";
        $params = array($loan_id, $diff, $details);
        Database::execute($query, $params);

        $query_additional_info = "SELECT  sum(diff) as total_loan_diff
                                FROM model m
                                INNER JOIN loan n ON (m.id = n.model_id)
                                LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
                                WHERE m.id = ? ";
        $additional_info = Database::fetch($query_additional_info, array($model_id));
        $response = array(
            'uid' => $current_user_id,
            'q'=> $current_query_string,
            't' => $current_query_type_string,
            'before' => $current_date_before,
            'after' => $current_date_after,
            'act' => 'success_returning',
            'redirect_to' => @$form_clear['redirect_to'],
            'nid' => $loan_id,
            'iid' => $model_id,
            'diff' => $diff,
            'original_count' => $result['original_count'],
            'details' => $details,
            'total_loan_diff' => $additional_info['total_loan_diff']
        );  
        if (!$response_json){
            header('Content-Type: text/plain');    
            $redirect_url = http_build_query($response);
            HTTPResponse::redirect("?$redirect_url");
        }
        
    }


    if ($response_json){
        include 'json.php';
    }

    $page_title = $last_user_selected ? $last_user_selected['name'] : 'Empréstimos';
    if ($is_inventory){
        $page_title = 'Inventário';
    }
}