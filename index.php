<?php require 'core.php'; ?><!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Empréstimos</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='main.css'>    
</head>
<body>
    <div id="main">
        <header id="content-input" class="mode <?= $selected_user ? 'code': 'user'?>">
            <?php if ($selected_user):?>

                <div class="display-user">
                    <i class="picture"></i>
                    <span id="edit_name" contenteditable="false"><?= $selected_user['name'] ?></span>&nbsp;
                    <!-- <small><span id="edit_code1" contenteditable="true" style="display:none"><?= $selected_user['code1'] ?></span></small>&nbsp;
                    <small><span id="edit_code2" contenteditable="true" style="display:none"><?= $selected_user['code2'] ?></span></small>   -->
                    <div class="bar">        
                        <a id="lnk_edit_user" onclick="edit_user()" href="javascript:;">editar</a>
                        <a style="display: none" id="lnk_save_user" onclick="save_user()" href="javascript:;">salvar</a> 
                        <a href="?reset">trocar</a>                           
                    </div>
                </div>
                <div id="block-code">
                    <form>
                        <input <?= $search_one_item ? '' : 'autofocus'?> type="text" name="code" id="code"  value="<?= htmlspecialchars(@$_GET['code']) ?>">
                    </form>
                </div>
            <?php else:?>

                <div id="block-user">
                    <form>
                        <input autofocus type="text" name="search_user" id="search_user" value="<?= htmlspecialchars(@$_GET['search_user']) ?>">
                    </form>
                </div>
            <?php endif; ?>

        </header>

        <main>            
            <section class="left">
                
                <div class="user">
                    <?php if ($selected_user):?>

                        <?php form_user($selected_user); ?>
                        
                    <?php endif;?>



                    <?php if (count($search_user_values) > 1): ?>

                        <fieldset>
                            <legend>Solicitantes registrados:</legend>
                            <?php foreach ($search_user_values as $item): ?>

                            <div class="already block">
                                <div class="bar">
                                    
                                </div>
                                <div class="form">                            
                                    <a href="?user=<?= @$item['id']; ?>"><?= $item['name']; ?></a><br>
                                    <small><?= @$item['code1']; ?> <?= @$item['code2']; ?></small>
                                </div>
                            </div>      

                            <?php endforeach; ?>

                        </fieldset>
                    <?php endif; ?>
                    <?php if ($action_search_user && !$selected_user): ?>
                        <p id="lnk-new-user">                        
                            <a href="javascript:;" onclick="show_new_user()">Registrar novo solicitante...</a>
                        </p> 
                        <?php form_user(NULL) ; ?>                         
                    <?php endif; ?>

                </div>

                <?php if ($selected_user): ?>
                    <pre><?php //var_dump($query); ?></pre>
                    <pre><?php // var_dump($params); ?></pre>
                    <pre><?php // var_dump($search_items_values); ?></pre>
                    <div class="item">
                        <?php if($action_search_code): ?>

                            <?php if (!$search_items_values) : ?>

                                <p>Nenhum item encontrado.</p>

                            <?php else:?> 

                                <fieldset class="reg_items">
                                    <legend>Itens registrados:</legend>
                                    
                                    <?php foreach ($search_items_values as $item): ?>

                                        <form method="POST" action=".">  
                                            <input type="hidden" name="code" value="<?= $get_clear['code'] ?>">
                                            <input type="hidden" name="model_id" value="<?= $item['model_id'] ?>">
                                            <input type="hidden" name="patrimony_id" value="<?= $item['patrimony_id'] ?>">
                                            <input type="hidden" name="user_id" value="<?= $selected_user['id'] ?>">
                                            <input type="hidden" name="loan_new_item" value="y">
                                            <table class="item">
                                                <tr class="block">                                               
                                                    
                                                    <td>
                                                        <input <?= $item['has_patrimony'] ? 'disabled' : '' ?>  
                                                            class="original_count"                                                            
                                                            maxlength="1" 
                                                            type="number" 
                                                            name="original_count" 
                                                            value="<?= $item['has_patrimony'] ? 1 : $loan_multiplier ?>" 
                                                            placeholder="Quantidade (padrão: 1)">
                                                    </td>
                                                    <td class="form">

                                                        <span class="model_name">
                                                            <a href="javascript:;"><?= trim($item['model_name']) ?></a>
                                                        </span>
                                                        
                                                    </td> 
                                                    <td class="">        
                                                        <button type="submit"<?= $item['has_patrimony'] && !$item['patrimony_number1'] ? ' disabled' : '' ?><?= $search_one_item ? ' autofocus' : ''?>>
                                                            Emprestar <?= $item['patrimony_number1'] ?>                                                            
                                                        </button>                         
                                                    </td>
                                                    
                                                </tr>
                                            </table>
                                        </form> 
                                    <?php endforeach;?> 
                                    
                                </fieldset>

                            <?php endif;?> 

                            <p id="lnk-new-model-item">                        
                                <a href="javascript:;" onclick="show_new_model()">Registrar novo modelo de item...</a>
                            </p> 

                        <?php endif;?>   
                        <?php form_model(NULL) ; ?>    
   
                    </div>
                
                <?php endif;?>
                
            </section>
            <section class="right">
                <?php if ($selected_user):?>
                <pre><?php //var_dump($query_search_user_loans) ?></pre>                
                <pre><?php //var_dump($search_user_loans) ?></pre>
                <table class="items">
                    <tr>
                        <th>Nome do item</th>
                        <th>Código</th>
                        <th>Patrimônio</th>
                        <th>Quantidade emprestada</th>
                        <th>Quantidade devolvida</th>
                        <th>Detalhes</th>
                    </tr>
                </table>                
                <div class="date-items">
                <?php $last_date = NULL; ?>    
                <?php foreach($search_user_loans as $item): ?>                    
                    <?php $this_date = (new DateTimeImmutable($item['loan_date']))->format('d/m/Y'); ?>
                    <?php if ($last_date != $this_date): ?>

                        <?php if (!is_null($last_date)) echo '</table>' ?>

                        <input class="loan_top_checkbox" type="checkbox" id="loan_date_<?= str_replace("/","_", $this_date) ?>">
                        <label for="loan_date_<?= str_replace("/","_", $this_date) ?>" class="date"><?= $this_date ?></label> 

                        <?php echo '<table class="items">' ?>
                        
                    <?php endif; ?> 
                    <?php $last_date = $this_date; ?> 

                        <tr>
                            <td>
                                <input type="checkbox" id="loan_<?= $item['loan_id'] ?>">
                                <label for="loan_<?= $item['loan_id'] ?>"><?= $item['model_name'] ?></label>
                            </td>
                            <td><?= $item['model_code'] ?></td>
                            <td><?= $item['patrimony_number'] ?></td>
                            <td><?= $item['original_count'] ?></td>
                            <td>
                                
                                <?= $item['count_remaining'] ?>

                                <?php if ($item['count_remaining'] >=  $item['original_count'] ): ?>

                                    <a href="?log_loan=y&loan_id=<?= $item['loan_id'] ?>&diff=-1">[-]</a>

                                <?php endif;?>

                                <?php if ($item['count_remaining'] <  $item['original_count'] ): ?>

                                    <a href="?log_loan=y&loan_id=<?= $item['loan_id'] ?>&diff=1">[+]</a>

                                <?php endif;?>

                            </td>
                            <td><?= $item['all_details'] ?></td>
                        </tr>                        
                    

                    <?php endforeach;?> 

                    <?php if (!is_null($last_date)) echo '</table>' ?>   

                </div>
                  
                <?php endif;?>
            </section>
        </main>
    </div>              
    <script src='main.js'></script>

</body>
</html>