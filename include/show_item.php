<?php 


$query = "SELECT * FROM model m
            WHERE m.id = ?";
$selected_item = Database::fetch($query, array($form_clear['iid']));        

$query_search_loans = "SELECT m.id as model_id, 
                     m.name as model_name,
                     m.code as model_code, 
                     p.id as patrimony_id, 
                     max(n.tstamp) as loan_date, 
                     p.number1 as patrimony_number, 
                     p.number2 as patrimony_number2, 
                     p.obs as obs,
					 u.name as username,
					 u.code1 as code1,
					 u.code2 as code2,
                     p.loan_block as loan_block,
                     CASE WHEN 
                        p.loan_block = 1 or p.usable = 0  or p.found = 0 THEN 'blocked'
                        ELSE '' END AS icon_block,
                     p.serial_number as patrimony_serial_number, 
                     original_count,
                     CASE 
                        WHEN original_count - sum(diff) > original_count THEN original_count
                        ELSE original_count - sum(diff) END as count_returned, 
                     group_concat(details, '<br>') as all_details ,
                     n.id as loan_id
                FROM patrimony p 
                INNER JOIN model m ON (p.model_id = m.id)
                LEFT JOIN loan n ON (n.model_id = m.id and p.id = n.patrimony_id)
				LEFT JOIN user u ON (u.id = n.user_id)
                LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
                WHERE p.model_id =  ? 
                -- AND n.tstamp BETWEEN ? AND ?
                GROUP BY p.id
			    HAVING n.id = max(n.id) OR n.id IS NULL
                ORDER BY n.id DESC
        ";
        $current_date_after_1day =  (new DateTimeImmutable($current_date_after . ' +1 day'))->format('Y-m-d');     

        $params = array($form_clear['iid']);
        $selected_loans = Database::fetchAll($query_search_loans, $params);



?>

<template id="titem">
   <?php form_model($selected_item) ?>
</template>
<div class="card item top">
    <h2>
        <i class="icon item"></i>
        <?= $selected_item['name'] ?>        
    </h2>
    <p><button onclick="show_modal('#titem')">Editar</button></p>
</div>
<?php if ($selected_item['has_patrimony']): ?>
    <p><a href="javascript:;">Adicionar um novo patrimônio</a></p>
    <?php if (count($selected_loans)==0): ?>
        <p>Nenhum empréstimo foi encontrado.</p>
    <?php else: ?>    
        <div class="card items">
            <table>
                <thead>
                    <tr>                
                        <th><input disabled type="checkbox" name="chk_all"></th>
                        <th colspan="3">Nº Patr.</th>
                        <th>Data do último<br> empréstimo</th>
                        <th>Nome da pessoa</th>
                        <th>Cód. Ident. 1</th>
                        <th>Cód. Ident. 2</th>
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
                    <td><?= $item['username'] ?></td>
                    <td><?= $item['code1'] ?></td>    
                    <td><?= $item['code2'] ?></td>    
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
