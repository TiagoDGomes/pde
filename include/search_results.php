<?php require_once __DIR__ . '/../base.php'; ?>
<?php foreach ($search_results as $result): ?>
    <div class="result <?= $is_search_type_user ? 'user' : 'item' ?>">
        <div class="title">                           
            <?php HTMLUtil::link_title_from_result($result) ?>
        </div>
        <div class="details">
            <?= $result['obs'] ?>
        </div>   
        <?php if (isset($result['last_user_name'])) : ?>
        <div class="alert">
            <i class="icon cart"></i>
            Emprestado para <a href="?uid=<?= $result['last_user_id'] ?>"><?= $result['last_user_name'] ?></a>
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
                <form>
                    <?php HTMLUtil::generate_input_hidden($form_clear,['act','before','after','iid','pid','units']); ?>

                    <input type="hidden" name="iid" value="<?= $result['model_id'] ?>"> 

                    <?php if ($result['has_patrimony'] && $result['patrimony_id'] != ''): ?>
                        <input type="hidden" name="pid" value="<?= $result['patrimony_id'] ?>">
                        <a href="?pid=<?= $result['patrimony_id'] ?>">
                            <span class="patrimony"><i class="icon pat"></i> <?= $result['patrimony_number1'] ?></span>
                        </a>
                    <?php endif; ?>    
                        
                    <input <?= $result['has_patrimony']  ? 'disabled':'' ?> 
                                class="units" 
                                name="units" 
                                type="number" 
                                value="<?= $result['has_patrimony'] ? 1 : $query_units?>"> &times;

                    <?php if ($result['last_loan_id']): ?>
                        <input type="hidden" name="act" value="ret">
                        <button>
                            <i class="icon check"></i>
                            Marcar como devolvido
                        </button>
                    <?php else: ?> 
                        <input type="hidden" name="act" value="loan">
                        <button <?= ($result['has_patrimony'] && $result['patrimony_id'] == '' ? 'disabled':'') ?>>
                            <i class="icon cart"></i>
                            Emprestar
                        </button>
                    <?php endif; ?>  
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