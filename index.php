<?php $PDE = 1; require 'base.php'; ?><!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='style.css'>  
    <link rel='stylesheet' type='text/css' media='screen' href='modal.css'>  
    <title>Empréstimos</title>    
</head>

<body>
    <div id="main" style="display: none;">
        <form action="?">
            <header>
                <input type="text" name="q" id="q" <?= $search_query_focus ? 'autofocus': '' ?> value="<?= @$form_clear['q'] ?>">
                <?php HTMLUtil::generate_input_hidden(array('uid' => $current_user_id)); ?>
                <ul>
                    <li>
                        <input type="radio" name="t" value="user" id="chk_user" <?= $option_search_user_checked ?>>
                        <label for="chk_user">pessoa</label>
                    </li>
                    <li>
                        <input type="radio" name="t" value="item" id="chk_item" <?= $option_search_item_checked ?>>
                        <label for="chk_item">item</label>
                    </li>
                    <li>
                        <button type="submit">
                            <i class="icon search"></i>
                            Procurar
                        </button>
                    </li>                    
                </ul>
                
            </header>
        </form>
        <main>
            <section class="search_results">  
                <?php if ($is_search_type_user): ?>
                    <?php include_once 'include/search_results_user.php'; ?>
                <?php else: ?>  
                    <?php include_once 'include/search_results_item.php'; ?>
                <?php endif; ?>      
            </section>
            <section class="content">  
                <?php if ($is_show_patrimony): ?> 
                    <?php include_once 'include/show_patrimony.php'; ?> 
                <?php else: ?>    

                    <?php include_once 'include/show_user.php'; ?>   
                <?php endif; ?>             
            </section>
        </main>
        <footer>
            <a href="javascript:show_user()">Mostrar usuário</a> &bullet;
            <a href="javascript:show_item()">Mostrar item</a> &bullet;
            <a href="javascript:show_patrimony()">Mostrar patrimônio</a>
        </footer>
        
    </div>
    <template id="tuser">
        <fieldset>
            <legend>Usuário</legend>
            <dl>
                <dt>Nome:</dt>
                <dd>aaaa</dd>
                <dt>&nbsp;</dt>
                <dd>                            
                    <button>Salvar</button>                                
                </dd>
            </dl>
        </fieldset>
    </template>
    <template id="titem">
        <fieldset>
            <legend>Item</legend>
            <dl>
                <dt>Nome:</dt>
                <dd></dd>
                <dt>&nbsp;</dt>
                <dd>                            
                    <button>Salvar</button>                                
                </dd>
            </dl>
        </fieldset>
    </template>
    <noscript>
        <p>O Javascript não está disponível em seu navegador.</p>
    </noscript>
    <script>
        document.getElementById('main').style.display = 'block';                
    </script>
    <?php if ($search_query_focus): ?>    
        <script>
            setTimeout(function(){
                console.log(document.activeElement);
                document.activeElement.select();
            }, 500);
        </script>
    <?php endif; ?>    
    <script src='script.js'></script>
</body>

</html>