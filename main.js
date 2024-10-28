var mode = function(m){
    document.querySelectorAll("#content-input div").forEach(function(elem){
        elem.style.display = 'none';
    })
    document.getElementById('block-' + m).style.display = 'block';
    document.getElementById('content-input').className = m;
}

setTimeout(function(){
    console.log(document.activeElement);
    document.activeElement.select();
}, 500);
    



function ocultar_modelo_popout(){
    document.getElementById('model_popout').style.display = 'none';
}
function pesquisar_modelo(){
    console.log('pesquisar_modelo')
    document.getElementById('model_popout').style.display = 'block';
    document.getElementById('unique').disabled = false;
    document.getElementById('multiple').disabled = false;
    document.getElementById('model_id').value = "";
}
function selecionar_modelo(id, value, unique){
    document.getElementById('model_id').value = id;
    document.getElementById('model_name').value = value;
    document.getElementById('model_popout').style.display = 'none';
    document.getElementById('model_popout').style.display = 'none';
    document.getElementById('unique').checked = unique;
    document.getElementById('multiple').checked = !unique;
    document.getElementById('unique').disabled = true;
    document.getElementById('multiple').disabled = true;
    document.getElementById('model_popout').style.display = 'none';
}

function show_modal(queryElem){    
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
    close_icon.onclick = function() {
        modal_window.style.display = "none";
    }    
    var elem_child = document.querySelector(queryElem);
    modal_header.className = 'modal-header';
    modal_body.className = 'modal-body';
    modal_footer.className = 'modal-footer';
    modal_body.appendChild(elem_child);
    elem_child.style.display = 'block';

    modal_header.appendChild(close_icon);
    modal_content.appendChild(modal_header);
    modal_content.appendChild(modal_body);
    modal_content.appendChild(modal_footer);
    modal_window.appendChild(modal_content);
    document.body.appendChild(modal_window);
    modal_window.style.display = 'block';

    window.addEventListener('click', function(event){
        if (event.target == modal_window) {
            close_icon.onclick();
        }
    });
}


function show_new_model(){
    show_modal('#block-new-model-item');
}
function show_new_user(){
    show_modal("#form_new_user");
}
function select_patrimony(unique){
    document.getElementById('unique_codes').style.display = unique ? 'inline-block': 'none'
}
function edit_user(){
    show_modal('#form_edit_user');
}