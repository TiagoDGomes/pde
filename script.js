setTimeout(function(){
    console.log(document.activeElement);
    document.activeElement.select();
}, 500);
    
function show_user (){
    show_modal("#tuser")
}
function show_item (){
    show_modal("#titem")
}
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
        modal_window.style.display = "none";
    }
    var elem_child = document.querySelector(queryElem).content.cloneNode(true);
    modal_header.className = 'modal-header';
    modal_body.className = 'modal-body';
    modal_footer.className = 'modal-footer';
    modal_body.appendChild(elem_child);
    //elem_child.style.display = 'block';

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