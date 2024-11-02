<?php foreach ($search_results as $result): ?>
    <div class="result user">
        <div class="title">                           
            <?php HTMLUtil::link_title_from_result($result) ?>
        </div>
        <div class="details">
            <?= $result['obs'] ?>
        </div> 
        <div class="actions">
            <?php if ($is_search_type_user): ?>                
                <form>
                    <button>Selecionar</button>
                    <input type="hidden" name="uid" value="<?= $result['id'] ?>">
                </form>
            <?php endif;  ?> 
        </div>
    </div>
<?php endforeach;  ?> 