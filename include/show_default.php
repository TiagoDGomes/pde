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
    <table class="<?= isset($form_clear['show_complete'])  ? 'show-completed' : '' ?>">
        <caption>Empréstimos</caption>
        <thead>
            <tr>
                <th>
                    <select disabled onchange="select_action(this)">
                        <option>...</option>                     
                        <option value="ret">&bullet; Devolver</option>    
                        <option value="delete">&bullet; Excluir</option>                        
                    </select>
                </th>              
                <th class="time">Horário</th>    
                <th class="username">Pessoa</th>   
                <th class="number">Etiqueta</th>
                <th>Nome do item</th>
                <th class="number">Código</th>
                <th class="return">Quant. devolvida</th>
                <th>Observações do<br>empréstimo</th>
            </tr>
        </thead>
        
            <?php $last_date = NULL; ?> 
            <?php $last_user = NULL; ?>  
            <?php $current_user = NULL; ?>    
            <?php foreach($loans as $key => $item): ?>    

                <?php $this_date = (new DateTimeImmutable($item['loan_date']))->format('d/m/Y'); ?>
                <?php $this_date_class = str_replace("/","_", $this_date); ?>
                <?php $this_time = (new DateTimeImmutable($item['loan_date']))->format('H:i'); ?>
                <?php $next_is_another = @$loans[$key+1]['user_id'] != $item['user_id']; ?>
                <?php $reset_date = false; ?>
                <?php $current_user = $item['user_id'];?>
                <?php $item_status = $item['count_returned'] >= $item['original_count'] ? 'complete' : 'remaining' ; ?> 
                <?php $user_loan_first = $reset_date || $current_user != $last_user ?>
                <?php if ($current_user != $last_user): ?>
                    <tbody class="user-loan-group">
                <?php endif; ?>    
                <?php if ($last_date != $this_date): ?>
                    </tbody><tbody class="user-loan-group">
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

                <tr id="line_loan_<?= $item['loan_id'] ?>" 
                    class="<?= $item_status ?> loan_date_<?= str_replace("/","_",$last_date) ?> <?=  $user_loan_first ? 'user-loan-first': ''?>">
                
                    <td>
                        <input class="line_checkbox" data-id="<?= $item['loan_id'] ?>" onchange="select_item(this,'nid')" type="checkbox" id="loan_<?= $item['loan_id'] ?>">
                    </td>
                    <td class="time">
                        <?= $this_time ?>
                    </td>
                    <td class="username">
                        
                        <pde-user title="<?= $item['user_name'] ?>" for="user_<?= $item['user_id'] ?>">
                            <a href="?uid=<?= $item['user_id'] ?>">
                                <?php if ($reset_date) : ?>
                                    <span class="show-name"><?= $item['user_name'] ?></span>
                                <?php else: ?>
                                    <span class="hide-name"><?= $item['user_name'] ?></span>
                                <?php endif; ?> 
                            </a>
                        </pde-user>
 
                         
                    </td>
                    <td class="patr">

                        <?php if ($item['patrimony_number']): ?>

                            <?php HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number'],$item['icon_set'] ); ?> 
                            <?= $item['patrimony_number2'] ? HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number2'],$item['icon_set'] ):''; ?> 

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
                <?php if ($next_is_another): ?>
                </tbody>
                <?php endif; ?>                             
                <?php $last_user = $current_user; ?>                 
            <?php endforeach;?> 

        
    </table>   

<?php endif;