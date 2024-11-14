function show_modal(queryElem) {
    var modal_window = document.createElement('div');
    var modal_content = document.createElement('div');
    var close_icon = document.createElement('span');
    var modal_body = document.createElement('div');
    var modal_header = document.createElement('div');
    var modal_footer = document.createElement('div');

    modal_window.className = 'modal';
    modal_content.className = 'modal-content';
    close_icon.className = 'close';
    close_icon.innerHTML = "&times;";
    close_icon.onclick = function () {
        modal_window.remove();
    }
    var elem_child = document.querySelector(queryElem).content.cloneNode(true);
    modal_header.className = 'modal-header';
    modal_body.className = 'modal-body';
    modal_footer.className = 'modal-footer';
    modal_body.appendChild(elem_child);

    modal_header.appendChild(close_icon);
    modal_content.appendChild(modal_header);
    modal_content.appendChild(modal_body);
    modal_content.appendChild(modal_footer);
    modal_window.appendChild(modal_content);
    document.body.appendChild(modal_window);
    modal_window.style.display = 'block';
    window.addEventListener('click', function (event) {
        if (event.target == modal_window) {
            close_icon.onclick();
        }
    });
}
var a = document.querySelector('[autofocus]');
if (a) {
    a.focus();
    unset(a);
}

function send(url, return_func, method='GET', data_arr=[]) {
    console.log('send', url);
    var xhr = new XMLHttpRequest();
    
    if (return_func) {
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    var r = JSON.parse(xhr.responseText);
                    return_func(r);
                } catch (e) {
                    console.error(url, '\n', e, xhr.responseText);
                    return_func({});
                }
            }
        };
    }
    xhr.open(method, url);
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");

    if (method=='POST'){
        xhr.send(JSON.stringify(data_arr));
    } else {
        xhr.send();
    }
    
}

function show_loan_details_action(nid){
    document.getElementById("loan_details_action_" + nid).style.display= 'block';
}

function hide_loan_details_action(nid){        
    setTimeout(function(){
        document.getElementById("loan_details_action_" + nid).style.display= 'none';
        console.log(document.activeElement)
    }, 100);
}
var diff_cache = {};
function save_loan_details(nid){
    var loan_details_new = document.getElementById("loan_details_new_" + nid);
    document.getElementById("loan_details_action_" + nid).style.display= 'none';
    send('?', update_loan_details, 'POST', {
        "details": loan_details_new.innerHTML,
        "nid": nid,
        "act": "ret",
        "diff": 0
    });
    loan_details_new.innerHTML = '';
    var url = "?"
}
function save_loan_all_reset(nid){
    var count_returned = document.getElementById('count_returned_' + nid);
    var original_count = document.getElementById('original_count_' + nid);
    save_loan_values(nid, count_returned.innerText);
}
function save_loan_all_complete(nid){
    var count_returned = document.getElementById('count_returned_' + nid);
    var original_count = document.getElementById('original_count_' + nid);
    save_loan_values(nid, count_returned.innerText - original_count.innerText);
}
function save_loan_values(nid, diff){
    send('?', update_loan_diff, 'POST', {
        "nid": nid,
        "act": "ret",
        "diff": diff,
        "return_to": 'json'
    });
}
function update_loan_diff(data){
    if (data.error){
        alert(data.message);
        return;
    }
    var count_returned = document.getElementById('count_returned_' + data.nid);
    var line_loan = document.getElementById('line_loan_' + data.nid);
    var last_count = count_returned.innerText * 1;
    var count = last_count - data.diff;
    count_returned.innerText = count;
   
    document.getElementById('minus_' + data.nid).style.display = count <= 0 ? 'none' : 'inline';
    document.getElementById('plus_' + data.nid).style.display = data.original_count == count ? 'none' : 'inline';
    if (data.original_count > 1){
        document.getElementById('complete_' + data.nid).style.display = document.getElementById('plus_' + data.nid).style.display;
        document.getElementById('reset_' + data.nid).style.display = document.getElementById('minus_' + data.nid).style.display; 
    }
    console.log('count', count, 'original_count', data.original_count);
    if (data.original_count == count){
        line_loan.classList.remove('remaining');
        line_loan.classList.add('complete');        
    } else {
        line_loan.classList.remove('complete');
        line_loan.classList.add('remaining'); 
    }    
    var loan_diff_search = document.getElementById('loan_diff_' + data.iid);
    if(loan_diff_search){
        console.log('search match', loan_diff_search)
        loan_diff_search.innerHTML = data.total_loan_diff;
    }
}
function update_loan_details(data){
    var loan_details = document.getElementById("loan_details_" + data['nid']);
    loan_details.innerHTML += '<li><span class="info">' + data['details'] + '</span><span onclick="force_loan_detail_refresh()" class="x">@</span></li>';                 
}

function delete_loan_detail(nnid){
    if (confirm("Deseja realmente remover esta observação?")){
        send('?', function(){
            document.getElementById('loan_detail_nnid_' + nnid).remove();
        }, 'POST', {
            "nnid": nnid,
            "act": "delete"
        });
    }
}
function force_loan_detail_refresh(){
    window.location.reload();
}


function select_action(obj){
    var form_hidden_select = document.getElementById('form_hidden_select');
    var act = form_hidden_select.querySelector('[name="multiact"]')
    act.value = obj.value;
    form_hidden_select.submit()
}
function select_item(obj, idname){
    var form_hidden_select = document.getElementById('form_hidden_select');
    var input_ = form_hidden_select.querySelector('[name="' + idname + '[' + obj.dataset.id +']"]');
    if (input_){
        input_.value = obj.checked;
    } else{
        input_ = document.createElement('input');
        input_.type = 'hidden';                
        input_.name = idname + '[' + obj.dataset.id + ']';
        input_.value = obj.checked;
        form_hidden_select.appendChild(input_);           
    }
}
function select_all_date(obj, idname){
    var filter = 'table.show-completed .remaining.' + obj.id + ', table:not(.show-completed) .' + obj.id;
    document.querySelectorAll(filter).forEach(function(elem){
        var line_checkbox = elem.querySelector('input.line_checkbox');
        console.log('line_checkbox', line_checkbox);
        line_checkbox.checked = obj.checked;
        select_item(line_checkbox, idname);
    });
}