<?php isset($PDE) or die('Nope');?> 

<?php foreach ($search_results as $result): ?>
    
    <?php 
        $has_patrimony = $result['has_patrimony'] == 1; 
        $has_patrimony_number = $result['patrimony_number1'] > 0;
        $is_loaned = $has_patrimony_number && $result['loan_diff'] > 0;
        $loan_block = $has_patrimony_number && $result['loan_block'] != 0;
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

    <div class="result item">
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
                Este item foi bloqueado para empréstimo.
            </div>
        <?php endif; ?>
        <?php if (!$usable) : ?>
            <div class="message alert">            
                Este item não é mais usável.
            </div>
        <?php endif; ?>
        <?php if (!$found) : ?>
            <div class="message alert">            
                A localização deste item é desconhecida.
            </div>
        <?php endif; ?>
        <?php if (!$has_patrimony_number && $has_patrimony) : ?>
            <div class="message alert">            
                Este item é um item patrimoniado mas não tem nenhum número associado.
                <a href="javascript:;">Adicionar números</a>
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
                
                <?php //var_dump($result); ?>
                <?php $input_hidden = array(); ?>
                <?php $input_hidden['iid'] = $result['model_id']; ?> 
                <?php $input_hidden['uid'] = $current_user_id; ?> 
                <?php $input_hidden['t'] = $current_query_type_string; ?> 
                <?php $input_hidden['q'] = $current_query_string; ?> 

                <?php if ($result['has_patrimony'] && $result['patrimony_id'] != ''): ?>

                    <?php $input_hidden['pid'] = $result['patrimony_id']; ?>    

                    <?php HTMLUtil::render_patrimony($result['patrimony_id'], $result['patrimony_number1'] ); ?>                    

                <?php endif; ?>    
                
                <?php if ($result['has_patrimony']) : ?>

                    <input type="hidden" name="units" value="1">

                <?php else: ?> 

                    <input type="number" name="units" value="<?= $query_units ?>" class="units"> &times;

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
                    <button <?= $allow_loan ? '': 'disabled' ?> 
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
