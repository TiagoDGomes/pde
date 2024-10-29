<?php function form_generator($title, $type, $itm, $elems){ ?>
<fieldset id="form_<?= $itm ? 'edit_' . $type : 'new_' . $type ?>" style="display:none">
    <legend><?= $title ?></legend>
    <div class="<?= $itm ? 'already' : 'new' ?> block">
        <form method="POST">  
            <input type="hidden" name="<?= $itm ? 'save_edit_' . $type: 'save_new_' . $type ?>" value="y">
            <?php if ($itm): ?>
            <input type="hidden" name="id" value="<?= $itm['id'] ?>">
            <?php endif; ?>
            <dl>
                <?php foreach($elems as $elem): ?>
                <dt><?= $elem['description'] ?></dt>
                <dd>
                    <?php switch ($elem['type']){
                         case 'text': 
                         case 'number': ?>
                        <input type="<?= $elem['type'] ?>" 
                                name="<?= $elem['name'] ?>" 
                                value="<?= $elem['value'] ?>"
                                onclick="<?= @$elem['onclick'] ?>"
                                placeholder="<?= $elem['placeholder'] ?>">
                            <?php break; ?>   
                    <?php } ?>
                    
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