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
         group_concat(nn.details, '<br>') as all_details_loan ,
         group_concat(pp.details, '<br>') as all_details_patrimony ,
         n.id as loan_id,
         pp.tstamp AS last_check
      FROM patrimony p 
      INNER JOIN model m ON (p.model_id = m.id)
      LEFT JOIN loan n ON (n.model_id = m.id and p.id = n.patrimony_id)
      LEFT JOIN user u ON (u.id = n.user_id)
      LEFT JOIN log_loan nn ON (nn.loan_id = n.id)
      LEFT JOIN log_patrimony pp ON (pp.patrimony_id = p.id)
      GROUP BY p.id
      ORDER BY last_check , number_cast
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
            <td class="text" data-name="patrimony__location__<?= $item['patrimony_id'] ?>"><?= $item['patrimony_location'] ?></td>
            <td class="text" data-name="patrimony__obs__<?= $item['patrimony_id'] ?>"><?= $item['obs'] ?></td>
            <td data-ignore class="date">
                <i class="icon <?= $item['original_count'] <= $item['loan_date'] && $item['count_returned'] ? 'check' : '' ?>"></i>
                <?= $item['loan_date'] ? (new DateTimeImmutable( $item['loan_date'] ))->format('d/m/Y, H:i:s') : ''?>
            </td>
            <td data-type="boolean" class="date" data-name="log_patrimony__last_check__<?= $item['patrimony_id'] ?>"><?=$item['last_check'] ? (new DateTimeImmutable( $item['last_check'] ))->format('d/m/Y, H:i:s'):'&nbsp;' ?></td>
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
                var data = element.cellName.split("__");
                var table_name = data[0];
                var column_name = data[1];
                var tid = data[2];
                var args;
                var func_callback;
                switch (table_name){
                    case 'log_patrimony':
                        args = {
                            "act": "insert",
                            "table_name" : table_name,
                            "columns_name": ['patrimony_id'],
                            "values": [tid]
                        };
                        func_callback = function(data){
                            console.log('func_callback insert', data);
                            console.log('func_callback element', element);
                            var ident = 'inventory_log_patrimony__last_check__' + tid;
                            var label = document.querySelector('td.' + ident + ' label');
                            var input = document.getElementById(ident);
                            console.log('func_callback label', label);
                            label.innerHTML = new Date(data.tstamp).toLocaleString();
                            setTimeout(()=>{
                                input.checked = false;
                            }, 3000)
                            element.value = valueRaw;
                        }
                        break;
                    case 'patrimony':
                        args = {
                            "id": tid,
                            "act": "update",
                            "table_name": table_name,
                            "column_name": column_name,
                            "value": valueRaw,
                            "return_to": 'json'
                        };
                        func_callback = function(data){
                            element.value = valueRaw;
                        }
                }
                if (args){
                    send('?', func_callback, 'POST', args);
                }
                
                // switch(column_name){
                //     case 'obs':
                //         break;
                //     case 'check':
                //         break;
                // }
                console.log(element)
                // if (confirm('Accept ' + colName + rowIndex + ' with ' + valueRaw + '?')){
                //     return true;
                // } else {
                //     return false;  // Undo changes
                // }
            },
            
            theme: 'JSimpleSpreadsheet/jsimplespreadsheet.theme.css',
            
            // trSelector: 'tr',
            
            // tdSelector: 'td',
            
            cellClassSelectorPreffix: 'inventory_',
            
            // focusClassSelector: 'focus',
            
            defaultClass: 'jss_default_class'

        });
    }
    
   
    window.addEventListener('DOMContentLoaded', init_inventory, false);
        

    function mark_check_and_disable(patrimony_id, elem){
        mark_check(patrimony_id, ()=>{
            elem.disabled = true;
        })
    }
    function mark_check(patrimony_id, func){
        args = {
            "act": "insert",
            "table_name" : 'log_patrimony',
            "columns_name": ['patrimony_id'],
            "values": [patrimony_id]
        };
        send('?', func, 'POST', args);
    }
  

</script>