<?php isset($PDE) or die('Nope');

include 'include/queries/item.php';

?>
<?php if (!$selected_item): ?>
    <p>Item não encontrado.</p>
<?php else: ?>
    <template id="titem">
    <?php form_model($selected_item) ?>
    </template>
    <div class="card item top">
        <h2>
            <i class="icon item"></i><?= $selected_item['name'] ?>        
        </h2>

        <?php if ($selected_item['model_location']): ?>
        <div class="details location"> 
        
            <p><i class="icon location"></i><?= $selected_item['model_location'] ?> </p>         

        </div>
        <?php endif; ?>

        <?php if ($selected_item['model_obs']): ?>
        <div class="details obs"> 
        
            <p><i class="icon obs"></i><?= $selected_item['model_obs'] ?></p>         

        </div>
        <?php endif; ?>
        <p class="bar">
            <button onclick="show_modal('#titem')">
                Editar modelo de item
            </button>

            <?php if ($selected_item['has_patrimony']): ?>
                <button onclick="show_modal('#tpatrimony');">
                    Adicionar uma nova etiqueta/patrimônio a este modelo de item
                </button>
            <?php endif; ?>
            
        </p>
    </div>
    <?php if ($selected_item['has_patrimony']): ?>
        <?php require_once 'include/form_patrimony.php'; ?>
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
                    <thead>
                        <tr>                
                            <th><input disabled type="checkbox" name="chk_all"></th>
                            <th colspan="3">Nº Patr.</th>
                            <th>Data do último<br> empréstimo</th>
                            <th>Nome da pessoa</th>
                            <th>Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    <?php foreach($selected_loans as $item): ?> 

                    <tr class="<?= $item['icon_block'] ?>">
                        <td><input disabled type="checkbox" name="chk_all"></td>
                        <td>
                            <i class="icon <?= $item['icon_block'] ?>"><span></span></i>
                        </td>
                        <td class="number">
                            <?php HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number']) ; ?>
                        </td>
                        <td class="number">
                            <?php $item['patrimony_number2'] ? HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number2']) : '' ; ?>
                        </td>
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
        
    <?php endif; ?>
<?php endif; ?>
