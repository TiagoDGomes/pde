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