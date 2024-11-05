<?php isset($PDE) or die('Nope');?>
<pre>
<?php
if (isset($form_clear['type'])&& isset($_FILES['filecsv']['tmp_name'])){    
    switch ($form_clear['type']){
        case 'user':
            $sql_insert = "INSERT OR REPLACE INTO user (id, name, code1, code2) VALUES \n";
            $sql_insert_line = '(?,?,?,?)';
            break;
        case 'model':
            $sql_insert = "INSERT OR REPLACE INTO model (id, name, code, has_patrimony) VALUES \n";
            $sql_insert_line = '(?,?,?,?)';
            break;
    }
    if (isset($sql_insert)){
        $handle = fopen($_FILES['filecsv']['tmp_name'], "r");
        if ($handle) {
            $sql_params = array();
            
            while (($line = fgets($handle)) !== false) {
                $line_split = explode(";", $line);
                if ($line_split[0] == 'id'){
                    
                } else {
                    $sql_insert_arr[] = $sql_insert_line;
                    $sql_params[] = $line_split[0];
                    $sql_params[] = $line_split[1];
                    $sql_params[] = $line_split[2];
                    $sql_params[] = trim($line_split[3]);
                }
            }
            $sql_insert .= implode(",\n", $sql_insert_arr);
            Database::execute($sql_insert, $sql_params);
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
        </select></label></p>
        <p><input type="file" name="filecsv" accept=".csv, .txt"></p>
        <button>Enviar</button>
    </form>
</fieldset>
<fieldset>
    <legend>Referência</legend>
    <table>
        <tr>
            <td>Usuários</td><td><code>id;nome;code1;code2</td>
        </tr>
    </table>
</fieldset>

