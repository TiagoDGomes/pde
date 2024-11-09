<?php isset($PDE) or die('Nope');?><div class="card top home">
    <h2>Sistema de controle de empréstimos</h2>
    <p></p>
</div>


<?php include 'include/queries/loans.php'; ?>
<?php include 'include/date_filter.php'; ?>

<?php if (!$loans) :?>

<p>Nenhum empréstimo foi encontrado no período selecionado.</p>

<?php else : ?>
    <?php include 'include/form_hidden_select.php'; ?>
    <table>
        <thead>
            <tr>
                <th>
                    <select onchange="select_action(this)">
                        <option>...</option>                     
                        <option value="ret">&bullet; Devolver</option>    
                        <option value="delete">&bullet; Excluir</option>                        
                    </select>
                </th>              
                <th class="time">Horário</th>    
                <th>Pessoa</th>   
                <th class="number">Patrimônio</th>
                <th>Nome do item</th>
                <th class="number">Código</th>
                <th class="return">Quant. devolvida</th>
                <th>Observações do<br>empréstimo</th>
            </tr>
        </thead>
        <tbody>
            <?php $last_date = NULL; ?> 
            <?php $last_user = NULL; ?>    
            <?php foreach($loans as $item): ?>    

                <?php $this_date = (new DateTimeImmutable($item['loan_date']))->format('d/m/Y'); ?>
                <?php $this_date_class = str_replace("/","_", $this_date); ?>
                <?php $this_time = (new DateTimeImmutable($item['loan_date']))->format('H:i'); ?>
                <?php $reset_date = false; ?>
                <?php if ($last_date != $this_date): ?>

                    <tr class="date">
                        <th>
                            <input onchange="select_all_date(this,'nid')" type="checkbox" class="loan_top_checkbox" id="loan_date_<?= $this_date_class ?>">
                        </th>
                        <th colspan="7">
                            <label for="loan_date_<?= $this_date_class ?>" class="date"><?= $this_date ?></label> 
                        </th>
                    </tr>
                    <?php $reset_date = true; ?>
                    <?php $last_date = $this_date; ?>

                <?php endif; ?>                     
                <?php $item_status = $item['count_returned'] >= $item['original_count'] ? 'complete' : 'remaining' ; ?>        
                <tr id="line_loan_<?= $item['loan_id'] ?>" style="display:<?= isset($form_clear['hide_complete']) && $item_status == 'complete' ? 'none' : 'table-row' ?>;"
                    class="<?= $item_status ?> loan_date_<?= str_replace("/","_",$last_date) ?>">
                
                    <td>
                        <input class="line_checkbox" data-id="<?= $item['loan_id'] ?>" onchange="select_item(this,'nid')" type="checkbox" id="loan_<?= $item['loan_id'] ?>">
                    </td>
                    <td class="time">
                        <?= $this_time ?>
                    </td>
                    <td>
                        <?php $current_user = $item['user_id'];?>
                        <label title="<?= $item['user_name'] ?>" for="user_<?= $item['user_id'] ?>">
                            <a href="?uid=<?= $item['user_id'] ?>">
                                <?php if ($last_user != $current_user || $reset_date) : ?>
                                    <?= $item['user_name'] ?>
                                <?php else: ?>
                                    <small class="quote">...</small>
                                <?php endif; ?> 
                            </a>
                        </label>
 
                        <?php $last_user = $current_user; ?>  
                    </td>
                    <td class="patr">

                        <?php if ($item['patrimony_number']): ?>

                            <?php HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number'] ); ?> 
                            <?= $item['patrimony_number2'] ? HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number2'] ):''; ?> 

                        <?php endif; ?>

                    </td>

                    <td>

                        <label for="loan_<?= $item['loan_id'] ?>">
                            <a href="?iid=<?=  $item['model_id'] ?>">
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

<?php endif;