<?php isset($PDE) or die('Nope');

$search_results = array();

$su = strtoupper($current_query_string);
$query = "SELECT id, name, 
            code1, code2,
            0 as has_patrimony, 
            'user' as result_type,
            concat(code1, '<br>', code2) as obs 
            FROM user WHERE normalize(name) LIKE ? OR code1 = ? OR code2 = ?";
$params = array(normalize("%$su%"), $su, $su); 
$search_results = Database::fetchAll($query, $params);

if (count($search_results) == 1) {
    $search_one_item = TRUE;
}

$search_query_focus = (!$search_one_item || isset($form_clear['act']));
$selected_one_item = !$search_query_focus;

?>
<?php foreach ($search_results as $result): ?>
    <div class="card result user <?= $selected_one_item ? 'one': '' ?>" >
        <div class="title">                           
            <?php HTMLUtil::link_title_from_result($result) ?>
        </div>
        <div class="details">
            <?= $result['obs'] ?>
        </div> 
        <div class="actions">                          
            <form>
                <button <?= $selected_one_item ? 'autofocus': '' ?>>Selecionar</button>
                <input type="hidden" name="uid" value="<?= $result['id'] ?>">
            </form>            
        </div>
    </div>
<?php endforeach;  ?> 