<?php isset($PDE) or die('Nope'); ?>

<?php

    $query_search_user_loans = "SELECT m.id as model_id, 
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
                     group_concat(details, '<br>') as all_details ,
                     n.id as loan_id
                FROM loan n
                INNER JOIN model m ON (n.model_id = m.id)
                INNER JOIN log_loan nn ON (nn.loan_id = n.id )
                LEFT JOIN patrimony p ON (n.patrimony_id = p.id AND m.id = p.model_id)
                WHERE n.user_id =  ? AND n.tstamp BETWEEN ? AND ?
                GROUP BY n.id
                ORDER BY n.tstamp DESC
                ";
    $current_date_after_1day =  (new DateTimeImmutable($current_date_after . ' +1 day'))->format('Y-m-d');     
    
    $params = array($form_clear['uid'], $current_date_before, $current_date_after_1day);
    $selected_user_loans = Database::fetchAll($query_search_user_loans, $params);

?>


<h2>
    <i class="icon user"></i>
    <?= $last_user_selected['name'] ?>
</h2>
<p><button>Editar</button></p>
<div>
    <form>    
        <input type="hidden" name="uid" value="<?= $form_clear['uid'] ?>">   
        <input type="hidden" name="q" value="<?= @$form_clear['q'] ?>">     
        <input type="hidden" name="t" value="<?= @$form_clear['t'] ?>">    
        <label>Entre:
            <input id="chk_date_before" onchange="date_change(this)" type="date" name="before" max="<?= $current_date_now ?>" value="<?=$current_date_before ?>">
        </label>        
        <label> e
            <input id="chk_date_after" onchange="date_change(this)" type="date" name="after" max="<?= $current_date_now ?>" value="<?= $current_date_after ?>">
        </label>
        <button>Filtrar</button>  
    </form>     
    <script>
        function date_change(elem){
            var chk_date_before = document.getElementById('chk_date_before');
            var chk_date_after = document.getElementById('chk_date_after');
            if (elem == chk_date_before){
                chk_date_after.min = chk_date_before.value;
            } else {
                chk_date_before.max = chk_date_after.value;
            }
        }
    </script> 
</div>
<?php if (!$selected_user_loans) :?>

    <p>Nenhum empréstimo foi encontrado no período selecionado.</p>

<?php else : ?>

<div class="items">
    <table>
        <thead>
            <tr>
                <th><input disabled type="checkbox" name="chk_all"></th>
                <!-- <th>Patrimônio</th> -->
                <th colspan="2">Nome do item</th>
                <th>Código</th>
                <th>Quant. devolvida</th>
                <th>Quant.</th>
                <th>Detalhes</th>
            </tr>
        </thead>
        <tbody>
            <?php $last_date = NULL; ?>    
            <?php foreach($selected_user_loans as $item): ?>    

                <?php $this_date = (new DateTimeImmutable($item['loan_date']))->format('d/m/Y'); ?>

                <?php if ($last_date != $this_date): ?>

                    <tr class="date">
                        <th>
                            <input disabled type="checkbox" class="loan_top_checkbox" id="loan_date_<?= str_replace("/","_", $this_date) ?>">
                        </th>
                        <th colspan="6">
                            <label for="loan_date_<?= str_replace("/","_", $this_date) ?>" class="date"><?= $this_date ?></label> 
                        </th>
                    </tr>

                    <?php $last_date = $this_date; ?>

                <?php endif; ?>                     

                <tr class="<?= $item['count_returned'] >= $item['original_count'] ? 'complete' : 'remaining' ?>">
                
                    <td>
                        <input disabled type="checkbox" id="loan_<?= $item['loan_id'] ?>">
                    </td>

                    <td class="number">

                        <?php if ($item['patrimony_number']): ?>

                            <?php HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number'] ); ?> 
                            <?= $item['patrimony_number2'] ? HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number2'] ):''; ?> 

                        <?php endif; ?>

                    </td>

                    <td>

                        <label for="loan_<?= $item['loan_id'] ?>">

                            <?= $item['model_name'] ?>

                        </label>

                    </td>

                    <td class="number">

                        <?= $item['model_code'] ?>

                    </td>                   

                    <td class="number">

                        <?= $item['count_returned'] . '/' . $item['original_count'] ?>

                    </td>

                    <td class="number return">
                    
                        <span class="return">
                        
                            <?php if ($item['count_returned'] >  0 ): ?>
                                
                                <a href="?uid=<?= $current_user_id ?>&log_loan=y&loan_id=<?= $item['loan_id'] ?>&diff=-1&code=<?= @$get_clear['code'] ?>">
                                    <span class="button-minus">-</span>  
                                </a>
                            
                            <?php endif;?>

                        </span>  

                        <?= $item['count_returned'] ?>

                        <span class="return">    
                            
                        <?php if ($item['count_returned'] <  $item['original_count'] ): ?>
                            
                                <a href="?uid=<?= $current_user_id ?>&log_loan=y&loan_id=<?= $item['loan_id'] ?>&diff=1&code=<?= @$get_clear['code'] ?>">
                                    <span class="button-plus">+</span>     
                                </a>
                            
                        <?php endif;?>
                        
                        </span>
                        <i class="icon loading"></i>
                    </td>

                    <td class="details" contenteditable="true">

                        <?= $item['all_details'] ?>

                    </td>
                </tr>                                
            
            <?php endforeach;?> 

        </tbody>
    </table>
</div>
<?php endif; ?>
