<?php function form_user($user){ ?>
<fieldset id="<?= $user ? 'form_edit_user' : 'form_new_user' ?>" style="display:none">
    <legend>Solicitante:</legend>
    <div class="already block">
        <form method="POST">  
            <input type="hidden" name="<?= $user ? 'save_edit_user' : 'save_new_user' ?>" value="y">
            <input type="hidden" name="id" value="<?= @$user['id'] ?>">
            <dl>
                <dt>Nome:</dt>
                <dd><input type="text" name="name" id="name" value="<?= @$user['name'] ?>" placeholder="Nome"></dd>
                <dt>Identificador adicional 1:</dt>  
                <dd><input type="text" name="code1" id="code1" value="<?= @$user['code1'] ?>" placeholder="Identificador adicional 1"></dd>
                <dt>Identificador adicional 2:</dt>
                <dd><input type="text" name="code2" id="code2" value="<?= @$user['code2'] ?>" placeholder="Identificador adicional 2"></dd>
                <dt>&nbsp;</dt>
                <dd>                            
                    <button>Salvar</button>                                
                </dd>  
            </dl>              
        </form>    
    </div>
</fieldset>
<?php } ?>