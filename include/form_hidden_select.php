<form id="form_hidden_select" method="">
    <input type="hidden" name="multiact" value="">
    <?php if (isset($form_clear['uid'])): ?><input type="hidden" name="uid" value="<?= $form_clear['uid'] ?>"><?php endif; ?>  
    <input type="hidden" name="q" value="<?= @$form_clear['q'] ?>">     
    <input type="hidden" name="t" value="<?= @$form_clear['t'] ?>">  
</form>