<?php isset($PDE) or die('Nope');

include 'include/queries/item.php';

?>
<?php if (!$selected_item): ?>
    <p>Item não encontrado.</p>
<?php else: ?>
    <template id="titem">
    <?php form_model($selected_item) ?>
    </template>
    <div class="card item top">
        <h2>
            <i class="icon item"></i><?= $selected_item['name'] ?>  
                 
        </h2>
        <p class="alert details">
            <?php if ($selected_item['model_loan_block'] >= 1): ?>
                <span class="message alert">   
                    <i class="icon blocked"></i>         
                    Este modelo de item foi bloqueado para empréstimo.
                </span>
            <?php endif; ?>               
        </p>
        <?php if ($selected_item['model_location']): ?>
        <p class="details location"> 
        
            <i class="icon location"></i><?= $selected_item['model_location'] ?>         

        </p>
        <?php endif; ?>

        <?php if ($selected_item['model_obs']): ?>
        <p class="details obs"> 
        
            <i class="icon obs"></i><?= $selected_item['model_obs'] ?>      

        </p>
        <?php endif; ?>
        
        <p class="bar">
            <button onclick="show_modal('#titem')">
                Editar modelo de item
            </button>

            <?php if ($selected_item['has_patrimony']): ?>
                <button onclick="show_modal('#tpatrimony');">
                    Adicionar uma nova etiqueta/patrimônio a este modelo de item
                </button>
            <?php endif; ?>
            
        </p>
        
    </div>
    <?php if ($selected_item['has_patrimony']): ?>
        <?php require_once 'include/show_item_has_patrimony.php'; ?>
    
    <?php else: ?>
        <?php require_once 'include/show_item_not_patrimony.php'; ?>
    <?php endif; ?>
<?php endif; ?>
