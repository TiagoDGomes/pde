<?php 


$query = "SELECT * FROM model m
            WHERE m.id = ?";
$selected_item = Database::fetch($query, array($form_clear['iid']));        
if ($selected_item['has_patrimony']){
    $query = "SELECT * FROM patrimony p
            INNER JOIN model m ON m.id = p.model_id
            WHERE m.id = ?";
    $patrimonies = Database::fetchAll($query, array($form_clear['iid']));    
}
?>

<h2>
    <i class="icon item"></i>
    <?= $selected_item['name'] ?>
</h2>
<pre><?php var_dump($selected_item); ?></pre>

<?php if ($selected_item['has_patrimony']): ?>

<pre><?php var_dump($patrimonies); ?></pre>

<?php endif; ?>