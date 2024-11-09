<?php isset($PDE) or die('Nope');

header('Content-Type: application/json');  

if ($is_selecting_user){
    include 'include/queries/user_loans.php';
    $response = array("user"=> $selected_user,"loans" => $selected_user_loans);
} else if ($is_show_item){
    include 'include/queries/item.php';
    $response = array("item"=> $selected_item,"loans" => $selected_loans);
} else if ($is_show_patrimony){
    include 'include/queries/patrimony.php';
    $response = array("patrimony"=> $selected_patrimony,"loans" => $selected_loans);
}



HTTPResponse::JSON($response);