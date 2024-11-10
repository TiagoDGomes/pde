<?php isset($PDE) or die('Nope');?>
<div class="filter">
    <form>    
        <?php if (isset($form_clear['uid'])): ?><input type="hidden" name="uid" value="<?= $form_clear['uid'] ?>"><?php endif; ?>
        <input type="hidden" name="q" value="<?= @$form_clear['q'] ?>">     
        <input type="hidden" name="t" value="<?= @$form_clear['t'] ?>">    
        <label>Entre:
            <input id="chk_date_before" onchange="date_change(this)" type="date" name="before" max="<?= $current_date_now ?>" value="<?=$current_date_before ?>">
        </label>        
        <label> e
            <input id="chk_date_after" onchange="date_change(this)" type="date" name="after" max="<?= $current_date_now ?>" value="<?= $current_date_after ?>">
        </label>
        <button>Filtrar</button>  
        <label><input <?= @$form_clear['hide_complete'] ? 'checked' : '' ?> type="checkbox" name="hide_complete" value="1" onclick="hide_completed(this.checked)">Ocultar devolvidos</label> 
    </form>     
    <script>
        function date_change(elem){
            var chk_date_before = document.getElementById('chk_date_before');
            var chk_date_after = document.getElementById('chk_date_after');
            if (elem == chk_date_before){
                chk_date_after.min = chk_date_before.value;
            } else {
                chk_date_before.max = chk_date_after.value;
            }
        }
        function hide_completed(val){
            var table = document.querySelector('table:has(tr.complete)');
            var urlPath;
            if (window.location.search == ''){
                urlPath = window.location.origin + window.location.pathname + '?'
            } else{
                urlPath = window.location.href.replace('&hide_complete=1','');
                urlPath = urlPath.replace('hide_complete=1','');
            }            
            if (val){
                table.classList.add('hide-completed');                                
                urlPath += '&hide_complete=1';
            } else {
                table.classList.remove('hide-completed');
            }
            window.history.replaceState({},"", urlPath);
        }
    </script> 
    
</div>