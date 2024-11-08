<?php isset($PDE) or die('Nope');
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
                //"onclick" => "select_patrimony(1)",
                @$model['has_patrimony'] ? 'checked': '' => @$model['has_patrimony'] ? 'checked': ''
            ),                        
            array(                
                "id" => "model_multiple",
                "name" => "has_patrimony",
                "type" => "radio",
                "value" => "0",
                "data-description" => "Modelo de item sem etiqueta",
                "placeholder" => "Modelo de item sem etiqueta",  
                //"onclick" => "select_patrimony(0)",
                @$model['has_patrimony'] ? '': 'checked' => @$model['has_patrimony'] ? '': 'checked'
            )
        )                        
    ) ;
}

function form_model_save($post_clear){
    $current_id = NULL;
    if (!isset($_POST['id']) || $post_clear['id'] == ''){
        $query = "INSERT INTO model (name, code, has_patrimony) VALUES (?,?,?)";
        $params = array($post_clear['model_name'], 
                        strtoupper($post_clear['model_code']), 
                        $post_clear['has_patrimony'] == "1"? 1 : 0
                  );
        Database::execute($query, $params);
        $query = "SELECT max(id) FROM model";          
        $current_id = Database::fetchOne($query, array());             
    } else {        
        $query = "UPDATE model SET name = ?, code = ?, has_patrimony = ? WHERE id = ?";
        $params = array($post_clear['model_name'], 
                    strtoupper($post_clear['model_code']), 
                    $post_clear['has_patrimony'] == "1"? 1 : 0,
                    $post_clear['id']
                );
        $current_id = $post_clear['id'];
        Database::execute($query, $params);
    }    

    HTTPResponse::redirect("?iid=$current_id");
    //$get_clear['code'] = $post_clear['model_code'];
    // if (@$post_clear['model_unique'] == "1"){
    //     $query = "SELECT max(id) FROM model";
    //     $model_id = @$post_clear['model_id'] ? $post_clear['model_id'] : Database::fetchOne($query, array());
    //     // $patrs = explode("\n", @$post_clear['unique_codes']);
    //     // foreach($patrs as $p){
    //     //     $query = "INSERT INTO patrimony (model_id, number1) VALUES (?,?)";
    //     //     $params = array($model_id, $p);
    //     //     var_dump($query);
    //     //     var_dump($params);            
    //     //     Database::execute($query, $params);
    //     // }
    // }

}