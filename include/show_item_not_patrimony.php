<?php isset($PDE) or die('Nope');
require_once 'include/form_patrimony.php'; ?>
<template id="tpatrimony">

    <?php form_patrimony(array_merge($selected_item, 
                                        array("patrimony_location"=>'',
                                                "obs" => '', 
                                                'model_id' => $selected_item['id'],
                                                'id' => NULL,
                        ))); ?>
</template>
<?php if (count($selected_loans)==0): ?>
    <p>Nenhum empréstimo foi encontrado.</p>
<?php else: ?>    
    <div class=" items">
        <table>
            <caption>Empréstimos deste modelo de item</caption>
            <thead>
                <tr>                
                    <th><input disabled type="checkbox" name="chk_all"></th>
                    <th>Data do último<br> empréstimo</th>
                    <th>Nome da pessoa</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
                
            <?php foreach($selected_loans as $item): ?> 

            <tr class="<?= $item['icon_block'] ?>">
                <td><input disabled type="checkbox" name="chk_all"></td>
                <td class="date">
                    <i class="icon <?= $item['original_count'] <= $item['count_returned'] ? 'check' : 'cart' ?>"><span></span></i>
                    <?= $item['loan_date'] ? (new DateTimeImmutable( $item['loan_date'] ))->format('d/m/Y H:i:s') : ''?>
                    
                    
                </td>
                <td>
                    <a href="?uid=<?= $item['uid'] ?>">
                        <?= $item['username'] ?>
                    </a>
                </td>  
                <td>
                    <?php if ($item['original_count'] > $item['count_returned'] ) : ?>
                        <?php $input_hidden['act'] = 'ret'; ?> 
                        <?php $input_hidden['redirect_to'] = 'item'; ?> 
                        <?php $input_hidden['diff'] = '-1'; ?> 
                        <?php $input_hidden['nid'] = $item['loan_id']; ?> 
                        <?php $input_hidden['iid'] = $item['model_id']; ?>                     
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
