<?php
$selected_patrimony = NULL;

$query = "SELECT * FROM patrimony p
            INNER JOIN model m ON m.id = p.model_id
            WHERE p.id = ?";
$selected_patrimony = Database::fetch($query, array($form_clear['pid']));        


$query_search_loans = "SELECT 
                     u.name AS username,
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
                     n.id as loan_id
                FROM loan n
                INNER JOIN log_loan nn ON (nn.loan_id = n.id)
                INNER JOIN user u ON (u.id = n.user_id)
                LEFT JOIN patrimony p ON (n.patrimony_id = p.id)
                WHERE p.id = ?
                GROUP BY n.id
                ORDER BY n.tstamp DESC
        ";
        $current_date_after_1day =  (new DateTimeImmutable($current_date_after . ' +1 day'))->format('Y-m-d');     

        $params = array($form_clear['pid']);
        $selected_loans = Database::fetchAll($query_search_loans, $params);


?>
<?php if (!$selected_patrimony): ?>
    <p>Patrimônio não encontrado.</p>
<?php else: ?>
    <div class="card item top">
        <h2>   
            <i class="icon item"></i>
            <a href="?iid=<?= $selected_patrimony['model_id'] ?>"><?= $selected_patrimony['name'] ?></a> &gt;
            <?php HTMLUtil::render_patrimony(NULL, $selected_patrimony['number1']) ; ?>
            <?php $selected_patrimony['number2'] ? HTMLUtil::render_patrimony(NULL, $selected_patrimony['number2']) : '' ; ?>
        </h2>
        <div class="details"> 
        
            <p><?= $selected_patrimony['obs'] ?></p>         

        </div>
        <p class="bar"><button>Editar</button></p>
    </div>

    <?php if (count($selected_loans)==0): ?>
        <p>Nenhum empréstimo foi encontrado.</p>
    <?php else: ?>  
    <div class="card items">
        <h3>Empréstimos deste patrimônio</h3>
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
                        <td class="number"><?= $item['loan_date'] ?></td>
                        <td></td>            
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
<?php endif; ?>
