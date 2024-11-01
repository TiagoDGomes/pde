<?php


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
            <a  href="?uid=<?= $result['id'] ?>"><?= $result['name'] ?></a>
        <?php } else {  ?>
            <a onclick="show_item(<?= $result['model_id'] ?>);return false;" href="?iid=<?= $result['model_id'] ?>"><?= $result['name'] ?></a>
        <?php } 
    }
}