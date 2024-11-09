<?php isset($PDE) or die('Nope');
require_once 'include/form_generic.php';
function form_patrimony($patrimony){     
    form_generator(
        "Patrimonio",
        "patrimony",
        $patrimony,
        array(                                
            array(
                "name" => "Número 1",
                "type" => "number",
                "value" => @$patrimony['number1'],
                "data-description" => "Etiqueta/patrimônio 1",  
                "placeholder" => "",  
            ),                                
            array(
                "name" => "Número 2",
                "type" => "number",
                "value" => @$patrimony['number2'],
                "data-description" => "Etiqueta/patrimônio 2",  
                "placeholder" => "",  
            ),                      
            array(
                "name" => "patrimony_location",
                "type" => "text",
                "value" => @$patrimony['patrimony_location'],
                "data-description" => "Localização",  
                "placeholder" => "",   
            ),                        
            array(
                "name" => "obs",
                "type" => "text",
                "value" => @$patrimony['patrimony_obs'],
                "data-description" => "Observações",  
                "placeholder" => "",   
            )
        )                        
    ) ;
}

function form_patrimony_save($post_clear){
    // $current_id = NULL;
    // if (!isset($_POST['id']) || $post_clear['id'] == ''){
    //     $query = "INSERT INTO patrimony (name, code, has_patrimony, patrimony_location, patrimony_obs) VALUES (?,?,?,?,?)";
    //     $params = array($post_clear['patrimony_name'], 
    //                     strtoupper($post_clear['patrimony_code']), 
    //                     $post_clear['has_patrimony'] == "1"? 1 : 0,
    //                     $post_clear['patrimony_location'],
    //                     $post_clear['patrimony_obs']
    //               );
    //     Database::execute($query, $params);
    //     $query = "SELECT max(id) FROM patrimony";          
    //     $current_id = Database::fetchOne($query, array());             
    // } else {        
    //     $query = "UPDATE patrimony SET name = ?, code = ?, has_patrimony = ?, patrimony_location = ?, patrimony_obs = ? WHERE id = ?";
    //     $params = array($post_clear['patrimony_name'], 
    //                 strtoupper($post_clear['patrimony_code']), 
    //                 $post_clear['has_patrimony'] == "1"? 1 : 0,
    //                 $post_clear['patrimony_location'],
    //                 $post_clear['patrimony_obs'],
    //                 $post_clear['id']
    //             );
    //     $current_id = $post_clear['id'];
    //     Database::execute($query, $params);
    // }    

    // HTTPResponse::redirect("?iid=$current_id");

}