<!DOCTYPE html>
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
                <input type="text" name="q" id="q" autofocus>
                <ul>
                    <li>
                        <button type="submit">
                            <i class="icon search"></i>
                            Procurar
                        </button>
                    </li>
                    <li>
                        <input checked type="radio" name="t" value="user" id="chk_user">
                        <label for="chk_user">pessoa</label>
                    </li>
                    <li>
                        <input type="radio" name="t" value="item" id="chk_item">
                        <label for="chk_item">item</label>
                    </li>
                    <li>
                        <label for="chk_date_before">Entre:</label>
                        <input type="date" name="db" value="2024-01-01" min="2024-01-01" id="chk_date_before">
                        <label for="chk_date_after">a</label>
                        <input type="date" name="da" value="2024-10-30" max="2024-10-30" id="chk_date_after">
                    </li>

                </ul>

            </header>
        </form>
        <main>
            <section class="search_results">
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


            </section>
            <section class="content">
                <h2>Fulano Beltrano</h2>
                <div class="items">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="chk_all"></th>
                                <th>Nome do item</th>
                                <th>Código</th>
                                <th>Patrimônio</th>
                                <th>Quantidade emprestada</th>
                                <th>Quantidade devolvida</th>
                                <th>Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="date">
                                <th>
                                    <input class="loan_top_checkbox" type="checkbox" id="loan_date_30_10_2024">
                                    </td>
                                <th colspan="6">
                                    <label for="loan_date_30_10_2024" class="date">30/10/2024</label>
                                </th>
                            </tr>
                            <tr class="remaining">
                                <td>
                                    <input type="checkbox" id="loan_98">
                                </td>
                                <td>
                                    <label for="loan_98">circuito integrado 74123</label>
                                </td>
                                <td class="number">2</td>
                                <td class="number">
                                    <span class="patrimony"><i class="icon pat"></i> 3</span>
                                </td>
                                <td class="number">4</td>
                                <td class="number return">
                                    <span class="return">

                                        <a href="?user_id=2&amp;log_loan=y&amp;loan_id=48&amp;diff=-1&amp;code=mult">
                                            <span class="button-minus">-</span>
                                        </a>

                                    </span>
                                    4
                                    <span class="return">

                                        <a href="?user_id=2&amp;log_loan=y&amp;loan_id=48&amp;diff=1&amp;code=mult">
                                            <span class="button-plus">+</span>
                                        </a>

                                    </span>

                                </td>
                                <td class="details" contenteditable="true"> </td>
                            </tr>
                            <tr class="complete">
                                <td>
                                    <input type="checkbox" id="loan_85">
                                </td>
                                <td>
                                    <label for="loan_85">Paquímetro Universal 150mm, aço inox</label>
                                </td>
                                <td></td>
                                <td class="number">
                                    <span class="patrimony"><i class="icon pat"></i> 11230261</span>
                                </td>
                                <td class="number">1</td>
                                <td class="number return">
                                    <span class="return">
                                        <a href="?user_id=2&amp;log_loan=y&amp;loan_id=84&amp;diff=-1&amp;code=mult">
                                            <span class="button-minus">-</span>
                                        </a>
                                    </span>
                                    1
                                    <span class="return">
                                    </span>

                                </td>
                                <td class="details" contenteditable="true">Proin urna nibh, maximus non lacus in, congue
                                    cursus est. Morbi nec
                                    consequat est. Sed leo mauris,
                                    gravida ut est id, finibus sagittis massa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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
    <noscript>
        <p>O Javascript não está disponível em seu navegador.</p>
    </noscript>
    <script>
        document.getElementById('main').style.display = 'block';
        function show_user (){
            show_modal("#tuser")
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