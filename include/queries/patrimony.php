<?php isset($PDE) or die('Nope');

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
