<?php 
function form_user($user){ 
    form_generator(
        "Solicitante",
        "user",
        $user,
        array(                                
            array(
                "name" => "name",
                "type" => "text",
                "value" => @$user['name'],
                "data-description" => "Nome",  
                "placeholder" => "Nome",  
            ),                        
            array(
                "name" => "code1",
                "type" => "text",
                "value" => @$user['code1'],
                "data-description" => "Identificador adicional 1",
                "placeholder" => "Identificador adicional 1",  
            ) ,                        
            array(
                "name" => "code2",
                "type" => "text",
                "value" => @$user['code2'],
                "data-description" => "Identificador adicional 2",  
                "placeholder" => "Identificador adicional 2",  
            )
        )                        
    ) ;
}


function form_user_save_new($post_clear){
    global $last_user_selected;
    $query = "INSERT INTO user (name, code1, code2) VALUES (?,?,?)";
    $params = array(strtoupper($post_clear['name']), strtoupper($post_clear['code1']), strtoupper($post_clear['code2']));
    Database::execute($query, $params);
    $_SESSION['last_user_selected'] = Database::fetch("SELECT id, name, code1, code2, max(id) FROM user ORDER BY id DESC", array());
    $last_user_selected = $_SESSION['last_user_selected'];
}

function form_user_save_edit($post_clear){
    global $last_user_selected;
    $query = "UPDATE user SET name = ?, code1 = ?, code2 = ? WHERE id = ?";
    $params = array(strtoupper($post_clear['name']), strtoupper($post_clear['code1']), strtoupper($post_clear['code2']), $post_clear['id']);

    Database::execute($query, $params);
    $get_clear['user_id'] = @$post_clear['id'];
    $_SESSION['last_user_selected'] = Database::fetch("SELECT * FROM user WHERE id = ?", array($post_clear['id']));
    $last_user_selected = $_SESSION['last_user_selected'];
}
