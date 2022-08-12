
'use strict';
const ul = document.getElementById('language-list');
if (document.body.contains(ul)) {
    ul.onclick = function(event) {
        document.getElementById('lang-code').value = event.target.getAttribute('data-value');
        document.getElementById('userLangForms').submit();
    };
}

