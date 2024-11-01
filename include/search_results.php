<?php require_once __DIR__ . '/../base.php'; ?>
<?php foreach ($search_results as $result): ?>
    <?php if ($is_search_type_user){
        $is_loaned = FALSE;
        $has_user_last_loan = FALSE;
        $has_patrimony = FALSE;
        $has_patrimony_number = FALSE;
    } else {
        $is_loaned = $result['loan_diff'] > 0;
        $has_user_last_loan = !is_null($result['last_user_name']);
        $has_patrimony = $result['has_patrimony'] == 1; 
        $has_patrimony_number = $result['patrimony_number1'] > 0;
        $allow_loan = ($current_user_id > 0) && (
            ($has_patrimony && !$is_loaned && $has_patrimony_number) ||
            (!$has_patrimony)
        ) ;
    }?> 
    

    <div class="result <?= $is_search_type_user ? 'user' : 'item' ?>">
        <div class="title">                           
            <?php HTMLUtil::link_title_from_result($result) ?>
        </div>
        <div class="details">
            <?= $result['obs'] ?>
        </div>   

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
                <a href="?uid=<?= $result['last_user_id'] ?>"><?= $result['last_user_name'] ?></a>
            </div>
        <?php endif; ?>

        <div class="actions">
            <?php if ($is_search_type_user): ?>
                <form>
                    <button>Editar</button>
                    <input type="hidden" name="act" value="edit">
                    <input type="hidden" name="uid" value="<?= $result['id'] ?>">
                </form>
                <form>
                    <button>Selecionar</button>
                    <input type="hidden" name="uid" value="<?= $result['id'] ?>">
                </form>

            <?php else: ?>
                <form action="?">
                    
                    <?php //var_dump($result); ?>
                    <?php $input_hidden = array(); ?>
                    <?php $input_hidden['iid'] = $result['model_id']; ?> 
                    <?php $input_hidden['uid'] = $current_user_id; ?> 
                    <?php $input_hidden['t'] = $current_query_type_string; ?> 
                    <?php $input_hidden['q'] = $current_query_string; ?> 

                    <?php if ($result['has_patrimony'] && $result['patrimony_id'] != ''): ?>
                        <?php $input_hidden['pid'] = $result['patrimony_id']; ?>                        
                        <a href="?pid=<?= $result['patrimony_id'] ?>">
                            <span class="patrimony">
                                <i class="icon pat"></i> 
                                <?= $result['patrimony_number1'] ?>
                            </span>
                        </a>
                    <?php endif; ?>    
                          
                    <input <?= $has_patrimony  ? 'disabled':'' ?> 
                                class="units" 
                                name="units" 
                                type="number" 
                                value="<?= $result['has_patrimony'] ? 1 : $query_units?>"> &times;

                    <?php if ($is_loaned): ?>
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
            <?php endif; ?>
        </div>    
    </div>
<?php endforeach;  ?> 

























<template>    
                <div class="result item">
                    <div class="title">
                        <i class="icon item"></i>
                        <a href="?iid=1234">Velit luctus convallis velit donec rutrum accumsan iaculis</a>
                    </div>
                    <div class="details">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent purus neque, venenatis sit
                        amet
                        velit vitae, luctus convallis velit. Donec rutrum accumsan iaculis. Curabitur feugiat rhoncus
                        metus
                        ac vehicula.
                    </div>
                    <div class="actions">
                        <form>
                            <input type="hidden" name="a" value="loan">
                            <input type="hidden" name="q" value="">
                            <input type="hidden" name="iid" value="1234">
                            <input type="hidden" name="uid" value="123">
                            <input name="un" class="units" name="units" type="number" value="1"> &times;
                            <button>
                                <i class="icon cart"></i>
                                Emprestar
                            </button>
                        </form>
                    </div>
                </div>
                <div class="result item">
                    <div class="title">
                        <i class="icon item"></i>
                        <a href="?i=456&p=12345">Taciti sociosqu ad litora torquent per conubia
                            nostra
                        </a>
                    </div>
                    <div class="details">
                        Fusce semper mi a nisl pulvinar iaculis. Vestibulum ante ipsum primis in faucibus orci luctus et
                        ultrices posuere cubilia curae; Donec iaculis pharetra lorem eu dignissim. Nam eget bibendum
                        dui,
                        nec fermentum erat.
                    </div>
                    <div class="alert">
                        <i class="icon cart"></i>
                        Emprestado para <a href="?uid=1">FULANO BELTRANO</a>
                    </div>
                    <div class="actions"></div>
                    <div class="actions">
                        <form>
                            <a href="?pid=12345">
                                <span class="patrimony"><i class="icon pat"></i> 12345</span>
                            </a>
                            <input type="hidden" name="act" value="ret">
                            <input type="hidden" name="q" value="">
                            <input type="hidden" name="uid" value="123">
                            <input type="hidden" name="pid" value="12345">
                            <input disabled class="units" name="units" type="number" value="1"> &times;
                            <button>
                                <i class="icon check"></i>
                                Marcar como devolvido
                            </button>
                        </form>
                    </div>

                </div>
                <div class="result item">
                    <div class="title">
                        <i class="icon user"></i>
                        <a href="?iid=4567">
                            Aptent taciti sociosqu ad litora torquent per conubia
                            nostra
                        </a>
                    </div>
                    <div class="details"></div>
                    <div class="actions"></div>
                    <div class="actions">
                        <a href="?pid=23456">
                            <span class="patrimony"><i class="icon pat"></i> 23456</span>
                        </a>
                        <input disabled class="units" name="units" type="number" value="1"> &times;
                        <button>
                            <i class="icon cart"></i>
                            Emprestar
                        </button>
                    </div>
                </div>

                <div class="result user">
                    <div class="title">
                        <i class="icon user"></i>
                        <a href="?uid=2">

                            Lorem ipsum dolor
                        </a>
                    </div>
                    <div class="details">

                    </div>
                    <div class="actions"></div>
                    <div class="actions">
                        <form>
                            <button>Editar</button>
                            <input type="hidden" name="act" value="edit">
                            <input type="hidden" name="uid" value="2">
                        </form>
                        <form>
                            <button>Selecionar</button>
                            <input type="hidden" name="act" value="select">
                            <input type="hidden" name="uid" value="2">
                        </form>
                    </div>
                </div>
                </template>    