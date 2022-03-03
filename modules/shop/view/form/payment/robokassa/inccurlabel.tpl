<div class="robokassa">
    <span class="robokassa-loading">{t}Загрузка...{/t}</span>
    <span class="robokassa-error"></span>
    <span class="robokassa-select">
        <select name="data[incCurrLabel]" data-selected-value="{$elem.incCurrLabel}">
            <option value="">{t}Нет предпочитаемого способа{/t}</option>
        </select>
    </span>
    <a class="robokassa-update" data-href="{adminUrl do="useract" mod_controller="shop-paymentctrl" module="shop" paymentObj="robokassa" userAct="getIncCurrLabels"}">{t}Обновить{/t}</a>
</div>

<script>
    $.allReady(function() {
        $('.robokassa-update').click(function() {
            var form = $(this).closest('form');
            var login = form.find('[name="data[login]"]').val();
            
            $('.robokassa-error, .robokassa-select, .robokassa-update').hide();
            $('.robokassa-loading').show();            
            
            $.ajaxQuery({
                url: $(this).data('href'),
                data: {
                    params: {
                        login: login
                    }
                },
                success: function(response) {
                    $('.robokassa-loading').hide();            
                    $('.robokassa-update').show();
                    
                    if (response.data.error) {
                        $('.robokassa-error').text(response.data.error).show();
                        $('.robokassa-select').hide();
                        $('.robokassa-select select').attr('disabled', true);
                    } else {
                        
                        $('.robokassa-error').hide();
                        $('.robokassa-select select .item').remove();
                        $('.robokassa-select select').attr('disabled', false);
                        $('.robokassa-select').show();
                        
                        $.each(response.data.list, function(key, title) {
                            
                            var option = $('<option>').attr('value', key).text(title).addClass('item');
                            $('.robokassa-select select').append(option);
                            $('.robokassa-select select').val($('.robokassa-select select').data('selectedValue'));
                        });
                    }
                }
            })
        }).click();
    });
</script>