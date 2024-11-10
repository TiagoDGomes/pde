<?php isset($PDE) or die('Nope');

function html_element($tag, $elem, $inner = NULL, $return_value=FALSE){
    $ret_value = "<$tag ";
    foreach ($elem as $key => $value) {
        if ($tag != 'option' && $key == 'value' && !is_null($inner)){
            $ret_value .= "data-value=\"" . htmlentities($value,ENT_QUOTES) . "\" "; 
        } else if ($key != 'values') {
            try{
                $ret_value .= "$key=\"" . htmlentities($value,ENT_QUOTES) . "\" "; 
            } catch(TypeError $e){
                exit(var_dump([$key, $value]));
            }     
        }   
    }
    $ret_value .= '>';
    if (!is_null($inner)){
        $ret_value .= $inner;
        $ret_value .= "</$tag>";
    }
    if ($return_value){
        return $ret_value;
    } else{
        echo $ret_value;
    }
}


function form_generator($title, $type, $itm, $elems){ ?>

<fieldset id="form_<?= $itm ? 'edit_' . $type : 'new_' . $type ?>">
    <legend><?= $title ?></legend>
    <div class="<?= $itm ? 'already' : 'new' ?> block">
        <form method="POST">  
            <?php $is_new = isset($itm['id']) && $itm['id'] > 0; ?>
            <input type="hidden" name="<?= $is_new  ? 'save_edit_' . $type: 'save_new_' . $type ?>" value="y">
            
            <?php if ($is_new): ?>
                
            <input type="hidden" name="id" value="<?= $itm['id'] ?>">

            <?php endif; ?>

            <dl>
                <?php foreach($elems as $elem): ?>

                <dt>
                    <?php
                    switch ($elem['type']){ 
                        case 'radio':
                        case 'checkbox':
                            break;
                        default:
                            echo @$elem['data-description'];
                    } ?>
                </dt>
                <dd>
                    <?php 
                        switch ($elem['type']){    
                            case 'radio':
                            case 'checkbox':
                                html_element('input', $elem);
                                html_element('label', array(
                                    "for" => $elem['id']
                                ),@$elem['data-description']);
                                break;                      
                            case 'hidden':                   
                            case 'text': 
                            case 'number':
                                html_element('input',$elem);
                                break;
                            case 'textarea':
                                html_element('textarea',$elem, @$elem['value']);
                                break;
                            case 'select':
                                $options = array();                                
                                foreach($elem['values'] as $key => $value){
                                    $e = array('value' => $key);
                                    if ($elem['value'] == $key){
                                        $e['selected'] = "selected";
                                    }
                                    $options[] = html_element('option', $e, $value,TRUE);
                                }    
                                unset($elem['type']);                            
                                html_element('select', $elem, implode("\n",$options));
                        } 
                    ?>

                </dd>

                <?php endforeach; ?>
                
                <dt>&nbsp;</dt>
                <dd>                            
                    <button>Salvar</button>                                
                </dd>  
            </dl>              
        </form>    
    </div>
</fieldset>

<?php }