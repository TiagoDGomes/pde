<?php isset($PDE) or die('Nope');?>
<header></header>
<main>
<section class="content">
    <div class="modal login">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body">
                <form method="post">
                 <fieldset>
                    <legend>Autenticação</legend>
                    <dl>
                        <dt>Login:</dt>
                        <dd><input type="text" name="login"></dd>
                        <dt>Senha:</dt>
                        <dd><input type="password" name="password"></dd>
                        <dt>&nbsp;</dt>
                        <dd><input type="submit" value="Entrar"></dd>
                    </dl>
                 </fieldset>  
                 </form>
            </div>
            <div class="modal-footer"></div>
        </div>            
    </div>  
</section>
</main>
<footer></footer>