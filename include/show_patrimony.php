<?php isset($PDE) or die('Nope'); 

include 'include/queries/patrimony.php';

?>
<?php if (!$selected_patrimony): ?>
    <p>Patrimônio não encontrado.</p>
<?php else: ?>
    <div class="card item top">
        <h2>   
            <i class="icon item"></i><a href="?iid=<?= $selected_patrimony['model_id'] ?>"><?= $selected_patrimony['name'] ?></a> &gt;
            <?php HTMLUtil::render_patrimony(NULL, $selected_patrimony['number1']) ; ?>
            <?php $selected_patrimony['number2'] ? HTMLUtil::render_patrimony(NULL, $selected_patrimony['number2']) : '' ; ?>
        </h2>
        <?php if ($selected_patrimony['model_location'] || $selected_patrimony['patrimony_location'])  : ?>
        <div class="details location"> 
        
            <p><i class="icon location"></i><?= $selected_patrimony['patrimony_location'] ?>
                <?= $selected_patrimony['patrimony_location'] ? '<del>': '' ?>
                    <?= $selected_patrimony['model_location'] ?>
                <?= $selected_patrimony['patrimony_location'] ? '</del>': '' ?>
            </p>         

        </div>        
        <?php endif; ?>

        <?php if ($selected_patrimony['obs']): ?>
        <div class="details obs"> 
        
            <p><i class="icon obs"></i><?= $selected_patrimony['obs'] ?></p>         

        </div>
        <?php endif; ?>
        <p class="bar"><button>Editar</button></p>
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
                        <td><?= $item['username'] ?></td>
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
