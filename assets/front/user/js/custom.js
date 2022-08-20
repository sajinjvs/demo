
'use strict';
const ul = document.getElementById('language-list');
if (document.body.contains(ul)) {
    ul.onclick = function(event) {
        document.getElementById('lang-code').value = event.target.getAttribute('data-value');
        document.getElementById('userLangForms').submit();
    };
}

const more = document.querySelectorAll('.more')
for(let i = 0; i<more.length; i++){
    more[i].addEventListener('click', ()=>{
        more[i].parentNode.classList.toggle('active')
    })
}
