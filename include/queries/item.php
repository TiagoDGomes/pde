<?php isset($PDE) or die('Nope');

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
                     p.patrimony_location as patrimony_location,
                     m.model_location as model_location,
                     u.id as uid,
                     u.name as username,
                     u.code1 as code1,
                     u.code2 as code2,
                     p.loan_block as loan_block,
                     CASE WHEN 
                        p.loan_block = 1 or p.usable = 0  or p.found = 0 THEN 'blocked'
                        ELSE 'ok' END AS icon_block,
                     CASE WHEN 
                        p.usable = 1  THEN 'usable'
                        ELSE 'trash' END AS icon_usable,
                     CASE WHEN 
                        p.found = 1  THEN 'found'
                        ELSE 'unknown' END AS icon_found,
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
			   -- HAVING n.id = max(n.id) OR n.id IS NULL
                ORDER BY n.id DESC
        ";
        $current_date_after_1day =  (new DateTimeImmutable($current_date_after . ' +1 day'))->format('Y-m-d');     

        $params = array($form_clear['iid']);
        $selected_loans = Database::fetchAll($query_search_loans, $params);
