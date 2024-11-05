    
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
        modal_window.remove();
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

