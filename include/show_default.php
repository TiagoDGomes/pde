<div class="card top home">
    <h2>Sistema de controle de empréstimos</h2>
    <p></p>
</div>


<?php
    $query_search_loans = "SELECT m.id as model_id, 
                     m.name as model_name,
                     m.code as model_code, 
                     p.id as patrimony_id, 
                     n.tstamp as loan_date, 
                     p.number1 as patrimony_number, 
                     p.number2 as patrimony_number2, 
                     p.serial_number as patrimony_serial_number, 
                     original_count,
                     CASE 
                        WHEN original_count - sum(diff) > original_count THEN original_count
                        ELSE original_count - sum(diff) END as count_returned, 
                     $all_details_sql_concat as all_details,
                     n.id as loan_id,
                     u.name as user_name,
                     u.id as user_id
                FROM loan n
                INNER JOIN model m ON (n.model_id = m.id)
                INNER JOIN log_loan nn ON (nn.loan_id = n.id )
                INNER JOIN user u ON (u.id = n.user_id)
                LEFT JOIN patrimony p ON (n.patrimony_id = p.id AND m.id = p.model_id)

                WHERE n.tstamp BETWEEN ? AND ?
                GROUP BY n.id
                ORDER BY n.tstamp DESC
                ";
    $current_date_after_1day =  (new DateTimeImmutable($current_date_after . ' +1 day'))->format('Y-m-d');     
    
    $params = array($current_date_before, $current_date_after_1day);
    $loans = Database::fetchAll($query_search_loans, $params);

?>
<?php include 'include/date_filter.php'; ?>

<?php if (!$loans) :?>

<p>Nenhum empréstimo foi encontrado no período selecionado.</p>

<?php else : ?>
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
                <th>Horário</th>    
                <th>Pessoa</th>   
                <th>Patrimônio</th>
                <th>Nome do item</th>
                <th>Código</th>
                <th>Quant. devolvida</th>
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

                <?php if ($last_date != $this_date): ?>

                    <tr class="date">
                        <th>
                            <input onchange="select_all_date(this,'nid')" type="checkbox" class="loan_top_checkbox" id="loan_date_<?= $this_date_class ?>">
                        </th>
                        <th colspan="7">
                            <label for="loan_date_<?= $this_date_class ?>" class="date"><?= $this_date ?></label> 
                        </th>
                    </tr>

                    <?php $last_date = $this_date; ?>

                <?php endif; ?>                     

                <tr class="<?= $item['count_returned'] >= $item['original_count'] ? 'complete' : 'remaining' ?> loan_date_<?= $this_date_class ?>">
                
                    <td>
                        <input class="line_checkbox" data-id="<?= $item['loan_id'] ?>" onchange="select_item(this,'nid')" type="checkbox" id="loan_<?= $item['loan_id'] ?>">
                    </td>
                        <td>
                            <?= $this_time ?>
                        </td>
                    <td>
                        <?php $current_user = $item['user_id'];?>
                        <label title="<?= $item['user_name'] ?>" for="user_<?= $item['user_id'] ?>">
                            <a href="?uid=<?= $item['user_id'] ?>">
                                <?php if ($last_user != $current_user) : ?>
                                    <?= $item['user_name'] ?>
                                <?php else: ?>
                                    <small class="quote">...</small>
                                <?php endif; ?> 
                            </a>
                        </label>
 
                        <?php $last_user = $current_user; ?>  
                    </td>
                    <td class="number">

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

                    <td class="number return">
                        <?php $input_hidden = array(); ?>
                        <?php $input_hidden['iid'] = $item['model_id']; ?> 
                        <?php $input_hidden['uid'] = $current_user_id; ?> 
                        <?php $input_hidden['t'] = $current_query_type_string; ?> 
                        <?php $input_hidden['q'] = $current_query_string; ?> 
                        <?php $input_hidden['redirect_to'] = 'user'; ?> 
                        <?php $input_hidden['act'] = 'ret'; ?> 
                        <?php $input_hidden['nid'] = $item['loan_id']; ?>  

                        <span class="return">
                            
                            <?php if ($item['count_returned'] >  0 ): ?>
                                
                                <?php $input_hidden['diff'] = '1'; ?> 
                                <a title="1 unidade a dever" href="?<?= http_build_query($input_hidden) ?>">
                                    <span class="button-minus">-</span>  
                                </a>
                            <?php else:?>
                                <!-- <span class="button-minus">&times;</span> -->
                            <?php endif;?>

                        </span>

                        <span class="return">    
                            
                            <?php if ($item['count_returned'] <  $item['original_count'] ): ?>
                                
                                <?php $input_hidden['diff'] = '-1'; ?> 
                                <a title="1 unidade devolvida" href="?<?= http_build_query($input_hidden) ?>">
                                    <span class="button-plus">+</span>     
                                </a>
                            <?php else:?>
                                <!-- <span class="button-plus">&times;</span>     -->
                            <?php endif;?>
                            
                        </span>

                        <?= $item['count_returned'] . '/' . $item['original_count'] ?>

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