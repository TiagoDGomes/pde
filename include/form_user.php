<?php isset($PDE) or die('Nope');
require_once 'include/form_generic.php';
function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
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
                "placeholder" => "",  
                "autocomplete" => "new-password"
            ),                        
            array(
                "name" => "change-password-1",
                "type" => "password",
                "value" => '',
                "class" => "small",
                "data-description" => "Senha", 
                "placeholder" => "",
                "autocomplete" => "new-password" 
            ),                        
            array(
                "name" => "change-password-2",
                "type" => "password",
                "value" => '',
                "class" => "small",
                "data-description" => "Repita a senha",  
                "placeholder" => "",
                "autocomplete" => "off"
            )
        )                        
    ) ;
}



function form_user_save_new($form_clear){
    global $last_user_selected;
    $user_login = $form_clear['login'] ? $form_clear['login'] : guidv4();
    if ($form_clear['change-password-1'] != $form_clear['change-password-2']){
        $query = "INSERT INTO user (name, code1, code2) VALUES (?,?,?)";
        $params = array(strtoupper($form_clear['name']), strtoupper($form_clear['code1']), strtoupper($form_clear['code2']));
    } else {
        $pwd_hashed = pde_password_hash($form_clear['change-password-1']);
        $query = "INSERT INTO user (name, code1, code2, login, password) VALUES (?,?,?,?,?)";
        $params = array(strtoupper($form_clear['name']), strtoupper($form_clear['code1']), strtoupper($form_clear['code2']), $user_login, $pwd_hashed);
    }    
    Database::execute($query, $params);
    $_SESSION['last_user_selected'] = Database::fetch("SELECT id, name, code1, code2, max(id) FROM user ORDER BY id DESC", array());
    $last_user_selected = $_SESSION['last_user_selected'];
}

function form_user_save_edit($form_clear){
    global $last_user_selected;
    $user_login = $form_clear['login'] ? $form_clear['login'] : guidv4();
    if ($form_clear['change-password-1'] != $form_clear['change-password-2']){
        $query = "UPDATE user SET name = ?, code1 = ?, code2 = ? WHERE id = ?";
        $params = array(strtoupper($form_clear['name']), strtoupper($form_clear['code1']), strtoupper($form_clear['code2']), $form_clear['id']);
    } else {
        $pwd_hashed = pde_password_hash($form_clear['change-password-1']);
        //exit(var_dump($pwd_hashed));
        $query = "UPDATE user SET name = ?, code1 = ?, code2 = ?, login = ?, password = ? WHERE id = ?";
        $params = array(strtoupper($form_clear['name']), strtoupper($form_clear['code1']), strtoupper($form_clear['code2']),  $user_login, $pwd_hashed, $form_clear['id']);
    }
    Database::execute($query, $params);
    $get_clear['user_id'] = @$form_clear['id'];
    $_SESSION['last_user_selected'] = Database::fetch("SELECT * FROM user WHERE id = ?", array($form_clear['id']));
    $last_user_selected = $_SESSION['last_user_selected'];
}
