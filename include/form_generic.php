<?php 

function html_element($tag, $elem, $inner = NULL){
    echo "<$tag ";
    foreach ($elem as $key => $value) {
        if ($key == 'value' && !is_null($inner)){
            echo "data-value=\"" . htmlentities($value,ENT_QUOTES) . "\" "; 
        } else {
            echo "$key=\"" . htmlentities($value,ENT_QUOTES) . "\" "; 
        }   
    }
    echo '>';
    if (!is_null($inner)){
        echo $inner;
        echo "</$tag>";
    }
}


function form_generator($title, $type, $itm, $elems){ ?>

<fieldset id="form_<?= $itm ? 'edit_' . $type : 'new_' . $type ?>">
    <legend><?= $title ?></legend>
    <div class="<?= $itm ? 'already' : 'new' ?> block">
        <form method="POST">  

            <input type="hidden" name="<?= $itm ? 'save_edit_' . $type: 'save_new_' . $type ?>" value="y">
            
            <?php if ($itm): ?>

            <input type="hidden" name="id" value="<?= $itm['id'] ?>">

            <?php endif; ?>

            <dl>
                <?php foreach($elems as $elem): ?>

                <dt>
                    <?php
                    switch ($elem['type']){ 
                        case 'radio':
                        case 'checkbox':
                            html_element('input', $elem);
                            html_element('label', array(
                                "for" => $elem['id']
                            ),@$elem['data-description']);
                            break;
                        default:
                            echo @$elem['data-description'];
                    } ?>
                </dt>
                <dd>
                    <?php 
                        switch ($elem['type']){                         
                            case 'text': 
                            case 'number':
                                html_element('input',$elem);
                                break;
                            case 'textarea':
                                html_element('textarea',$elem, @$elem['value']);
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