<?php isset($PDE) or die('Nope');

$query_string_full = explode("*", $current_query_string);
$query_string = normalize($query_string_full[0]);
if (isset($query_string_full[1])){
    if (!is_numeric($query_string_full[1]) || ($query_string_full[0] < $query_string_full[1])){
        $query_units = $query_string_full[0] > 0 ? $query_string_full[0] : 1;
        $query_string = normalize(@$query_string_full[1]);
    } else {                
        $query_units = $query_string_full[1];                               
    }
} else {
    $query_units = 1;
}
$query = "SELECT m.id as model_id, 
                    loan_block,
                    model_loan_block,
                    usable,
                    found,
                    m.name AS name, 
                    m.code AS model_code, 
                    has_patrimony, 
                    number1 as patrimony_number1,
                    number2 as patrimony_number2,                         
                    serial_number as patrimony_serial_number,
                    p.id AS patrimony_id,
                    sum(nn.diff) as loan_diff,
                    p.obs as obs,
                    max(n.id) as last_loan_id,
                    u.name AS last_user_name,
                    u.id AS last_user_id,                            
                    'patrimony' as result_type,
                    1 as query_units,
                    CASE WHEN number1 = ? THEN 1
                            WHEN number2 = ? THEN 1
                            WHEN serial_number = ? THEN 1
                            ELSE 0 END AS is_match,
                    n.id as loan_id
            FROM model m  
            LEFT JOIN patrimony p ON (p.model_id = m.id)
            LEFT JOIN loan n ON (n.patrimony_id = p.id AND m.id = n.model_id)
            LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
            LEFT JOIN user u ON (n.user_id = u.id)
            WHERE has_patrimony = 1 
                AND 
                    (m.code = ?                     
                    OR p.number1 = ? 
                    OR p.number2 = ? 
                    OR p.serial_number = ? 
                    OR normalize(m.name) LIKE ?
                    OR normalize(obs) LIKE ?)               
            GROUP BY m.id, p.id            
			HAVING n.id = max(n.id) OR n.id IS NULL OR n.id > 0
            UNION "; 
$query .= "SELECT m.id as model_id,
                    0 AS loan_block,
                    model_loan_block,
                    1 AS usable, 
                    1 AS found, 
                    m.name AS name, 
                    m.code AS model_code, 
                    has_patrimony, 
                    NULL as patrimony_number1,
                    NULL as patrimony_number2,                         
                    NULL as patrimony_serial_number,
                    NULL AS patrimony_id ,
                    sum(nn.diff) as loan_diff,
                    NULL as obs,                            
                    0 as last_loan_id,
                    NULL AS last_user_name,
                    NULL AS last_user_id,                            
                    'item' as result_type,
                    ? AS query_units,
                    0 as is_match,
                    n.id as loan_id
            FROM model m  
            LEFT JOIN loan n ON 
                (n.model_id = m.id)
            LEFT JOIN log_loan nn ON 
                (nn.loan_id = n.id)
            WHERE has_patrimony = 0 
                AND 
                    (m.code = ?   
                    OR normalize(m.name) LIKE ?) 
            GROUP BY m.id
            ORDER BY loan_id desc, is_match DESC, 
                has_patrimony DESC, 
                patrimony_number1, 
                patrimony_number2, 
                name,  
                patrimony_serial_number";
$params = array(
    $query_string, $query_string, $query_string, # CASE1 
    strtoupper($query_string),strtoupper($query_string),strtoupper($query_string),strtoupper($query_string), "%$query_string%","%$query_string%", # WHERE1
    $query_units, #SELECT2 query_units
    strtoupper($query_string), "%$query_string%" # WHERE2
);

$search_results = Database::fetchAll($query, $params);
if (count($search_results) == 1) {
    $search_one_item = TRUE;
}

$search_query_focus = (!$search_one_item || isset($form_clear['act']));
$selected_one_item = !$search_query_focus;

foreach ($search_results as $result): ?>
    
    <?php 
        $has_patrimony = $result['has_patrimony'] == 1; 
        $has_patrimony_number = $result['patrimony_number1'] > 0;
        $is_loaned = $has_patrimony_number && $result['loan_diff'] > 0;
        $loan_block = ($has_patrimony_number && $result['loan_block'] != 0) || $result['model_loan_block'] != 0;
        $found = !$has_patrimony_number || $result['found'] != 0;
        $usable = !$has_patrimony_number || $result['usable'] != 0;
        $has_user_last_loan = !is_null($result['last_user_name']);
        $allow_loan = (!$loan_block) && 
                        ($usable) && 
                        ($found) && 
                        ($current_user_id > 0) && 
                        (
                            ($has_patrimony && !$is_loaned && $has_patrimony_number) ||
                            (!$has_patrimony)
                        );
    ?>    

    <div class="card result item <?= $selected_one_item ? 'one': '' ?>">
        <div class="title">                           
            <?php HTMLUtil::link_title_from_result($result) ?>
        </div>
        <div class="details">
            <?= $result['obs'] ?>
        </div> 
        <?php if ($is_loaned && !$has_patrimony) : ?>
            <div class="message info">            
                Unidades deste item emprestadas: <?= $result['loan_diff'] ?>
            </div>
        <?php endif; ?>  
        <?php if ($loan_block) : ?>
            <div class="message alert">   
                <i class="icon blocked"></i>         
                Este item foi bloqueado para empréstimo.
            </div>
        <?php endif; ?>
        <?php if (!$usable) : ?>
            <div class="message alert"> 
                <i class="icon trash"></i>             
                Este item não é mais usável.
            </div>
        <?php endif; ?>
        <?php if (!$found) : ?>
            <div class="message alert"> 
                <i class="icon unknown"></i>             
                A localização deste item é desconhecida.
            </div>
        <?php endif; ?>
        <?php if (!$has_patrimony_number && $has_patrimony) : ?>
            <div class="message alert"> 
                <i class="icon tag-minus"></i>              
                Este modelo de item precisa ter etiquetas de identificação, mas nenhum número foi associado.
            </div>
            <div class="message info"> 
                <i class="icon tag-plus"></i>              
                
                <a href="?iid=<?= $result['model_id'] ?>">Editar item para exibir opções de identificação</a>
            </div>
            
        <?php endif; ?>

        <?php if ($has_user_last_loan) : ?>
            <div class="message <?= ($is_loaned) ? 'alert': 'info'?>">            
                <?php if ($is_loaned): ?>
                    <i class="icon cart"></i>
                    Emprestado para 
                <?php else: ?>
                    <i class="icon check"></i>
                    Última vez por 
                <?php endif; ?>
                <a target="_blank" href="?uid=<?= $result['last_user_id'] ?>"><?= $result['last_user_name'] ?></a>
            </div>
        <?php endif; ?>

        <div class="actions">            
            <form action="?">
                
                <?php $input_hidden = array(); ?>
                <?php $input_hidden['iid'] = $result['model_id']; ?> 
                <?php $input_hidden['uid'] = $current_user_id; ?> 
                <?php $input_hidden['t'] = $current_query_type_string; ?> 
                <?php $input_hidden['q'] = $current_query_string; ?> 
                <?php $input_hidden['redirect_to'] = 'user'; ?> 

                <?php if ($result['has_patrimony'] && $result['patrimony_id'] != ''): ?>

                    <?php $input_hidden['pid'] = $result['patrimony_id']; ?>    
                    <span class="identifier">

                    <?php HTMLUtil::render_patrimony($result['patrimony_id'], $result['patrimony_number1'] ); ?> 
                    <?php if ($result['patrimony_number2']) : ?>
                        <br><?php HTMLUtil::render_patrimony($result['patrimony_id'], $result['patrimony_number2'] ); ?>                    
                    <?php endif; ?>

                    </span>
                <?php endif; ?>    
                
                <?php if ($result['has_patrimony']) : ?>

                    <input type="hidden" name="units" value="1">

                <?php else: ?> 
                    <?php if ($current_user_id > 0): ?>   
                        <input type="number" name="units" value="<?= $query_units ?>" class="units"> &times;
                    <?php endif; ?> 
                <?php endif; ?>   

                <?php if ($is_loaned && $has_patrimony): ?>
                    <?php $input_hidden['act'] = 'ret'; ?> 
                    <?php $input_hidden['diff'] = '-1'; ?> 
                    <?php $input_hidden['nid'] = $result['last_loan_id']; ?> 
                    <button <?= $selected_one_item  ? 'autofocus': '' ?>>
                        <i class="icon check"></i>
                        Marcar como devolvido
                    </button>
                <?php else: ?> 
                                        
                        <?php $input_hidden['act'] = 'loan'; ?>                         
                        <button title="<?= !$current_user_id ? 'Você precisa escolher um usuário antes de emprestar': '' ?>" <?= $allow_loan ? '': 'disabled' ?> 
                                <?= $selected_one_item  ? 'autofocus': '' ?>
                            >
                            <i class="icon cart"></i>
                            Emprestar
                        </button>
                    
                <?php endif; ?>  
                <?php HTMLUtil::generate_input_hidden($input_hidden); ?>
            </form>
        </div>    
    </div>
<?php endforeach;  ?> 
