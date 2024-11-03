<?php
$selected_patrimony = NULL;

$query = "SELECT * FROM patrimony p
            INNER JOIN model m ON m.id = p.model_id
            WHERE p.id = ?";
$selected_patrimony = Database::fetch($query, array($form_clear['pid']));        

?>
<h2>   
   <i class="icon item"></i>
   <a href="?iid=<?= $selected_patrimony['model_id'] ?>"><?= $selected_patrimony['name'] ?></a> &gt;
   <?php HTMLUtil::render_patrimony(NULL, $selected_patrimony['number1']) ; ?>
   <?php $selected_patrimony['number2'] ? HTMLUtil::render_patrimony(NULL, $selected_patrimony['number2']) : '' ; ?>
</h2>

<div class="details"> 
  
   <p><?= $selected_patrimony['obs'] ?></p>         

</div>
<p><button>Editar</button></p>
<div class="items">
    <table>
        <thead>
            <tr>                
                <th><input disabled type="checkbox" name="chk_all"></th>
                <th>Nome da pessoa</th>
                <th>Cód. Ident. 1</th>
                <th>Cód. Ident. 2</th>
                <th>Data de devolução</th>
                <th>Detalhes</th>
            </tr>
        </thead>
        <tbody>
         <tr>
            <td></td>
            <td></td>
            <td class="number"></td>
            <td class="number"></td>
            <td class="number"></td>
            <td></td>            
         </tr>
        </tbody>
    </table>
</div>

