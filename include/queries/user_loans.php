<?php isset($PDE) or die('Nope');

$query = "SELECT * FROM user u
            WHERE u.id = ?";
$selected_user = Database::fetch($query, array($form_clear['uid']));    

$query_search_user_loans = "SELECT m.id as model_id, 
                    m.name as model_name,
                    m.code as model_code, 
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
            INNER JOIN model m ON (n.model_id = m.id)
            INNER JOIN log_loan nn ON (nn.loan_id = n.id )
            LEFT JOIN patrimony p ON (n.patrimony_id = p.id AND m.id = p.model_id)
            WHERE n.user_id =  ? AND n.tstamp BETWEEN ? AND ?
            GROUP BY n.id
            ORDER BY n.tstamp DESC
            ";
$current_date_after_1day =  (new DateTimeImmutable($current_date_after . ' +1 day'))->format('Y-m-d');     

$params = array($form_clear['uid'], $current_date_before, $current_date_after_1day);
$selected_user_loans = Database::fetchAll($query_search_user_loans, $params);

