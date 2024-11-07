<?php $PDE = 1; require 'base.php'; ?><!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='style.css'>  
    <link rel='stylesheet' type='text/css' media='screen' href='modal.css'>  
    <title><?= $page_title ?></title>    
</head>

<body>
    <div id="main">
        <form action="?install">
            <header>
                <input type="text" name="q" id="q" value="<?= @$form_clear['q'] ?>">
                <?php HTMLUtil::generate_input_hidden(array('uid' => $current_user_id)); ?>
                
                <ul>
                    <li>
                        <button type="submit">
                            <i class="icon search"></i>
                            Procurar
                        </button>
                    </li>
                    <li><span>Mostrar primeiro: </span></li>
                    <li>
                        <input type="radio" name="t" value="user" id="chk_user" <?= $option_search_user_checked ?>>
                        <label for="chk_user">pessoas</label>
                    </li>
                    <li>
                        <input type="radio" name="t" value="item" id="chk_item" <?= $option_search_item_checked ?>>
                        <label for="chk_item">itens</label>
                    </li>
                    <li>|</li>
                    <li><a href=".">Home</a></li>                    
                </ul>
                
            </header>
        </form>
        <main>
            <section class="search_results">
                <?php if ($is_searching && !$is_install): ?>  
                    <?php if ($is_search_type_user): ?>
                        <?php include_once 'include/search_results_user.php'; ?>
                        <?php include_once 'include/search_results_item.php'; ?>
                    <?php else: ?>  
                        <?php include_once 'include/search_results_item.php'; ?>
                        <?php include_once 'include/search_results_user.php'; ?>                        
                    <?php endif; ?>    
                <?php endif; ?>   
            </section>
            <section class="content">  
                <?php if ($is_install): ?> 
                    <?php include_once 'include/form_install.php'; ?>                 
                <?php elseif ($is_show_patrimony): ?> 
                    <?php include_once 'include/show_patrimony.php'; ?> 
                <?php elseif ($is_show_item) : ?>
                    <?php require_once 'include/form_generic.php'; ?>
                    <?php require_once 'include/form_model.php'; ?>
                    <?php include_once 'include/show_item.php'; ?>   
                <?php elseif ($last_user_selected) : ?>
                    <?php require_once 'include/form_generic.php'; ?>
                    <?php require_once 'include/form_user.php'; ?>
                    <?php include_once 'include/show_user.php'; ?>   
                <?php else: ?>
                    <?php include_once 'include/show_default.php'; ?>   
                <?php endif; ?>             
            </section>
        </main>
        <footer>
            <?php if ($is_searching): ?>   
                <?php require_once 'include/form_generic.php'; ?>
                <?php require_once 'include/form_user.php'; ?>
                <?php require_once 'include/form_model.php'; ?>
                <template id="tnewuser">
                    <?php form_user(NULL); ?>  
                </template>             
                <template id="tnewitem">
                    <?php form_model(NULL); ?>  
                </template>             
                <a href="javascript:show_modal('#tnewuser')">Adicionar usuário</a> &bullet;
                <a href="javascript:show_modal('#tnewitem')">Adicionar item</a>
            <?php endif; ?>
        </footer>        
    </div>

    
    <noscript>
        <div class="modal">
            <div class="modal-content">
                <div class="modal-header"><span class="close">×</span></div>
                <div class="modal-body">
                    <p style="text-align:center">
                        Para utilizar este sistema, você precisar ativar o suporte a Javascript em seu navegador.
                    </p>    
                </div>
                <div class="modal-footer"></div>
            </div>            
        </div>        
    </noscript>    
    <?php if ($search_query_focus || !$is_searching): ?>    
        <script>
            setTimeout(function(){           
                var q = document.getElementById("q"); 
                q.focus();    
                q.select();
            }, 300);
        </script>
    <?php endif; ?>    
    <script src='script.js'></script>
</body>

</html>