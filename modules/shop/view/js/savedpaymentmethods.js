/**
 * Скрипт активирует функции на странице Мои карты в личном кабинете
 */
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.rs-payment-method-delete').forEach((element) => {
        element.addEventListener('click', (event) => {
            let saved_method = event.target.closest('.rs-payment-method');
            let saved_method_title = saved_method.querySelector('.rs-payment-method-title').textContent;
            if (confirm(lang.t('Вы действительно хотите отвязать способ оплаты "%0"', [saved_method_title]))) {

                let url = document.querySelector('.rs-payment-methods').dataset.deleteUrl;
                let data = new FormData();
                data.append('saved_method', saved_method.dataset.id);
                data.append('payment', saved_method.closest('.rs-payment').dataset.id);

                fetch(url, {
                    method: 'post',
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    body: data,
                }).then((response) => {
                    return response.json();
                }).then((response) => {
                    if (response.success) {
                        location.reload();
                    }
                });
            }
        });
    });

    document.querySelectorAll('.rs-payment-method-makedefault').forEach((element) => {
        element.addEventListener('click', (event) => {
            let saved_method = event.target.closest('.rs-payment-method');
            let url = document.querySelector('.rs-payment-methods').dataset.makeDefaultUrl;
            let data = new FormData();
            data.append('saved_method', saved_method.dataset.id);

            fetch(url, {
                method: 'post',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                body: data,
            }).then((response) => {
                return response.json();
            }).then((response) => {
                if (response.success) {
                    location.reload();
                }
            });
        });
    });
});