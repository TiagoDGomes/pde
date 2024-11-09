<?php isset($PDE) or die('Nope');
require_once 'include/form_generic.php';
function form_patrimony($patrimony){     
    form_generator(
        "Patrimonio",
        "patrimony",
        $patrimony,
        array(                                
            array(
                "name" => "name",
                "type" => "text",
                "value" => @$patrimony['name'],
                "data-description" => "Nome do item",  
                "disabled" => "disabled",  
            ),  
            array(
                "name" => "number1",
                "type" => "text",
                "value" => @$patrimony['number1'],
                "data-description" => "Etiqueta/patrimônio 1",  
                "placeholder" => "",  
            ),                                
            array(
                "name" => "number2",
                "type" => "text",
                "value" => @$patrimony['number2'],
                "data-description" => "Etiqueta/patrimônio 2",  
                "placeholder" => "",  
            ),                      
            array(
                "name" => "model_location",
                "type" => "text",
                "value" => @$patrimony['model_location'],
                "data-description" => "Localização do item",  
                "placeholder" => "",   
                "disabled" => "disabled",  
            ),                      
            array(
                "name" => "patrimony_location",
                "type" => "text",
                "value" => @$patrimony['patrimony_location'],
                "data-description" => "Localização do patrimônio",  
                "placeholder" => "",   
            ),                        
            array(
                "name" => "obs",
                "type" => "text",
                "value" => @$patrimony['obs'],
                "data-description" => "Observações",  
                "placeholder" => "",   
            )
        )                        
    ) ;
}

function form_patrimony_save($post_clear){
    $current_id = NULL;
    if (!isset($_POST['id']) || $post_clear['id'] == ''){
        $query = "INSERT INTO patrimony (number1, number2, patrimony_location, obs) VALUES (?,?,?,?)";
        $params = array($post_clear['number1'], 
                        $post_clear['number2'], 
                        $post_clear['patrimony_location'],
                        $post_clear['obs']
                  );
        Database::execute($query, $params);
        $query = "SELECT max(id) FROM patrimony";          
        $current_id = Database::fetchOne($query, array());             
    } else {        
        $query = "UPDATE patrimony SET number1 = ?, number2 = ?, patrimony_location = ?, obs = ? WHERE id = ?";
        $params = array($post_clear['number1'], 
                        $post_clear['number2'], 
                        $post_clear['patrimony_location'],
                        $post_clear['obs'],
                        $post_clear['id']
                );
        $current_id = $post_clear['id'];
        Database::execute($query, $params);
    }    

    HTTPResponse::redirect("?pid=$current_id&save");

}