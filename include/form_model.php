<?php isset($PDE) or die('Nope');
require_once 'include/form_generic.php';
function form_model($model){     
    form_generator(
        "Item",
        "model",
        $model,
        array(                                
            array(
                "name" => "model_name",
                "type" => "text",
                "value" => @$model['name'],
                "data-description" => "Nome do modelo do item",  
                "placeholder" => "Nome do modelo do item",  
            ),                        
            array(
                "name" => "model_code",
                "type" => "text",
                "value" => @$model['code'],
                "data-description" => "Identificador para busca rápida",  
                "placeholder" => "Ex.: CI7411",   
            ) ,                        
            array(
                "id" => "model_unique",
                "name" => "has_patrimony",
                "type" => "radio",
                "value" => "1",
                "data-description" => "Modelo de item com etiqueta, patrimônio ou identificações únicas",
                "placeholder" => "Modelo de item com etiqueta, patrimônio ou identificações únicas",  
                @$model['has_patrimony'] ? 'checked': '' => @$model['has_patrimony'] ? 'checked': ''
            ),                        
            array(                
                "id" => "model_multiple",
                "name" => "has_patrimony",
                "type" => "radio",
                "value" => "0",
                "data-description" => "Modelo de item sem etiqueta",
                "placeholder" => "Modelo de item sem etiqueta",  
                @$model['has_patrimony'] ? '': 'checked' => @$model['has_patrimony'] ? '': 'checked'
            ),                        
            array(
                "name" => "model_location",
                "type" => "text",
                "value" => @$model['model_location'],
                "data-description" => "Localização",  
                "placeholder" => "",   
            ),                        
            array(
                "name" => "model_obs",
                "type" => "text",
                "value" => @$model['model_obs'],
                "data-description" => "Observações",  
                "placeholder" => "",   
            ),                    
            array(
                "id"=> "model_loan_block",
                "name" => "model_loan_block",
                "type" => "checkbox",
                "value" => "1",
                "data-description" => '<span class="denied">Bloqueado para empréstimo</span>',  
                "placeholder" => "",   
                @$model['model_loan_block'] ? 'checked': '' => @$model['model_loan_block'] ? 'checked': ''
            )
        )                        
    ) ;
}

function form_model_save($post_clear){
    $current_id = NULL;
    if (!isset($_POST['id']) || $post_clear['id'] == ''){
        $query = "INSERT INTO model (name, code, has_patrimony, model_location, model_obs, model_loan_block) VALUES (?,?,?,?,?)";
        $params = array($post_clear['model_name'], 
                        strtoupper($post_clear['model_code']), 
                        $post_clear['has_patrimony'] == "1"? 1 : 0,
                        $post_clear['model_location'],
                        $post_clear['model_obs'],
                        $post_clear['model_loan_block'] == "1"? 1 : 0
                  );
        Database::execute($query, $params);
        $query = "SELECT max(id) FROM model";          
        $current_id = Database::fetchOne($query, array());             
    } else {        
        $query = "UPDATE model SET name = ?, code = ?, has_patrimony = ?, model_location = ?, model_obs = ?, model_loan_block = ? WHERE id = ?";
        $params = array($post_clear['model_name'], 
                    strtoupper($post_clear['model_code']), 
                    $post_clear['has_patrimony'] == "1"? 1 : 0,
                    $post_clear['model_location'],
                    $post_clear['model_obs'],
                    $post_clear['model_loan_block'] == "1"? 1 : 0,
                    $post_clear['id']
                );
        $current_id = $post_clear['id'];
        Database::execute($query, $params);
    }    

    HTTPResponse::redirect("?iid=$current_id");

}