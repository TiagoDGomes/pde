<?php require 'core.php'; ?><!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Empréstimos</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='main.css'>    
</head>
<body>
    <div id="main">
        <header id="content-input" class="mode <?= $_SESSION['selected_user'] ? 'code': 'user'?>">
            <?php if ($_SESSION['selected_user']):?>

            <div id="block-code">
                <form>
                    <input <?= $search_one_item ? '' : 'autofocus'?> type="text" name="code" id="code"  value="<?= htmlspecialchars(@$_GET['code']) ?>">
                </form>
            </div>
            <?php else:?>

            <div id="block-user">
                <form>
                    <input autofocus type="text" name="search_user" id="search_user" value="<?= htmlspecialchars(@$_GET['search_user']) ?>">
                </form>
            </div>
            <?php endif; ?>

        </header>

        <main>            
            <section class="left">
                
                <div class="user">
                    <?php if ($_SESSION['selected_user']):?>

                        <fieldset id="form_edit_user" style="display:none">
                            <legend>Solicitante:</legend>
                            <div class="already block">
                                <form method="POST">  
                                        <input type="hidden" name="save_edit_user" value="y">
                                        <div class="bar">                            
                                            <button>Salvar</button><br>       
                                            <button onclick="edit_user()">Cancelar</button>                            
                                        </div>
                                        <div class="form">
                                            <input type="hidden" name="id" id="id" value="<?= @$_SESSION['selected_user']['id'] ?>">
                                            <input type="text" name="name" id="name" value="<?= @$_SESSION['selected_user']['name'] ?>" placeholder="Nome"><br>
                                            <input type="text" name="code1" id="code1" value="<?= @$_SESSION['selected_user']['code1'] ?>" placeholder="Identificador adicional 1"><br>
                                            <input type="text" name="code2" id="code2" value="<?= @$_SESSION['selected_user']['code2'] ?>" placeholder="Identificador adicional 2">
                                        </div>  
                                    </form>    
                            </div>      

                        </fieldset>
                    <?php endif; ?>
                    <?php if (count($search_user_values) > 1): ?>

                        <fieldset>
                            <legend>Solicitantes registrados:</legend>
                            <?php foreach ($search_user_values as $item): ?>

                            <div class="already block">
                                <div class="bar">
                                    
                                </div>
                                <div class="form">                            
                                    <a href="?user=<?= @$item['id']; ?>"><?= $item['name']; ?></a><br>
                                    <small><?= @$item['code1']; ?> <?= @$item['code2']; ?></small>
                                </div>
                            </div>      

                            <?php endforeach; ?>

                        </fieldset>
                    <?php endif; ?>
                    <?php if ($action_search_user && !$_SESSION['selected_user']): ?>
                    
                        <fieldset>
                            <legend>Registrar novo solicitante:</legend>
                            <div class="new block">                                
                                <form method="POST">  
                                    <input type="hidden" name="save_new_user" value="y">
                                    <div class="bar">    
                                        <button>Salvar novo usuário</button>                            
                                    </div>
                                    <div class="form">
                                        <input type="text" name="name" id="name" value="<?= htmlspecialchars(@$_GET['search_user']) ?>">
                                        <input type="text" name="code1" id="code2" value="" placeholder="Identificador adicional 1"><br>
                                        <input type="text" name="code2" id="code2" value="" placeholder="Identificador adicional 2">
                                    </div>  
                                </form>                        
                            </div>  
                        </fieldset>                         
                    <?php endif; ?>

                </div>

                <?php if ($_SESSION['selected_user']): ?>
                    <pre><?php //var_dump($query); ?></pre>
                    <pre><?php // var_dump($params); ?></pre>
                    <pre><?php // var_dump($search_items_values); ?></pre>
                    <div class="item">
                        <?php if($action_search_code): ?>

                            <?php if (!$search_items_values) : ?>

                                <p>Nenhum item encontrado.</p>

                            <?php else:?> 

                                <fieldset>
                                    <legend>Itens registrados:</legend>
                                    <?php foreach ($search_items_values as $item): ?>
                                        <div class="block">
                                            <!-- <pre><?php //var_dump($item) ?></pre> -->
                                            <form method="POST" action=".">  
                                                <input type="hidden" name="loan_new_item" value="y">
                                                
                                                <div class="bar">        
                                                    <button <?= $search_one_item ? 'autofocus' : ''?>>Emprestar</button>                            
                                                </div>
                                                <div class="form">
                                                <input type="hidden" name="model_id" value="<?= $item['model_id'] ?>">
                                                <input type="hidden" name="patrimony_id" value="<?= $item['patrimony_id'] ?>">
                                                <input type="hidden" name="user_id" value="<?= $_SESSION['selected_user']['id'] ?>">
                                                    <textarea disabled name="item_name" id="item_name" placeholder="Nome do item"><?= $item['model_name'] ?></textarea><br>
                                                    <input maxlength="1" type="number" name="loan_diff" id="loan_diff" value="<?= $loan_multiplier ?>" placeholder="Quantidade (padrão: 1)">
                                                </div>  
                                            </form> 
                                        </div>
                                    <?php endforeach;?> 
                                </fieldset>

                            <?php endif;?> 

                            <p id="lnk-new-model-item">                        
                                <a href="javascript:;" onclick="show_new_model()">Registrar novo modelo de item...</a>
                            </p> 

                        <?php endif;?>       
                        <fieldset id="block-new-model-item" style="display:none">
                            <legend>Novo item:</legend>
                            <div class="new block">
                                <form method="POST" action=".">  
                                    <input type="hidden" name="save_item" value="y">
                                    <div class="bar">        
                                        <button>Salvar</button>                            
                                    </div>
                                    <div class="form">
                                        <input type="text" name="model_name" id="model_name" value="" placeholder="Nome do modelo do item"><br>
                                        <input type="hidden" name="model_id" id="model_id" value="">
                                        <input type="text" name="model_code" id="model_code" value="" placeholder="Identificador para busca rápida"><br>
                                        <input onclick="select_patrimony(1)" type="radio" name="model_unique" id="model_unique" value="1" checked><label for="unique">Modelo com identificador único (patrimoniado)</label><br>
                                        <input onclick="select_patrimony(0)" type="radio" name="model_unique" id="model_multiple" value="0"><label for="multiple">Modelo múltiplo (não patrimoniado)</label><br>
                                        <textarea name="unique_codes" id="unique_codes" rows="5" placeholder="Insira os patrimônios aqui (um por linha)."></textarea>
                                    </div>  
                                </form> 
                            </div>
                            <script>
                                function show_new_model(){
                                    document.getElementById('block-new-model-item').style.display = 'block';
                                    document.getElementById('lnk-new-model-item').style.display = 'none';
                                }
                                function select_patrimony(unique){
                                    document.getElementById('unique_codes').style.display = unique ? 'inline-block': 'none'
                                }
                            </script>
                        </fieldset>
                    </div>
                
                <?php endif;?>
                
            </section>
            <section class="right">
                <?php if ($_SESSION['selected_user']):?>
                <table class="items">
                    <tr>
                        <th>Nome do item</th>
                        <th>Código</th>
                        <th>Quantidade emprestada</th>
                        <th>Quantidade devolvida</th>
                        <th>Detalhes</th>
                    </tr>
                </table>
                <?php for ($i = 0; $i < 1; $i++): ?>
                <div class="date-items">
                    <div class="date">26/10/2024</div>            
                    <table class="items">
                        <?php for ($j = 0; $j < 6; $j++): ?>
                        <tr>
                            <td>Parafuso 1mm</td>
                            <td><?= ($j+1) ?></td>
                            <td>1</td>
                            <td>
                                <a href="#">-</a>
                                0
                                <a href="#">+</a>
                            </td>
                            <td></td>
                        </tr>
                        <?php endfor;?> 
                    </table>
                </div>
                <?php endfor;?>    
                <?php endif;?>
            </section>
        </main>
        <?php if ($_SESSION['selected_user']):?>
        <footer class="user">
            <i class="picture"></i>
            <span id="edit_name" contenteditable="false"><?= @$_SESSION['selected_user']['name'] ?></span>&nbsp;
            <!-- <small><span id="edit_code1" contenteditable="true" style="display:none"><?= @$_SESSION['selected_user']['code1'] ?></span></small>&nbsp;
            <small><span id="edit_code2" contenteditable="true" style="display:none"><?= @$_SESSION['selected_user']['code2'] ?></span></small>   -->
            <div class="bar">        
                <a id="lnk_edit_user" onclick="edit_user()" href="javascript:;">editar</a>
                <a style="display: none" id="lnk_save_user" onclick="save_user()" href="javascript:;">salvar</a> 
                <a href="?reset">trocar</a>                           
            </div>    
            <script>
                function edit_user(){
                    var form_edit_user = document.getElementById('form_edit_user');
                    form_edit_user.style.display = form_edit_user.style.display == 'none' ? 'block' : 'none';
                    return false;
                }
            </script>        
        </footer>
        <?php endif;?>
    </div>
    <script src='main.js'></script>

</body>
</html>