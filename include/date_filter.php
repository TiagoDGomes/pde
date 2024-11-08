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
    </script> 
</div>