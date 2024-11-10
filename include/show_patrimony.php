<?php isset($PDE) or die('Nope'); 

include 'include/queries/patrimony.php';

?>
<?php if (!$selected_patrimony): ?>
    <p>Patrimônio não encontrado.</p>
<?php else: ?>
    <template id="tpatrimony">
    <?php form_patrimony($selected_patrimony) ?>
    </template>
    <div class="card item top">
        <h2>   
            <i class="icon item"></i><a href="?iid=<?= $selected_patrimony['model_id'] ?>"><?= $selected_patrimony['name'] ?></a> &gt;
            <?php HTMLUtil::render_patrimony(NULL, $selected_patrimony['number1']) ; ?>
            <?php $selected_patrimony['number2'] ? HTMLUtil::render_patrimony(NULL, $selected_patrimony['number2']) : '' ; ?>
            <?php if ($selected_patrimony['model_loan_block'] >= 1): ?>
                <span class="message alert">   
                    <i class="icon blocked"></i>         
                    Este modelo de item foi bloqueado para empréstimo.
                </span>&nbsp;
            <?php endif; ?> 
            <?php if ($selected_patrimony['loan_block'] >= 1): ?>
                <span class="message alert">   
                    <i class="icon blocked"></i>         
                    Este item foi bloqueado para empréstimo.
                </span>&nbsp;
            <?php endif; ?>
            <?php if ($selected_patrimony['usable'] == 0): ?>
                <span class="message alert">   
                    <i class="icon trash"></i>         
                    Este item foi marcado como não utilizável.
                </span>&nbsp;
            <?php endif; ?>
            <?php if ($selected_patrimony['found'] == 0): ?>
                <span class="message alert">   
                    <i class="icon unknown"></i>         
                    Este item foi marcado como não encontrado.
                </span>&nbsp;
            <?php endif; ?>
                
        </h2>
        <?php if ($selected_patrimony['model_location'] || $selected_patrimony['patrimony_location'])  : ?>
        <div class="details location"> 
            <?php if ($selected_patrimony['patrimony_location'] != ''): ?>
                <i class="icon location"></i><?= $selected_patrimony['patrimony_location'] ?>
            <?php endif; ?>
            <?php if ($selected_patrimony['model_location'] != ''): ?>
                <?= $selected_patrimony['patrimony_location'] ? '<br><i class="icon location"></i><small>': '' ?><?= 
                    $selected_patrimony['model_location'] ?><?= 
                    $selected_patrimony['patrimony_location'] ? '</small>': '' ?>
            <?php endif; ?>
        </div>        
        <?php endif; ?>

        <?php if ($selected_patrimony['obs'] || $selected_patrimony['model_obs']): ?>
        <div class="details obs">   
            <?php if ($selected_patrimony['obs'] != ''): ?>
                <i class="icon obs"></i><?= $selected_patrimony['obs'] ?>
            <?php endif; ?>
            <?php if ($selected_patrimony['model_obs'] != ''): ?>
                <?= $selected_patrimony['obs'] ? '<br><i class="icon obs"></i><small>': '' ?><?= 
                    $selected_patrimony['model_obs'] ?><?= 
                    $selected_patrimony['obs'] ? '</small>': '' ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <p class="bar"><button onclick="show_modal('#tpatrimony')">Editar</button></p>
    </div>

    <?php if (count($selected_loans)==0): ?>
        <p>Nenhum empréstimo foi encontrado.</p>
    <?php else: ?>  
    <div class=" items">
        <h3>Empréstimos deste item etiquetado/patrimoniado</h3>
        <table>
            <thead>
                <tr>                
                    <th><input disabled type="checkbox" name="chk_all"></th>
                    <th>Nome da pessoa</th>
                    <th></th>
                    <th></th>
                    <th>Data de devolução</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($selected_loans as $item): ?> 

                    <tr>
                        <td><input disabled type="checkbox" name="chk_all"></td>
                        <td>
                            <a href="?uid=<?= $item['uid'] ?>">
                                <?= $item['username'] ?>
                            </a>
                        </td>
                        <td class="number"></td>
                        <td class="number"></td>
                        <td class=""><?= $item['loan_date'] ?></td>
                        <td></td>            
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
<?php endif; ?>
