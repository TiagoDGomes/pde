<?php
session_start();

require_once 'config.php';
require_once 'database.php';
require_once 'include/form.generic.php';
require_once 'include/form.user.php';
require_once 'include/form.model.php';

Database::startInstance($CONFIG_PDO_CONN, $CONFIG_PDO_USER, $CONFIG_PDO_PASS);

$selected_user = NULL;

$action_search_user = isset($_GET['search_user']);
$action_search_code = isset($_GET['code']) && @$_GET['code'] != '';
$action_select_user = isset($_GET['user']);
$action_reset_user = isset($_GET['reset']);
$action_save_new_user = isset($_POST['save_new_user']);
$action_save_edit_user = isset($_POST['save_edit_user']);
$action_save_new_model = isset($_POST['save_new_model']);
$action_loan_new_item = isset($_POST['loan_new_item']);
$action_log_loan = isset($_GET['log_loan']);
$search_user_values = array();
$search_items_values = array();
$loan_multiplier = 1;
$search_one_item = FALSE;

$get_clear = array();
$post_clear = array();

foreach($_GET as $key => $value){
    $get_clear[$key] = htmlspecialchars(trim($value));
}
foreach($_POST as $key => $value){
    $post_clear[$key] = htmlspecialchars(trim($value));
}


if ($action_reset_user) {
    $_SESSION['selected_user'] = NULL;
    exit(header('Location: .'));

} else if ($action_save_new_user) {
    form_user_save_new($post_clear);
    exit(header('Location: .'));

} else if ($action_save_edit_user) {
    form_user_save_edit($post_clear);
    exit(header('Location: .'));

} else if ($action_save_new_model){
    form_model_save($post_clear);
    exit(header("Location: ?reg_item=y&code=" . $post_clear['model_code']));

} else if ($action_loan_new_item){
    $original_count = $post_clear['original_count'] > 0 ? $post_clear['original_count'] : 1;
    $query = "INSERT INTO loan (user_id, model_id, patrimony_id, original_count) VALUES (?,?,?,?)";
    $params = array($post_clear['user_id'], $post_clear['model_id'], $post_clear['patrimony_id'], $original_count);
    Database::execute($query, $params);
    $query = "SELECT max(id) FROM loan";
    $loan_id = Database::fetchOne($query, array());
    $query = "INSERT INTO log_loan (loan_id, diff) VALUES (?,?)";
    $params = array($loan_id, $original_count);
    Database::execute($query, $params);
    exit(header("Location: ?loan_new_item=y&code=" . $post_clear['code']));
} else if ($action_log_loan){
    $loan_id = $get_clear['loan_id'];
    $diff = $get_clear['diff'] * -1;
    $query = "INSERT INTO log_loan (loan_id, diff) VALUES (?,?)";
    $params = array($loan_id, $diff);
    Database::execute($query, $params);
    exit(header("Location: ?&redirect_log_loan=y&code=" . @$get_clear['code']));
}



if ($action_search_user) {
    $su = strtoupper(@$get_clear['search_user']);
    $query = "SELECT * FROM user WHERE name LIKE ? OR code1 = ? OR code2 = ?";
    $params = array("%$su%", $su, $su);
    $search_user_values = Database::fetchAll($query, $params);
    if (count($search_user_values) == 1) {
        $_SESSION['selected_user'] = $search_user_values[0];
        exit(header('Location: .'));
    }

} else if ($action_search_code) {
    $code_search = explode("*",$get_clear['code']);
    $code = $code_search[0];
    if ($code == ''){
        // nope
    } else {
        if (isset($code_search[1]) && $code_search[1] != ''){
            $loan_multiplier = @$code_search[1] * 1;
        } else {
            $loan_multiplier = 1;
        }
        
        if ($loan_multiplier < 1){
            $loan_multiplier = 1;
        }  
        $query = "";  
        $query .= "SELECT m.id as model_id, 
                            m.name AS model_name, 
                            m.code AS model_code, 
                            has_patrimony, 
                            number1 as patrimony_number1,
                            number2 as patrimony_number2,                         
                            serial_number as patrimony_serial_number,
                            p.id AS patrimony_id,
                            sum(nn.diff) as loan_diff
                    FROM model m  
                    LEFT JOIN patrimony p ON (p.model_id = m.id)
                    LEFT JOIN loan n ON (n.patrimony_id = p.id)
                    LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
                    WHERE has_patrimony = 1 
                        AND 
                            (model_code = ?                     
                            OR patrimony_number1 = ? 
                            OR patrimony_number2 = ? 
                            OR serial_number = ? 
                            OR name LIKE ?)                    
                    
                    UNION "; 
        $query .= "SELECT m.id as model_id, 
                            m.name AS model_name, 
                            m.code AS model_code, 
                            has_patrimony, 
                            NULL as patrimony_number1,
                            NULL as patrimony_number2,                         
                            NULL as patrimony_serial_number,
                            NULL AS patrimony_id ,
                            0 as loan_diff 
                    FROM model m  
                    WHERE has_patrimony = 0 
                        AND 
                            (model_code = ?   
                            OR name LIKE ?)
                    " ;  
        $query .= "ORDER BY has_patrimony DESC, m.name, number1";
        
        $params = array(
                    strtoupper($code),strtoupper($code),strtoupper($code),strtoupper($code), "%$code%",
                    strtoupper($code), "%$code%"
            );
        $search_items_values = Database::fetchAll($query, $params);
        if (count($search_items_values) == 1){
            $search_one_item = TRUE;
        }
    }
    
}

if ($action_select_user) {
    $query = "SELECT * FROM user WHERE id = ?";
    $params = array($get_clear['user']);
    $search_user_values = Database::fetchAll($query, $params);
    if (count($search_user_values) == 1) {
        $_SESSION['selected_user'] = $search_user_values[0];
        exit(header('Location: .'));
    }
}
$selected_user = @$_SESSION['selected_user'];

if ($selected_user){
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
    $params = array($selected_user['id']);
    $search_user_loans = Database::fetchAll($query_search_user_loans, $params);
}