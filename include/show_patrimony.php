<?php
$selected_patrimony = NULL;

$query = "SELECT * FROM patrimony p
            INNER JOIN model m ON m.id = p.model_id
            WHERE p.id = ?";
$selected_patrimony = Database::fetch($query, array($form_clear['pid']));        

?>
<h2>
   <?php HTMLUtil::render_patrimony(NULL, $selected_patrimony['number1']) ; ?>
   <?= $selected_patrimony['name'] ?>
</h2>

<div class="details"> 
   <dl>
      <dt>Observações:</dt>
      <dd><?= $selected_patrimony['obs'] ?></dd>
         
   </dl>
</div>

<div class="items">
    <table>
        <thead>
            <tr>                
                <th><input type="checkbox" name="chk_all"></th>
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


<pre><?php var_dump($selected_patrimony); ?></pre>