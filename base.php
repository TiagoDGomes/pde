<?php

session_start();

require_once 'config.php';
require_once 'classes/Filter.php';
require_once 'classes/Database.php';
require_once 'classes/HTTPResponse.php';
require_once 'classes/HTMLUtil.php';
require_once 'include/form.generic.php';
require_once 'include/form.user.php';
require_once 'include/form.model.php';

Database::startInstance($CONFIG_PDO_CONN, $CONFIG_PDO_USER, $CONFIG_PDO_PASS);

$form_clear = array();
foreach($_GET as $key => $value){
    $form_clear[$key] = @htmlspecialchars(trim($value));
}
foreach($_POST as $key => $value){
    $form_clear[$key] = @htmlspecialchars(trim($value));
}

$search_results = array();

$is_search_type_item = @$form_clear['t'] == 'item';
$is_search_type_user = !$is_search_type_item;

$is_searching = isset($_GET['q']) && @$form_clear['q'] != '';
$is_selecting_user = isset($_GET['uid']) && @$form_clear['uid'] != '';

$option_search_user_checked = (!isset($form_clear['t'])) || 
                              (@$form_clear['t'] == 'user') ? 
                              'checked' : '';

$option_search_item_checked = (@$form_clear['t'] == 'item') || 
                               ( @$form_clear['t'] != 'user' && isset($form_clear['uid']) ) ? 
                               'checked' : '';

$last_user_selected = @$_SESSION['last_user_selected'];                            
$is_changed_user = @$form_clear['uid'] != '' &&((is_null($last_user_selected)) || $last_user_selected['id'] != $form_clear['uid']);

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
        $su = strtoupper($form_clear['q']);
        $query = "SELECT id, name, 
                    code1, code2,
                    0 as has_patrimony, 
                    'user' as result_type,
                    code1 || '<br>' || code2 as obs 
                  FROM user WHERE normalize(name) LIKE ? OR code1 = ? OR code2 = ?";
        $params = array(normalize("%$su%"), $su, $su);    
        
        
        
    } else {
        $query_string_full = explode("*", $form_clear['q']);
        $query_string = normalize($query_string_full[0]);
        $query_units = is_numeric(@$query_string_full[1]) ?  $query_string_full[1] : 1;
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
                            1 as query_units
                    FROM model m  
                    LEFT JOIN patrimony p ON (p.model_id = m.id)
                    LEFT JOIN loan n ON (n.patrimony_id = p.id)
                    LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
                    LEFT JOIN user u ON (n.user_id = u.id)
                    WHERE has_patrimony = 1 
                        AND 
                            (model_code = ?                     
                            OR patrimony_number1 = ? 
                            OR patrimony_number2 = ? 
                            OR serial_number = ? 
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
                            ? as query_units
                    FROM model m  
                    WHERE has_patrimony = 0 
                        AND 
                            (model_code = ?   
                            OR normalize(m.name) LIKE ?)  
                    " ;  
        $query .= "ORDER BY has_patrimony DESC, m.name, patrimony_number1, patrimony_number2, patrimony_serial_number";
        $params = array(
            strtoupper($query_string),strtoupper($query_string),strtoupper($query_string),strtoupper($query_string), "%$query_string%",
            $query_units,strtoupper($query_string), "%$query_string%"
            );
    }
    $search_results = Database::fetchAll($query, $params);
    if (count($search_results) == 1) {
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
                WHERE n.user_id =  ?
                GROUP BY n.id
                ORDER BY n.tstamp DESC
                ";
    $params = array($form_clear['uid']);
    $selected_user_loans = Database::fetchAll($query_search_user_loans, $params);
}
