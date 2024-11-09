<?php $PDE = 1; require 'base.php'; ?><!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='style.css'>  
    <link rel='stylesheet' type='text/css' media='screen' href='modal.css'>  
    <title><?= $page_title ?></title>    
</head>

<body class="<?= $is_searching ? 'searching': 'not-searching'?>">
    <div id="main">
        <?php if (!$is_logged): ?>
            <?php require 'page_login.php'; ?>
        <?php else: ?>
            <?php require 'page_main.php'; ?>
        <?php endif; ?>         
    </div>    
    <noscript>
        <div class="modal">
            <div class="modal-content">
                <div class="modal-header"></div>
                <div class="modal-body">
                    <p style="text-align:center">
                        Para utilizar este sistema, você precisar ativar o suporte a Javascript em seu navegador.
                    </p>    
                </div>
                <div class="modal-footer"></div>
            </div>            
        </div>        
    </noscript>     
    <script src='script.js'></script>
</body>
</html>