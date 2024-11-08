<?php isset($PDE) or die('Nope');?>
<pre>
<?php
if (isset($form_clear['force'])){
    include 'install.php';
}

if (isset($form_clear['type'])&& isset($_FILES['filecsv']['tmp_name'])){    
    switch ($form_clear['type']){
        case 'user':
            $sql_insert = "INSERT OR REPLACE INTO user (id, name, code1, code2) VALUES \n";
            $sql_insert_line = '(?,?,?,?)';
            $sql_col_count = 4;           
            break;
        case 'model':
            $sql_insert = "INSERT OR REPLACE INTO model (id, name, code, has_patrimony) VALUES \n";
            $sql_insert_line = '(?,?,?,?)';
            $sql_col_count = 4;
           break;
        case 'patrimony':
            $sql_insert = "INSERT OR REPLACE INTO patrimony (id, model_id, number1, number2, obs) VALUES \n";
            $sql_insert_line = '(?,?,?,?,?)';
            $sql_col_count = 5;
            break;
    }
    if (isset($sql_insert)){
        $handle = fopen($_FILES['filecsv']['tmp_name'], "r");
        if ($handle) {
            $sql_params = array();
            while (($line_split  = fgetcsv($handle,0,";")) !== false) {                
                if ($line_split[0] == 'id'){
                
                } else {
                    $sql_insert_arr[] = $sql_insert_line;
                    $sql_params = array_merge($sql_params, $line_split);  
                }                
            }
            $sql_insert .= implode(",\n", $sql_insert_arr);
            try{
                Database::execute($sql_insert, $sql_params);
            } catch(Exception $e){
                echo $e->getMessage();
            }
            
            fclose($handle);
        }    
    }    
}
?>
</pre>
<h2>Instalação</h2>

<fieldset>
    <legend>Importar dados CSV</legend>
    <form enctype="multipart/form-data" action="?install" method="post">
        <p><label>Tipo de dados: <select name="type">
            <option value="user">Usuários</option>
            <option value="model">Modelos de itens</option>
            <option value="patrimony">Patrimônios</option>
        </select></label></p>
        <p><input type="file" name="filecsv" accept=".csv, .txt"></p>
        <button>Enviar</button>
        <table>
            <tr>
                <th colspan="2">Referência</th>
            </tr>
        <tr>
            <td>Usuários</td><td><code>id;nome;code1;code2</td>
        </tr>
    </table>
    </form>
</fieldset>

<fieldset>
    <legend>Utilitários</legend>

    <ul>
        <li>
            <td><a href="?install&force">Forçar atualização</a></td>
        </li>
    </ul>
    </form>
</fieldset>

