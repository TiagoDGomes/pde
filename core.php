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
if ($action_reset_user) {
    $_SESSION['selected_user'] = NULL;
    exit(header('Location: .'));

} else if ($action_save_new_user) {
    $query = "INSERT INTO user (name, code1, code2) VALUES (?,?,?)";
    $params = array(strtoupper($_POST['name']), strtoupper($_POST['code1']), strtoupper($_POST['code2']));
    Database::execute($query, $params);
    $action_search_user = TRUE;

} else if ($action_save_edit_user) {
    $query = "UPDATE user SET name = ?, code1 = ?, code2 = ? WHERE id = ?";
    $params = array(strtoupper($_POST['name']), strtoupper($_POST['code1']), strtoupper($_POST['code2']), $_POST['id']);
    Database::execute($query, $params);
    $action_select_user = TRUE;
    $_GET['user'] = $_POST['id'];

} else if ($action_save_item){
    if (!isset($_POST['model_id']) || $_POST['model_id'] == ''){
        $query = "INSERT INTO model (name, code, has_patrimony) VALUES (?,?,?)";
        $params = array($_POST['model_name'], 
                        strtoupper($_POST['model_code']), 
                        $_POST['model_unique'] == "1"? 1 : 0
                  );
                     
    } else {        
        $query = "UPDATE model SET name = ?, code = ?, has_patrimony = ? WHERE id = ?";
        $params = array($_POST['model_name'], 
                    strtoupper($_POST['model_code']), 
                    $_POST['model_unique'] == "1"? 1 : 0,
                    $_POST['model_id']
                );
        
    }    
    Database::execute($query, $params);
    $action_search_code = TRUE;
    $_GET['code'] = $_POST['model_code'];
    if ($_POST['model_unique'] == "1"){
        $query = "SELECT max(id) FROM model";
        $model_id = $_POST['model_id'] ? $_POST['model_id'] : Database::fetchOne($query, array());
        $patrs = explode("\n", htmlspecialchars(@$_POST['unique_codes']));
        header("Content-Type: text/plain");
        foreach($patrs as $p){
            $query = "INSERT INTO patrimony (model_id, num) VALUES (?,?)";
            $params = array($model_id, $p);
            var_dump($query);
            var_dump($params);            
            Database::execute($query, $params);
        }
    }

} else if ($action_loan_new_item){
    $loan_diff = $_POST['loan_diff'] > 0 ? $_POST['loan_diff'] : 1;
    $query = "INSERT INTO loan (user_id, model_id, patrimony_id) VALUES (?,?,?)";
    $params = array($_POST['user_id'], $_POST['model_id'], $_POST['patrimony_id']);
    Database::execute($query, $params);
    $query = "SELECT max(id) FROM loan";
    $loan_id = Database::fetchOne($query, array());
    $query = "INSERT INTO log_loan (load_id, diff) VALUES (?,?)";
    $params = array($loan_id, $loan_diff);
    Database::execute($query, $params);
    $action_search_user = TRUE;
}



if ($action_search_user) {
    $su = strtoupper(htmlspecialchars($_GET['search_user']));
    $query = "SELECT * FROM user WHERE name LIKE ? OR code1 = ? OR code2 = ?";
    $params = array("%$su%", $su, $su);
    $search_user_values = Database::fetchAll($query, $params);
    if (count($search_user_values) == 1) {
        $_SESSION['selected_user'] = $search_user_values[0];
        exit(header('Location: .'));
    }

} else if ($action_search_code) {
    $code_search = explode("*",htmlspecialchars($_GET['code']));
    $code = $code_search[0];
    $loan_multiplier = @$code_search[1] * 1;
    $query = "SELECT m.id as model_id, 
                        m.name AS model_name, 
                        m.code AS model_code, 
                        has_patrimony, 
                        num, p.id AS patrimony_id 
                FROM model m  
                RIGHT JOIN patrimony p ON (model_id = m.id)
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
    $params = array($_GET['user']);
    $search_user_values = Database::fetchAll($query, $params);
    if (count($search_user_values) == 1) {
        $_SESSION['selected_user'] = $search_user_values[0];
        exit(header('Location: .'));
    }
}
