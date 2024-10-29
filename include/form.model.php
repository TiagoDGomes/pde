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
                "value" => @$model['model_name'],
                "data-description" => "Nome do modelo do item",  
                "placeholder" => "Nome do modelo do item",  
            ),                        
            array(
                "name" => "model_code",
                "type" => "text",
                "value" => @$model['model_code'],
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
                "onclick" => "select_patrimony(1)"
            ),                        
            array(                
                "id" => "model_multiple",
                "name" => "model_unique",
                "type" => "radio",
                "value" => "0",
                "data-description" => "Modelo múltiplo (não patrimoniado)",
                "placeholder" => "Modelo múltiplo (não patrimoniado)",  
                "onclick" => "select_patrimony(0)"
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