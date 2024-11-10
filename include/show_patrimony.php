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
            
            <i class="icon item"></i><?php HTMLUtil::render_patrimony(NULL, $selected_patrimony['number1']) ; ?>
            <?php $selected_patrimony['number2'] ? HTMLUtil::render_patrimony(NULL, $selected_patrimony['number2']) : '' ; ?>
            <a href="?iid=<?= $selected_patrimony['model_id'] ?>">
                <?= $selected_patrimony['name'] ?>
            </a>            
                
        </h2>
        <?php if ($selected_patrimony['model_location'] || $selected_patrimony['patrimony_location'])  : ?>
        <p>
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
                    Este item foi definido como não utilizável.
                </span>&nbsp;
            <?php endif; ?>
            <?php if ($selected_patrimony['found'] == 0): ?>
                <span class="message alert">   
                    <i class="icon unknown"></i>         
                    Este item foi definido como não encontrado.
                </span>&nbsp;
            <?php endif; ?>
        </p>
            <p class="details location"> 
            <?php if ($selected_patrimony['patrimony_location'] != ''): ?>
                <i class="icon location"></i><?= $selected_patrimony['patrimony_location'] ?>
                <?php $br = 1; ?>
            <?php endif; ?>
            <?php if ($selected_patrimony['model_location'] != ''): ?>  
                <?= isset($br)? '<br>': '' ?>              
                <small>
                    <i class="icon location"></i>
                    <?= $selected_patrimony['model_location'] ?>
                </small>
            <?php endif; ?>
        </p>        
        <?php endif; ?>
        <?php unset($br); ?>          
        <?php if ($selected_patrimony['obs'] || $selected_patrimony['model_obs']): ?>
        <p class="details obs">   
            <?php if ($selected_patrimony['obs'] != ''): ?>
                <i class="icon obs"></i><?= $selected_patrimony['obs'] ?>
                <?php $br = 1; ?>
            <?php endif; ?>
            <?php if ($selected_patrimony['model_obs'] != ''): ?>                
                <?= isset($br)? '<br>': '' ?>  
                <small>
                    <i class="icon obs"></i>
                    <?= $selected_patrimony['model_obs'] ?>
                </small>
            <?php endif; ?>
        </p>
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
                    <th class="date">Data do empréstimo</th>
                    <th class="text">Nome da pessoa</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($selected_loans as $item): ?> 

                    <tr>
                        <td><input disabled type="checkbox" name="chk_all"></td>
                        <td class=""><?= $item['loan_date'] ? (new DateTimeImmutable( $item['loan_date'] ))->format('d/m/Y H:i:s') : ''?></td>
                        <td>
                            <a href="?uid=<?= $item['uid'] ?>">
                                <?= $item['username'] ?>
                            </a>
                        </td>
                        <td>
                        <?php if ($item['original_count'] > $item['count_returned'] ) : ?>
                            <?php $input_hidden['act'] = 'ret'; ?> 
                            <?php $input_hidden['redirect_to'] = 'patrimony'; ?> 
                            <?php $input_hidden['diff'] = '-1'; ?> 
                            <?php $input_hidden['nid'] = $item['loan_id']; ?> 
                            <?php $input_hidden['pid'] = $item['patrimony_id']; ?>                     
                            <i class="icon check"></i>
                            <a href="?<?= http_build_query($input_hidden) ?>">Marcar como devolvido</a>
                        <?php endif; ?>    
                        </td>            
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
<?php endif; ?>
