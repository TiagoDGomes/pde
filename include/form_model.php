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
                @$model['has_patrimony'] === 1 ? 'checked': '' 
                        => @$model['has_patrimony'] === 1 ? 'checked': '',
                'data-help'=> 'Itens em que o controle do empréstimo é pela etiqueta única de cada item. <br>A quantidade é baseada pelo número de bens etiquetados (patrimoniados).'
            ),                        
            array(                
                "id" => "model_multiple_quantity_strict",
                "name" => "has_patrimony",
                "type" => "radio",
                "value" => "-1",
                "data-description" => "Modelo de item sem etiqueta e com controle de quantidade de saída",
                "placeholder" => "Modelo de item sem etiqueta",  
                @$model['has_patrimony'] ===0 && @$model['quantity_strict'] === 1 ? 'checked': '' => 
                        @!$model['has_patrimony'] === 0 && @$model['quantity_strict'] === 1 ? 'checked': '',
                'data-help'=> 'Itens em que o controle do empréstimo é pela quantidade do item. <br>Se a quantidade emprestada for superior ao que estiver disponível, o empréstimo é negado.<br>A quantidade refere-se ao número atual de itens.'
            ),                        
            array(                
                "id" => "model_multiple",
                "name" => "has_patrimony",
                "type" => "radio",
                "value" => "0",
                "data-description" => "Modelo de item sem etiqueta e sem controle de quantidade de saída",
                "placeholder" => "Modelo de item sem etiqueta",  
                @$model['has_patrimony'] === 0 && @$model['quantity_strict'] === 0 ? 'checked': '' 
                        =>  @$model['has_patrimony'] === 0 && @$model['quantity_strict'] === 0 ? 'checked': '',
                'data-help'=> 'Itens em que o controle do empréstimo é pela quantidade do item. <br>Não há bloqueios de empréstimos em caso de não houver quantidade suficiente do item.<br>A quantidade refere-se ao número ideal.'
            ),                        
            array(
                "name" => "model_location",
                "type" => "text",
                "value" => @$model['model_location'],
                "data-description" => "Localização",  
                "placeholder" => "",
                'data-help'=> ''   
            ),                        
            array(
                "name" => "model_obs",
                "type" => "text",
                "value" => @$model['model_obs'],
                "data-description" => "Observações",  
                "placeholder" => "",   
            ),                        
            array(
                "name" => "model_quantity",
                "type" => "number",
                "value" => @$model['has_patrimony'] === 1 ? 1 : @$model['quantity'],
                "data-description" => "Quantidade",  
                "placeholder" => "1",   
            ),                    
            array(
                "id"=> "model_loan_block",
                "name" => "model_loan_block",
                "type" => "checkbox",
                "value" => "1",
                "data-description" => '<span class="denied">Bloqueado para empréstimo</span>',  
                "placeholder" => "",   
                @$model['model_loan_block'] ? 'checked': '' => @$model['model_loan_block'] ? 'checked': ''
            ),                    
            array(
                "id"=> "icon_set",
                "name" => "icon_set",
                "type" => "select",
                "value" => @$model['icon_set'],
                "values" => array("default" => "(padrão)", 'key' => 'Chave'),
                "data-description" => 'Ícone',  
                "placeholder" => "",                   
            )
        )                        
    ) ;
}

function form_model_save($post_clear){
    $current_id = NULL;
    //exit(var_dump($post_clear));
    $model_multiple_quantity_strict = $post_clear['has_patrimony']== '-1' ? 1 : 0;
    $model_quantity = $post_clear['model_quantity'] > 1 ? $post_clear['model_quantity']: 1;
    if (!isset($_POST['id']) || $post_clear['id'] == ''){
        $query = "INSERT INTO model (name, code, has_patrimony, model_location, model_obs, model_loan_block, icon_set, quantity_strict, quantity) VALUES (?,?,?,?,?,?,?,?,?)";
        $params = array($post_clear['model_name'], 
                        strtoupper($post_clear['model_code']), 
                        $post_clear['has_patrimony'] == "1"? 1 : 0,
                        $post_clear['model_location'],
                        $post_clear['model_obs'],
                        $post_clear['model_loan_block'] == "1"? 1 : 0,
                        $post_clear['icon_set'],
                        $model_multiple_quantity_strict,
                        $model_quantity
                  );
        Database::execute($query, $params);
        $query = "SELECT max(id) FROM model";          
        $current_id = Database::fetchOne($query, array());             
    } else {        
        $query = "UPDATE model SET name = ?, code = ?, has_patrimony = ?, model_location = ?, model_obs = ?, model_loan_block = ?, icon_set = ?, quantity_strict = ?, quantity = ? WHERE id = ?";
        $params = array($post_clear['model_name'], 
                    strtoupper($post_clear['model_code']), 
                    $post_clear['has_patrimony'] == "1"? 1 : 0,
                    $post_clear['model_location'],
                    $post_clear['model_obs'],
                    $post_clear['model_loan_block'] == "1"? 1 : 0,
                    $post_clear['icon_set'],
                    $model_multiple_quantity_strict,
                    $model_quantity,
                    $post_clear['id']
                );
        $current_id = $post_clear['id'];
        Database::execute($query, $params);
    }    

    HTTPResponse::redirect("?iid=$current_id");

}