<?php

session_start();

require_once 'config.php';
require_once 'classes/Filter.php';
require_once 'classes/Database.php';
require_once 'classes/HTTPResponse.php';
require_once 'classes/HTMLUtil.php';
include_once 'include/fatal_error.php';

Database::startInstance($CONFIG_PDO_CONN, $CONFIG_PDO_USER, $CONFIG_PDO_PASS);

$form_clear = array();
foreach($_GET as $key => $value){
    $form_clear[$key] = @htmlspecialchars(trim($value));
}
foreach($_POST as $key => $value){
    $form_clear[$key] = @htmlspecialchars(trim($value));
}

$search_results = array();


$current_query_string = @$form_clear['q'];
$current_query_type_string = @$form_clear['t'];

$is_search_type_item = @$form_clear['t'] == 'item';
$is_search_type_user = !$is_search_type_item;



$is_searching = isset($_GET['q']) && $current_query_string != '';
$is_selecting_user = isset($_GET['uid']) && @$form_clear['uid'] != '';

$is_returning_item = isset($_GET['nid']) && @$_GET['act'] == 'ret';

$option_search_user_checked = (!isset($form_clear['t'])) || 
                              (@$form_clear['t'] == 'user') ? 
                              'checked' : '';

$option_search_item_checked = (@$form_clear['t'] == 'item') || 
                               ( @$form_clear['t'] != 'user' && isset($form_clear['uid']) ) ? 
                               'checked' : '';

$last_user_selected = @$_SESSION['last_user_selected'];                            
$is_changed_user = @$form_clear['uid'] != '' &&((is_null($last_user_selected)) || $last_user_selected['id'] != $form_clear['uid']);

$current_date_now = (new DateTimeImmutable())->format('Y-m-d');

$search_one_item = FALSE;

if (isset($_SESSION['default_date_after'])){
    $default_date_after = $_SESSION['default_date_after'];
}  else {
    $default_date_after = $current_date_now;
    $_SESSION['default_date_after'] = $default_date_after;
} 

if (isset($_SESSION['default_date_before'])){
    $default_date_before = $_SESSION['default_date_before'];
}  else {
    $default_date_before = (new DateTimeImmutable("-6 month"))->format('Y-m-d');
    $_SESSION['default_date_before'] = $default_date_before;
}                             
                 
$current_date_after = isset($form_clear['after']) ? 
                        (new DateTimeImmutable($form_clear['after']))->format('Y-m-d') :
                            $default_date_after;

$current_date_before = isset($form_clear['before']) ? 
                        (new DateTimeImmutable($form_clear['before']))->format('Y-m-d') :
                        $default_date_before;


$is_loaning = @$form_clear['act'] == 'loan';


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

if ($is_searching){
    $params = array();    
    if ($is_search_type_user){        
        $su = strtoupper($current_query_string);
        $query = "SELECT id, name, 
                    code1, code2,
                    0 as has_patrimony, 
                    'user' as result_type,
                    code1 || '<br>' || code2 as obs 
                  FROM user WHERE normalize(name) LIKE ? OR code1 = ? OR code2 = ?";
        $params = array(normalize("%$su%"), $su, $su);  
    } else {
        $query_string_full = explode("*", $current_query_string);
        $query_string = normalize($query_string_full[0]);
        if (isset($query_string_full[1])){
            if (!is_numeric($query_string_full[1]) || ($query_string_full[0] < $query_string_full[1])){
                $query_units = $query_string_full[0] > 0 ? $query_string_full[0] : 1;
                $query_string = normalize(@$query_string_full[1]);
            } else {                
                $query_units = $query_string_full[1];                               
            }
        } else {
            $query_units = 1;
        }
        $query = "SELECT m.id as model_id, 
                            m.name AS name, 
                            m.code AS model_code, 
                            has_patrimony, 
                            number1 as patrimony_number1,
                            number2 as patrimony_number2,                         
                            serial_number as patrimony_serial_number,
                            p.id AS patrimony_id,
                            sum(nn.diff) as loan_diff,
                            p.obs as obs,
                            max(n.id) as last_loan_id,
                            u.name AS last_user_name,
                            u.id AS last_user_id,                            
                            'patrimony' as result_type,
                            1 as query_units,
                            CASE WHEN number1 = ? THEN 1
                                 WHEN number2 = ? THEN 1
                                 WHEN serial_number = ? THEN 1
                                 ELSE 0 END AS is_match
                    FROM model m  
                    LEFT JOIN patrimony p ON (p.model_id = m.id)
                    LEFT JOIN loan n ON (n.patrimony_id = p.id)
                    LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
                    LEFT JOIN user u ON (n.user_id = u.id)
                    WHERE has_patrimony = 1 
                        AND 
                            (m.code = ?                     
                            OR p.number1 = ? 
                            OR p.number2 = ? 
                            OR p.serial_number = ? 
                            OR normalize(m.name) LIKE ?)               
                    GROUP BY p.id
                    UNION "; 
        $query .= "SELECT m.id as model_id, 
                            m.name AS name, 
                            m.code AS model_code, 
                            has_patrimony, 
                            NULL as patrimony_number1,
                            NULL as patrimony_number2,                         
                            NULL as patrimony_serial_number,
                            NULL AS patrimony_id ,
                            0 as loan_diff ,
                            NULL as obs,                            
                            0 as last_loan_id,
                            NULL AS last_user_name,
                            NULL AS last_user_id,                            
                            'item' as result_type,
                            ? AS query_units,
                            0 as is_match
                    FROM model m  
                    WHERE has_patrimony = 0 
                        AND 
                            (m.code = ?   
                            OR normalize(m.name) LIKE ?)  
                    " ;  
        $query .= "ORDER BY is_match DESC, has_patrimony DESC, patrimony_number1, patrimony_number2, name,  patrimony_serial_number";
        $params = array(
            $query_string, $query_string, $query_string,
            strtoupper($query_string),strtoupper($query_string),strtoupper($query_string),strtoupper($query_string), "%$query_string%",
            $query_units,strtoupper($query_string), "%$query_string%"
            );
    }
    $search_results = Database::fetchAll($query, $params);
    if (count($search_results) == 1) {
        $search_one_item = TRUE;
        //HTTPResponse::redirect('?act=select&uid=' . $_SESSION['selected_user']['id']);
    }
}

$selected_user_loans = NULL;

if ($is_selecting_user){
    $query_search_user_loans = "SELECT m.id as model_id, 
                     m.name as model_name,
                     m.code as model_code, 
                     p.id as patrimony_id, 
                     n.tstamp as loan_date, 
                     p.number1 as patrimony_number, 
                     p.number2 as patrimony_number2, 
                     p.serial_number as patrimony_serial_number, 
                     original_count,
                     original_count - sum(diff) as count_remaining, 
                     group_concat(details, '<br>') as all_details ,
                     n.id as loan_id
                FROM loan n
                INNER JOIN model m ON (n.model_id = m.id)
                INNER JOIN log_loan nn ON (nn.loan_id = n.id)
                LEFT JOIN patrimony p ON (n.patrimony_id = p.id)
                WHERE n.user_id =  ? AND n.tstamp BETWEEN ? AND ?
                GROUP BY n.id
                ORDER BY n.tstamp DESC
                ";
    $current_date_after_1day =  (new DateTimeImmutable($current_date_after . ' +1 day'))->format('Y-m-d');     
    
    $params = array($form_clear['uid'], $current_date_before, $current_date_after_1day);
    $selected_user_loans = Database::fetchAll($query_search_user_loans, $params);
}

if ($is_loaning){
    header('Content-Type: text/plain');    
    $user_id = @$form_clear['uid'];
    $model_id = @$form_clear['iid'];
    $patrimony_id = @$form_clear['pid'] ;
    if ($user_id < 0 || $model_id < 0 || $patrimony_id < 0){
        HTTPResponse::forbidden('A requisição é inválida.');
    }
    $original_count = @$form_clear['units'] > 0 ? @$form_clear['units'] : 1;    
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
    header('Content-Type: text/plain');
    $loan_id = $form_clear['nid'];
    $diff = isset($form_clear['diff']) ? $form_clear['diff'] * 1 : -1;

    $query = "INSERT INTO log_loan (loan_id, diff) VALUES (?,?)";
    $params = array($loan_id, $diff);
    Database::execute($query, $params);
    $redirect_url = http_build_query(array(
        'uid' => $current_user_id,
        'q'=> $current_query_string,
        't' => $current_query_type_string,
        'before' => $current_date_before,
        'after' => $current_date_after,
        'act' => 'success_returning',
        'nid' => $loan_id
    ));
    var_dump($query);
    var_dump($params);
    //exit();
    HTTPResponse::redirect("?$redirect_url");
}

$search_query_focus = (!$search_one_item || isset($form_clear['act']));
$selected_one_item = !$search_query_focus;