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