<?php 
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
                "placeholder" => "Identificador para busca rápida",   
            ) ,                        
            array(
                "id" => "model_unique",
                "name" => "model_unique",
                "type" => "radio",
                "value" => "1",
                "data-description" => "Modelo com identificador único (patrimoniado)",
                "placeholder" => "Modelo com identificador único (patrimoniado)",  
                //"onclick" => "select_patrimony(1)",
                "checked" => @$model['has_patrimony'] ? 'checked': ''
            ),                        
            array(                
                "id" => "model_multiple",
                "name" => "model_unique",
                "type" => "radio",
                "value" => "0",
                "data-description" => "Modelo múltiplo (não patrimoniado)",
                "placeholder" => "Modelo múltiplo (não patrimoniado)",  
                //"onclick" => "select_patrimony(0)",
                "checked" => @$model['has_patrimony'] ? '': 'checked'
            ),                        
            array(
                "id" => "unique_codes",
                "name" => "unique_codes",
                "type" => "textarea",
                "value" => "",
                "style" => "display: none",
                "placeholder" => "",  
                "data-description" => "",  
            )
        )                        
    ) ;
}

function form_model_save($post_clear){
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
    if (@$post_clear['model_unique'] == "1"){
        $query = "SELECT max(id) FROM model";
        $model_id = @$post_clear['model_id'] ? $post_clear['model_id'] : Database::fetchOne($query, array());
        $patrs = explode("\n", @$post_clear['unique_codes']);
        foreach($patrs as $p){
            $query = "INSERT INTO patrimony (model_id, number1) VALUES (?,?)";
            $params = array($model_id, $p);
            var_dump($query);
            var_dump($params);            
            Database::execute($query, $params);
        }
    }
}