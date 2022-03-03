/**
 * Отправка формы из блока дизайнера
 *
 * @param {HTMLElement} form - форма
 */
function submitDesignerForm(form)
{
    form.classList.add('d-in-loading');
    let errors = form.querySelector('.d-form-errors');
    if (errors){
        errors.remove();
    }
    fetch(form.getAttribute('action'), {
        method: 'POST', // *GET, POST, PUT, DELETE, etc.
        body: new FormData(form)
    }).then((response) => {
        return response.json();
    }).then((response) => {
        form.classList.remove('d-in-loading');
        if (response.success){ //Покажем результат
            form.innerHTML = `<div class="d-form-success">${response.success_text}</div>`;
        }else{ //Покажем ошибки
            let errors = [];
            response.messages.forEach((message) => {
                errors.push(`<div class="d-form-error">${message.text}</div>`);
            });
            form.querySelector('.d-atom-form-fields-wrapper').insertAdjacentHTML('beforebegin', `<div class="d-form-errors">${errors.join("")}</div>`);
        }
    });
}

/**
 * Инициализация отправки формы
 */
document.addEventListener('DOMContentLoaded', (e) => {
    document.addEventListener('submit', (e) => {
        let form = e.target;
        if (form.closest('.d-atom-form')){ //Если это том формы
            submitDesignerForm(form);
            e.preventDefault();
        }
    });
});