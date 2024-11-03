<?php
isset($PDE) or die('Nope');
session_start();

require_once 'config.php';
require_once 'classes/Filter.php';
require_once 'classes/Database.php';
require_once 'classes/HTTPResponse.php';
require_once 'classes/HTMLUtil.php';

Database::startInstance($CONFIG_PDO_CONN, $CONFIG_PDO_USER, $CONFIG_PDO_PASS);

$form_clear = array();
foreach($_GET as $key => $value){
    $form_clear[$key] = @htmlspecialchars(trim($value));
}
foreach($_POST as $key => $value){
    $form_clear[$key] = @htmlspecialchars(trim($value));
}

$search_results = array();

$search_query_focus = FALSE;

$search_one_item = FALSE;

$current_query_string = @$form_clear['q'];
$current_query_type_string = @$form_clear['t'];

$is_search_type_item = @$form_clear['t'] == 'item';
$is_search_type_user = !$is_search_type_item;

$is_show_patrimony = isset($_GET['pid']) && @$_GET['pid'] != '';

$is_show_item = isset($_GET['iid']) & @$_GET['iid'] != '';

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
    if (!isset($form_clear['nid'])||!isset($form_clear['iid'])){
        HTTPResponse::forbidden("Ação inválida.");
    }
    $model_id = $form_clear['iid'];
    $loan_id = $form_clear['nid'];    
    $diff = isset($form_clear['diff']) ? $form_clear['diff'] * 1 : -1;

    // *** Impedir devolução quando já estiver concluído: ***
    $protection_query = "SELECT original_count, 
                            sum(diff) as count_remaining
                            FROM loan n 
                            INNER JOIN model m ON (m.id = n.model_id)
                            LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
                            WHERE n.id = ?
                            GROUP BY n.id";
    $params = array($loan_id);
    $result = Database::fetch($protection_query,$params);
    if ($diff < $result['count_remaining'] * -1){
        HTTPResponse::forbidden("Este empréstimo já foi devolvido.");
    }
    // *******


    $query = "INSERT INTO log_loan (loan_id, diff) VALUES (?,?)";
    $params = array($loan_id, $diff);
    Database::execute($query, $params);
    $model_id = @$form_clear['iid'];
    $redirect_url = http_build_query(array(
        'uid' => $current_user_id,
        'q'=> $current_query_string,
        't' => $current_query_type_string,
        'before' => $current_date_before,
        'after' => $current_date_after,
        'act' => 'success_returning',
        'nid' => $loan_id,
        'iid' => $model_id,
    ));
    HTTPResponse::redirect("?$redirect_url");
}


