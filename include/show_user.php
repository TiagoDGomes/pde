<?php isset($PDE) or die('Nope'); ?>

<?php include 'include/queries/user_loans.php'; ?>

<template id="tuser">
    <?php form_user($last_user_selected) ?>    
</template>


<div class="card user top">
    <h2>
        <i class="icon user"></i>
        <?= $last_user_selected['name'] ?>
    </h2>
    <p class="bar"><button onclick="show_modal('#tuser')">Editar</button></p>
    
</div>


<div class="central items table-items">
    
    <?php include 'include/date_filter.php'; ?>

    <?php if (!$selected_user_loans) :?>

        <p>Nenhum empréstimo foi encontrado no período selecionado.</p>

    <?php else : ?>
        <?php include 'include/form_hidden_select.php'; ?>

    
        <table class="<?= isset($form_clear['hide_complete'])  ? 'hide-completed' : '' ?>">
            <caption>Empréstimos deste usuário</caption>
            <thead>
                <tr>
                    <th colspan="2">
                        <select onchange="select_action(this)">
                            <option>Ações...</option>                     
                            <option value="ret">&bullet; Devolver</option>    
                            <option value="delete">&bullet; Excluir</option>                        
                        </select>
                    </th>              
                    <th>Nome do item</th>
                    <th>Código</th>
                    <th class="return">Quant. devolvida</th>
                    <th>Observações do<br>empréstimo</th>
                </tr>
            </thead>
            <tbody>
                <?php $last_date = NULL; ?>    
                <?php foreach($selected_user_loans as $item): ?>    

                    <?php $this_date = (new DateTimeImmutable($item['loan_date']))->format('d/m/Y'); ?>

                    <?php if ($last_date != $this_date): ?>

                        <tr class="date">
                            <th>
                                <input onchange="select_all_date(this,'nid')" type="checkbox" class="loan_top_checkbox" id="loan_date_<?= str_replace("/","_", $this_date) ?>">
                            </th>
                            <th colspan="6">
                                <label for="loan_date_<?= str_replace("/","_", $this_date) ?>" class="date"><?= $this_date ?></label> 
                            </th>
                        </tr>

                        <?php $last_date = $this_date; ?>

                    <?php endif; ?>                     
                    <?php $item_status = $item['count_returned'] >= $item['original_count'] ? 'complete' : 'remaining' ; ?>        
                    <tr id="line_loan_<?= $item['loan_id'] ?>" 
                        class="<?= $item_status ?> loan_date_<?= str_replace("/","_",$last_date) ?>">
                    
                        <td>
                            <input class="line_checkbox" data-id="<?= $item['loan_id'] ?>" onchange="select_item(this,'nid')" type="checkbox" id="loan_<?= $item['loan_id'] ?>">
                        </td>

                        <td class="number">

                            <?php if ($item['patrimony_number']): ?>

                                <?php HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number'] ); ?> 
                                <?= $item['patrimony_number2'] ? HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number2'] ):''; ?> 

                            <?php endif; ?>

                        </td>

                        <td>

                            <label for="loan_<?= $item['loan_id'] ?>">
                                <a href="?iid=<?= $item['model_id'] ?>">
                                    <?= $item['model_name'] ?>
                                </a>
                            </label>

                        </td>

                        <td class="number">

                            <?= $item['model_code'] ?>

                        </td>                   

                        <td class="return">
                            <?php HTMLUtil::render_counter($item, $current_user_id, $current_query_type_string, $current_query_string); ?>
                        </td>

                        <td class="details">
                            <ul class="loan_details" data-nid="<?= $item['loan_id'] ?>"
                                id="loan_details_<?= $item['loan_id'] ?>"><?php 
                                    $all_details = explode($all_details_separator_items, $item['all_details']);                                           
                                    foreach($all_details as $detail):  
                                        if ($detail){                                   
                                            $detail_data = explode($all_details_separator_cols, $detail);  
                                            if (strlen(trim($detail_data[2])) > 0) {
                                                ?><li id="loan_detail_nnid_<?= $detail_data[0] ?>">
                                                <span class="info" title="<?= $detail_data[1] ?>"><?= $detail_data[2] ?></span>                      
                                                <span class="x" onclick="delete_loan_detail(<?= $detail_data[0] ?>)">&times;</span>
                                            </li><?php }
                                        }
                                    endforeach;   
                                ?></ul> 
                            </ul>
                            <div class="loan_details_new"
                                    id="loan_details_new_<?= $item['loan_id'] ?>" 
                                    onclick="show_loan_details_action(<?= $item['loan_id'] ?>)" 
                                    onblur="hide_loan_details_action(<?= $item['loan_id'] ?>)" 
                                    contenteditable="true">        
                                
                            </div>
                            <div class="loan_details_action"
                                id="loan_details_action_<?= $item['loan_id'] ?>" 
                                style="display:none;text-align:center" >
                                <a href="javascript:;" onclick="save_loan_details(<?= $item['loan_id'] ?>)">Registrar observações</a>
                            </div>
                        </td>
                    </tr>                                
                
                <?php endforeach;?> 

            </tbody>
        </table>   
    <?php endif; ?> 
</div>
