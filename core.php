<?php
session_start();

require_once 'config.php';
require_once 'database.php';

Database::startInstance($CONFIG_PDO_CONN, $CONFIG_PDO_USER, $CONFIG_PDO_PASS);

$action_search_user = isset($_GET['search_user']);
$action_search_code = isset($_GET['code']);
$action_select_user = isset($_GET['user']);
$action_reset_user = isset($_GET['reset']);
$action_save_new_user = isset($_POST['save_new_user']);
$action_save_edit_user = isset($_POST['save_edit_user']);
$action_save_item = isset($_POST['save_item']);
$action_loan_new_item = isset($_POST['loan_new_item']);
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
    $query = "INSERT INTO user (name, code1, code2) VALUES (?,?,?)";
    $params = array(strtoupper($post_clear['name']), strtoupper($post_clear['code1']), strtoupper($post_clear['code2']));
    Database::execute($query, $params);
    $action_search_user = TRUE;

} else if ($action_save_edit_user) {
    $query = "UPDATE user SET name = ?, code1 = ?, code2 = ? WHERE id = ?";
    $params = array(strtoupper($post_clear['name']), strtoupper($post_clear['code1']), strtoupper($post_clear['code2']), $post_clear['id']);
    Database::execute($query, $params);
    $action_select_user = TRUE;
    $get_clear['user'] = $post_clear['id'];

} else if ($action_save_item){
    if (!isset($_POST['model_id']) || $post_clear['model_id'] == ''){
        $query = "INSERT INTO model (name, code, has_patrimony) VALUES (?,?,?)";
        $params = array($post_clear['model_name'], 
                        strtoupper($post_clear['model_code']), 
                        $post_clear['model_unique'] == "1"? 1 : 0
                  );
                     
    } else {        
        $query = "UPDATE model SET name = ?, code = ?, has_patrimony = ? WHERE id = ?";
        $params = array($post_clear['model_name'], 
                    strtoupper($post_clear['model_code']), 
                    $post_clear['model_unique'] == "1"? 1 : 0,
                    $post_clear['model_id']
                );
        
    }    
    Database::execute($query, $params);
    $action_search_code = TRUE;
    $get_clear['code'] = $post_clear['model_code'];
    if ($post_clear['model_unique'] == "1"){
        $query = "SELECT max(id) FROM model";
        $model_id = $post_clear['model_id'] ? $post_clear['model_id'] : Database::fetchOne($query, array());
        $patrs = explode("\n", @$post_clear['unique_codes']);
        header("Content-Type: text/plain");
        foreach($patrs as $p){
            $query = "INSERT INTO patrimony (model_id, num) VALUES (?,?)";
            $params = array($model_id, $p);
            var_dump($query);
            var_dump($params);            
            Database::execute($query, $params);
        }
    }
    exit(header("Location: ?reg_item=y&code=" . $post_clear['model_code']));

} else if ($action_loan_new_item){
    $loan_diff = $post_clear['loan_diff'] > 0 ? $post_clear['loan_diff'] : 1;
    $query = "INSERT INTO loan (user_id, model_id, patrimony_id) VALUES (?,?,?)";
    $params = array($post_clear['user_id'], $post_clear['model_id'], $post_clear['patrimony_id']);
    Database::execute($query, $params);
    $query = "SELECT max(id) FROM loan";
    $loan_id = Database::fetchOne($query, array());
    $query = "INSERT INTO log_loan (loan_id, diff) VALUES (?,?)";
    $params = array($loan_id, $loan_diff);
    Database::execute($query, $params);
    $action_search_user = TRUE;
}



if ($action_search_user) {
    $su = strtoupper($get_clear['search_user']);
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
    $loan_multiplier = @$code_search[1] * 1;
    $query = "SELECT m.id as model_id, 
                        m.name AS model_name, 
                        m.code AS model_code, 
                        has_patrimony, 
                        num, p.id AS patrimony_id 
                FROM model m  
                LEFT JOIN patrimony p ON (model_id = m.id)
                WHERE code = ? OR name LIKE ?
                ";
    $params = array(strtoupper($code), "%$code%");
    $search_items_values = Database::fetchAll($query, $params);
    if (count($search_items_values) == 1){
        $search_one_item = TRUE;
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


if ($_SESSION['selected_user']){
    $query = "SELECT m.id as model_id, m.name as model_name, 
                    p.id as patrimony_id, p.num as patrimony_number, sum(diff) as count_remaining 
                FROM model m
                INNER JOIN patrimony p ON (p.model_id = m.id)
                INNER JOIN loan n ON (n.model_id = m.id)
                LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
                WHERE n.user_id = ?
                ";
    $params = array($_SESSION['selected_user']['id']);
    $search_user_loans = Database::fetchAll($query, $params);
}