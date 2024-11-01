<?php require 'base.php'; ?><!DOCTYPE html>
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
        <form>
            <header>
                <input type="text" name="q" id="q" autofocus value="<?= @$form_clear['q'] ?>">
                <?php HTMLUtil::generate_input_hidden(@$form_clear, ['q', 't', 'before', 'after']); ?>
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
                <?php include_once 'include/search_results.php'; ?>
            </section>
            <section class="content">                
                <?php include_once 'include/contents.php'; ?>                
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
        function show_user (){
            show_modal("#tuser")
        }
        function show_item (){
            show_modal("#titem")
        }
        function show_modal(queryElem) {
            var modal_window = document.createElement('div');
            var modal_content = document.createElement('div');
            var close_icon = document.createElement('span');
            var modal_body = document.createElement('div');
            var modal_header = document.createElement('div');
            var modal_footer = document.createElement('div');

            modal_window.className = 'modal';
            modal_content.className = 'modal-content';
            close_icon.className = 'close';
            close_icon.innerHTML = "&times;";
            close_icon.onclick = function () {
                modal_window.style.display = "none";
            }
            var elem_child = document.querySelector(queryElem).content.cloneNode(true);
            modal_header.className = 'modal-header';
            modal_body.className = 'modal-body';
            modal_footer.className = 'modal-footer';
            modal_body.appendChild(elem_child);
            //elem_child.style.display = 'block';

            modal_header.appendChild(close_icon);
            modal_content.appendChild(modal_header);
            modal_content.appendChild(modal_body);
            modal_content.appendChild(modal_footer);
            modal_window.appendChild(modal_content);
            document.body.appendChild(modal_window);
            modal_window.style.display = 'block';
            window.addEventListener('click', function (event) {
                if (event.target == modal_window) {
                    close_icon.onclick();
                }
            });
        }
    </script>
</body>

</html>