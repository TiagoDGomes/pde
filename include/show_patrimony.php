<?php
$selected_patrimony = NULL;

$query = "SELECT * FROM patrimony p
            INNER JOIN model m ON m.id = p.model_id
            WHERE p.id = ?";
$selected_patrimony = Database::fetch($query, array($form_clear['pid']));        

?>
<h2>
   <?php HTMLUtil::render_patrimony(NULL, $selected_patrimony['number1']) ; ?>
   <?= $selected_patrimony['name'] ?>
</h2>
<pre><?php var_dump($selected_patrimony); ?></pre>
