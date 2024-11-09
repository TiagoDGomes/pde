<?php
isset($PDE) or die('Nope');

class HTMLUtil{
    public static function generate_input_hidden($arr, $arr_key_ignore = array()){
        foreach ($arr as $key => $value){
            if (! in_array($key, $arr_key_ignore)) {
                ?>

                <input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
    
                <?php
            }            
        } 
               
    }
    public static function link_title_from_result($result){
        if ($result['result_type'] == 'user'){ ?>
            <a href="?uid=<?= $result['id'] ?>"><?= $result['name'] ?></a>
        <?php } else {  ?>
            <a href="?iid=<?= $result['model_id'] ?>"><?= $result['name'] ?></a>
        <?php } 
    }
    public static function render_counter($item,
                                        $current_user_id, 
                                        $current_query_type_string,
                                        $current_query_string ){
        ?>
        <?php $input_hidden = array(); ?>
        <?php $input_hidden['iid'] = $item['model_id']; ?> 
        <?php $input_hidden['uid'] = $current_user_id; ?> 
        <?php $input_hidden['t'] = $current_query_type_string; ?> 
        <?php $input_hidden['q'] = $current_query_string; ?> 
        <?php $input_hidden['redirect_to'] = 'user'; ?> 
        <?php $input_hidden['act'] = 'ret'; ?> 
        <?php $input_hidden['nid'] = $item['loan_id']; ?>  

        <?php $minus_style_display = ($item['count_returned'] >  0 ) ? 'inline': 'none'  ?>   
        <?php $plus_style_display = ($item['count_returned'] <  $item['original_count']) ? 'inline': 'none'  ?>   


        <small class="counter">
            <span id="count_returned_<?= $item['loan_id']; ?>">
                <?= $item['count_returned'] ?>
            </span> /
            <span id="original_count_<?= $item['loan_id']; ?>"><?=$item['original_count'] ?></span>
        </small>
        <br>
        <?php if ($item['original_count'] > 1): ?>
        <span class="cell return">
            <a style="display: <?= $minus_style_display ?>"  
                    id="reset_<?= $item['loan_id']; ?>" 
                    onclick="save_loan_all_reset(<?= $item['loan_id']; ?>);return false;" 
                    title="Resetar tudo" 
                    href="javascript:;">
                    <i class="icon refresh"></i>
            </a>        
        </span>
        <?php endif; ?>
        <span class="cell return">                                    
            <?php $input_hidden['diff'] = '1'; ?> 
            <a style="display: <?= $minus_style_display ?>" 
                id="minus_<?= $item['loan_id']; ?>" 
                onclick="save_loan_values(<?= $item['loan_id']; ?>,1);return false;" 
                title="1 unidade a dever" 
                href="?<?= http_build_query($input_hidden) ?>">
                <i class="icon minus"></i>
            </a> 
        </span>
        <span class="cell return">                                    
            <?php $input_hidden['diff'] = '-1'; ?> 
            <a style="display: <?= $plus_style_display ?>"  
                id="plus_<?= $item['loan_id']; ?>" 
                onclick="save_loan_values(<?= $item['loan_id']; ?>,-1);return false;" 
                title="1 unidade devolvida" 
                href="?<?= http_build_query($input_hidden) ?>">
                <i class="icon plus"></i>
            </a>           
        </span>
        <?php if ($item['original_count'] > 1): ?>
        <span class="cell return">
            <a style="display: <?= $plus_style_display ?>"  
                    id="complete_<?= $item['loan_id']; ?>" 
                    onclick="save_loan_all_complete(<?= $item['loan_id']; ?>);return false;" 
                    title="Devolver tudo" 
                    href="javascript:;">
                    <i class="icon complete"></i>
            </a>        
        </span>
        <?php endif; ?>      

        <?php 
    }
    public static function render_patrimony($id, $number){ ?>
        <?php if (!is_null($id)) : ?>

            <a href="?pid=<?= $id ?>">
                
        <?php endif; ?>

        <span class="patrimony">
            <i class="icon pat"></i> 
            <?= $number ?>

        </span>

        <?php if (!is_null($id)) : ?>

            </a>

        <?php endif; ?>

    <?php }
}