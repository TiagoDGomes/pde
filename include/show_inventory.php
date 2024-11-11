<?php

$query = "SELECT m.id as model_id, 
         m.name as model_name,
         m.code as model_code, 
         p.id as patrimony_id, 
         max(n.tstamp) as loan_date, 
         cast(p.number1 as integer) as number_cast,
         p.number1 as patrimony_number1, 
         p.number2 as patrimony_number2, 
         p.obs as obs,
         p.patrimony_location as patrimony_location,
         m.model_location as model_location,
         u.id as uid,
         u.name as username,
         u.code1 as code1,
         u.code2 as code2,
         p.loan_block as loan_block,
         icon_set,       
         CASE WHEN 
            p.loan_block = 1 or p.usable = 0  or p.found = 0 THEN 'blocked'
            ELSE 'ok' END AS icon_block,
         CASE WHEN 
            p.usable = 1  THEN 'usable'
            ELSE 'trash' END AS icon_usable,
         CASE WHEN 
            p.found = 1  THEN 'found'
            ELSE 'unknown' END AS icon_found,
         p.serial_number as patrimony_serial_number, 
         original_count,
         CASE 
            WHEN original_count - sum(diff) > original_count THEN original_count
            ELSE original_count - sum(diff) END as count_returned, 
         group_concat(details, '<br>') as all_details ,
         n.id as loan_id
      FROM patrimony p 
      INNER JOIN model m ON (p.model_id = m.id)
      LEFT JOIN loan n ON (n.model_id = m.id and p.id = n.patrimony_id)
      LEFT JOIN user u ON (u.id = n.user_id)
      LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
      GROUP BY p.id
      ORDER BY number_cast
      LIMIT 0,20
            ";
$patrimonies = Database::fetchAll($query, array());                
?>

<?php include 'include/form_hidden_select.php'; ?>
<?php include 'include/date_filter.php'; ?>
<table class="inventory">
    <caption>Inventário</caption>
    <thead>
        <tr>
            <th class="number">Etiqueta</th>
            <th>Nome</th>
            <th class="status">Status</th>
            <th class="text">Localização</th>
            <th class="text">Observação</th>
            <th class="date">Último empréstimo</th>
            <th class="date">Última marcação de visto</th>
        </tr>
        <!-- <tr>
            <td><input type="text" name="field1"></td>
            <td><input type="text" name="field2"></td>
            <td><input type="text" name="field3"></td>
            <td><input type="text" name="field4"></td>
            <td><input type="text" name="field5"></td>
        </tr> -->
    </thead>
    <tbody>
    <?php foreach($patrimonies as $item): ?> 
        <tr>
            <td data-ignore class="number"><?= HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number1']) ?></td>
            <!-- <td><?= HTMLUtil::render_patrimony($item['patrimony_id'], $item['patrimony_number2']) ?></td> -->
            <td data-ignore><?= $item['model_name'] ?></td>
            <td data-ignore class="icon-cell">
                <i class="icon <?= $item['icon_block'] ?>"></i>
                <i class="icon <?= $item['icon_usable'] ?>"></i>
                <i class="icon <?= $item['icon_found'] ?>"></i>
            </td>
            <td class="text"><?= $item['patrimony_location'] ?></td>
            <td class="text"><?= $item['obs'] ?></td>
            <td data-ignore class="date">
                <i class="icon <?= $item['original_count'] <= $item['loan_date'] && $item['count_returned'] ? 'check' : '' ?>"></i>
                <?= $item['loan_date'] ? (new DateTimeImmutable( $item['loan_date'] ))->format('d/m/Y H:i:s') : ''?>
            </td>
            <td data-type="boolean" class="date"></td>
        </tr>
    <?php endforeach; ?> 
    </tbody>
</table>

<script>
    var worksheet = null;
    function init_inventory(){
        var your_table_selector = '.inventory';
        worksheet = new JSimpleSpreadsheet(your_table_selector, {

            // onFocus: function(colName, rowIndex, valueRaw){
            //     // Example - onFocus:
            //     alert('This is ' + colName + rowIndex + ' with focus!');
            // },
            
            // onBlur: function(colName, rowIndex, valueRaw){						
            //     // Example - onFocus:
            //     alert('Bye ' + colName + rowIndex);
            // },
            
            onChange: function(colName, rowIndex, valueRaw, oldValueRaw, element){					
                // Example - onBlur:
                if (confirm('Accept ' + colName + rowIndex + ' with ' + valueRaw + '?')){
                    return true;
                } else {
                    return false;  // Undo changes
                }
            },
            
            theme: 'JSimpleSpreadsheet/jsimplespreadsheet.theme.css',
            
            // trSelector: 'tr',
            
            // tdSelector: 'td',
            
            // cellClassSelectorPreffix: 'cell_',
            
            // focusClassSelector: 'focus',
            
            defaultClass: 'jss_default_class'

        });
    }
    
   
    window.addEventListener('DOMContentLoaded', init_inventory, false);
        

    
  

</script>