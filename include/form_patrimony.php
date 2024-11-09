<?php isset($PDE) or die('Nope');
require_once 'include/form_generic.php';
function form_patrimony($patrimony){     
    form_generator(
        "Patrimônio",
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
            ),                    
            array(
                "id"=> "loan_block",
                "name" => "loan_block",
                "type" => "checkbox",
                "value" => "1",
                "data-description" => '<span class="denied">Bloqueado para empréstimo ' . 
                                      ($patrimony['model_loan_block'] == 1 ? '(modelo do item está bloqueado)':'') . 
                                      '</span>',  
                "placeholder" => "",   
                ($patrimony['loan_block'] || $patrimony['model_loan_block'] == 1) ? 'checked': '' => @$patrimony['loan_block'] ? 'checked': '',
                $patrimony['model_loan_block'] == 1 ? 'disabled' :'' => NULL
            ),                    
            array(
                "id"=> "found",
                "name" => "found",
                "type" => "checkbox",
                "value" => "1",
                "data-description" => '<span class="allow">Localizado / Não perdido</span>',  
                "placeholder" => "",   
                @$patrimony['found'] ? 'checked': '' => @$patrimony['found'] ? 'checked': ''
            ),                    
            array(
                "id"=> "usable",
                "name" => "usable",
                "type" => "checkbox",
                "value" => "1",
                "data-description" => '<span class="allow">Utilizável / Funcional</span>',  
                "placeholder" => "",   
                @$patrimony['usable'] ? 'checked': '' => @$patrimony['usable'] ? 'checked': ''
            )
        )                        
    ) ;
}

function form_patrimony_save($post_clear){
    $current_id = NULL;
    if (!isset($_POST['id']) || $post_clear['id'] == ''){
        $query = "INSERT INTO patrimony (number1, number2, patrimony_location, obs, loan_block, usable) VALUES (?,?,?,?,?,?)";
        $params = array($post_clear['number1'], 
                        $post_clear['number2'], 
                        $post_clear['patrimony_location'],
                        $post_clear['obs'],
                        @$post_clear['loan_block'] ? 1 : 0,
                        @$post_clear['found'] ? 1 : 0,
                        @$post_clear['usable'] ? 1 : 0,
                  );
        Database::execute($query, $params);
        $query = "SELECT max(id) FROM patrimony";          
        $current_id = Database::fetchOne($query, array());             
    } else {        
        $query = "UPDATE patrimony SET number1 = ?, number2 = ?, patrimony_location = ?, obs = ?, loan_block = ?, found = ?, usable = ? WHERE id = ?";
        $params = array($post_clear['number1'], 
                        $post_clear['number2'], 
                        $post_clear['patrimony_location'],
                        $post_clear['obs'],
                        @$post_clear['loan_block'] ? 1 : 0,
                        @$post_clear['found'] ? 1 : 0,
                        @$post_clear['usable'] ? 1 : 0,
                        $post_clear['id']
                );
        $current_id = $post_clear['id'];
        Database::execute($query, $params);
    }    

    HTTPResponse::redirect("?pid=$current_id&save");

}