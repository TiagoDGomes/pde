<?php require_once __DIR__ . '/../base.php'; ?>


<pre><?php //var_dump($search_user_values); ?></pre>


<?php if ($last_user_selected) : ?>

    <h2>
        <?= $last_user_selected['name'] ?>
    </h2>
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
                    <th><input type="checkbox" name="chk_all"></th>
                    <th>Nome do item</th>
                    <th>Código</th>
                    <th>Patrimônio</th>
                    <th>Quantidade emprestada</th>
                    <th>Quantidade devolvida</th>
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
                                <input class="loan_top_checkbox" type="checkbox" id="loan_date_<?= str_replace("/","_", $this_date) ?>">
                            </th>
                            <th colspan="6">
                                <label for="loan_date_<?= str_replace("/","_", $this_date) ?>" class="date"><?= $this_date ?></label> 
                            </th>
                        </tr>
                    
                    <?php endif; ?> 
                    <?php $last_date = $this_date; ?>

                    <tr class="<?= $item['count_remaining'] == $item['original_count'] ? 'complete' : 'remaining' ?>">
                    
                    <td>
                        <input type="checkbox" id="loan_<?= $item['loan_id'] ?>">
                    </td>

                    <td>
                        <label for="loan_<?= $item['loan_id'] ?>"><?= $item['model_name'] ?></label>
                    </td>

                    <td class="number"><?= $item['model_code'] ?></td>

                    <td class="number">
                        <?php if ($item['patrimony_number']): ?>
                            <span class="patrimony">
                                <i class="icon pat"></i> 
                                <?= $item['patrimony_number'] ?>
                            </span>
                        <?php endif; ?>
                    </td>

                    <td class="number">
                        <?= $item['original_count'] ?>
                    </td>

                    <td class="number return">
                    
                        <span class="return">
                        
                        <?php if ($item['count_remaining'] >  0 ): ?>
                            
                                <a href="?uid=<?= $current_user_id ?>&log_loan=y&loan_id=<?= $item['loan_id'] ?>&diff=-1&code=<?= @$get_clear['code'] ?>">
                                    <span class="button-minus">-</span>  
                                </a>
                            
                        <?php endif;?>
                        </span>        
                        <?= $item['count_remaining'] ?>
                        <span class="return">        
                        <?php if ($item['count_remaining'] <  $item['original_count'] ): ?>
                            
                                <a href="?uid=<?= $current_user_id ?>&log_loan=y&loan_id=<?= $item['loan_id'] ?>&diff=1&code=<?= @$get_clear['code'] ?>">
                                    <span class="button-plus">+</span>     
                                </a>
                            
                        <?php endif;?>
                        </span>
                        <i class="icon loading"></i>
                    </td>
                    <td class="details" contenteditable="true"><?= $item['all_details'] ?></td>
                </tr>                                
                
                <?php endforeach;?> 
            </tbody>
        </table>
    </div>
    <?php endif; ?>
<?php endif; ?>