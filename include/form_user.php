<?php isset($PDE) or die('Nope');
require_once 'include/form_generic.php';
function form_user($user){ 
    form_generator(
        "Solicitante",
        "user",
        $user,
        array(                                
            array(
                "name" => "name",
                "type" => "text",
                "required" => "required",
                "value" => @$user['name'],
                "data-description" => "Nome",  
                "placeholder" => "Nome",  
            ),                        
            array(
                "name" => "code1",
                "type" => "text",
                "required" => "required",
                "class" => "small",
                "value" => @$user['code1'],
                "data-description" => "Identificador adicional 1",
                "placeholder" => "Identificador adicional 1",  
            ) ,                        
            array(
                "name" => "code2",
                "type" => "text",
                "class" => "small",
                "value" => @$user['code2'],
                "data-description" => "Identificador adicional 2",  
                "placeholder" => "Identificador adicional 2",  
            ),                        
            array(
                "name" => "login",
                "type" => "text",
                "class" => "small",
                "value" => @$user['login'],
                "data-description" => "Login", 
                "data-help" => "Se login for definido, permite a autenticação no sistema",    
                "placeholder" => "",  
            ),                        
            array(
                "name" => "change-password-1",
                "type" => "password",
                "value" => '',
                "class" => "small",
                "data-description" => "Senha", 
                "placeholder" => "",  
            ),                        
            array(
                "name" => "change-password-2",
                "type" => "password",
                "value" => '',
                "class" => "small",
                "data-description" => "Repita a senha",  
                "placeholder" => "",  
            )
        )                        
    ) ;
}



function form_user_save_new($form_clear){
    global $last_user_selected;
    
    if ($form_clear['change-password-1'] != $form_clear['change-password-2']){
        $query = "INSERT INTO user (name, code1, code2) VALUES (?,?,?)";
        $params = array(strtoupper($form_clear['name']), strtoupper($form_clear['code1']), strtoupper($form_clear['code2']));
    } else {
        $pwd_hashed = pde_password_hash($form_clear['change-password-1']);
        $query = "INSERT INTO user (name, code1, code2, login, password) VALUES (?,?,?,?,?)";
        $params = array(strtoupper($form_clear['name']), strtoupper($form_clear['code1']), strtoupper($form_clear['code2']), $form_clear['login'], $pwd_hashed);
    }    
    Database::execute($query, $params);
    $_SESSION['last_user_selected'] = Database::fetch("SELECT id, name, code1, code2, max(id) FROM user ORDER BY id DESC", array());
    $last_user_selected = $_SESSION['last_user_selected'];
}

function form_user_save_edit($form_clear){
    global $last_user_selected;
    if ($form_clear['change-password-1'] != $form_clear['change-password-2']){
        $query = "UPDATE user SET name = ?, code1 = ?, code2 = ? WHERE id = ?";
        $params = array(strtoupper($form_clear['name']), strtoupper($form_clear['code1']), strtoupper($form_clear['code2']), $form_clear['id']);
    } else {
        $pwd_hashed = pde_password_hash($form_clear['change-password-1']);
        //exit(var_dump($pwd_hashed));
        $query = "UPDATE user SET name = ?, code1 = ?, code2 = ?, login = ?, password = ? WHERE id = ?";
        $params = array(strtoupper($form_clear['name']), strtoupper($form_clear['code1']), strtoupper($form_clear['code2']), $form_clear['login'], $pwd_hashed, $form_clear['id']);
    }
    Database::execute($query, $params);
    $get_clear['user_id'] = @$form_clear['id'];
    $_SESSION['last_user_selected'] = Database::fetch("SELECT * FROM user WHERE id = ?", array($form_clear['id']));
    $last_user_selected = $_SESSION['last_user_selected'];
}
