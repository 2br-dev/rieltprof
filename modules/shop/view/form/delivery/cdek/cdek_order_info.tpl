{if $order_info}
    <div class="titlebox">{$title}</div>
    <div class="cdekStatuses">
        
        <table class="table">
            <tr class="hr">
                <td>
                   {t}Номер отправления клиента{/t}:
                </td>
                <td>
                   {$order_info.Number} 
                </td>
            </tr>
            <tr>
                <td>
                   {t}Дата, в которую был передан заказ в базу СДЭК{/t}:
                </td>
                <td>
                   {$order_info.Date} 
                </td>
            </tr>
            <tr class="hr">
                <td>
                   {t}Номер отправления СДЭК{/t}:<br/> 
                   ({t}присваивается при импорте заказов{/t}) 
                </td>
                <td>
                   {$order_info.DispatchNumber} 
                </td>
            </tr>
            <tr>
                <td>
                   {t}Код типа тарифа{/t}:
                </td>
                <td>
                   [{$order_info.TariffTypeCode}] {$tariffCode.title}
                </td>
            </tr>
            <tr class="hr">
                <td>
                   {t}Расчетный вес (в граммах){/t}:
                </td>
                <td>
                   {$order_info.Weight * 1000}
                </td>
            </tr>
            <tr>
                <td>
                   {t}Стоимость услуги доставки, руб{/t}:
                </td>
                <td>
                   {$order_info.DeliverySum} 
                </td>
            </tr>
            {if isset($order_info.DateLastChange) && !empty($order_info.DateLastChange)}
            <tr class="hr">
                <td>
                   {t}Дата последнего изменения суммы по услуге доставки{/t}:
                </td>
                <td>
                   {$order_info.DateLastChange} 
                </td>
            </tr>
            {/if}
            <tr>
                <td>
                   {t}Сумма наложенного платежа,<br/> которую необходимо было взять с получателя{/t}:
                </td>
                <td>
                   {$order_info.CachOnDelivFac} 
                </td>
            </tr>
            {if isset($order_info.CashOnDeliv) && !empty($order_info.CashOnDeliv)}
            <tr class="hr">
                <td>
                   {t}Сумма наложенного платежа,<br/> которую взяли с получателя, с учетом частичной доставки.<br/>Доступно только для накладных в статусе «Вручен»{/t}:
                </td>
                <td>
                   {$order_info.CashOnDeliv} 
                </td>
            </tr>
            {/if}
        </table>
        <p><b>{t}Город отправителя{/t}</b></p>

        <table class="table">
            <tr class="hr">
                <td>
                    {t}Код города по базе СДЭК{/t}:
                </td>
                <td>
                    {$order_info->SendCity.Code} 
                </td>
            </tr>
            <tr>
                <td>
                    {t}Почтовый индекс города{/t}:
                </td>
                <td>
                    {$order_info->SendCity.PostCode} 
                </td>
            </tr>
            <tr class="hr">
                <td>
                    {t}Название города{/t}:
                </td>
                <td>
                    {$order_info->SendCity.Name} 
                </td>
            </tr>
        </table>
        <p><b>{t}Город получателя{/t}</b></p>

        <table class="table">
            <tr class="hr">
                <td>
                    {t}Код города по базе СДЭК{/t}:
                </td>
                <td>
                    {$order_info->RecCity.Code} 
                </td>
            </tr>
            <tr>
                <td>
                    {t}Почтовый индекс города{/t}:
                </td>
                <td>
                    {$order_info->RecCity.PostCode} 
                </td>
            </tr>
            <tr class="hr">
                <td>
                    {t}Название города{/t}:
                </td>
                <td>
                    {$order_info->RecCity.Name} 
                </td>
            </tr>
        </table>
        {if isset($order_info->AddedService) && !empty($order_info->AddedService.ServiceCode)}
            <p><b>{t}Дополнительные услуги к заказам{/t}</b></p>

            <table class="table">
                <tr class="hr">
                    <td>
                        {t}Тип дополнительной услуги{/t}:
                    </td>
                    <td>
                        [{$order_info->AddedService.ServiceCode}] {$addTariffCode.title}
                    </td>
                </tr>
                <tr>
                    <td>
                        {t}Сумма услуги, руб{/t}:
                    </td>
                    <td>
                        {$order_info->AddedService.Sum} 
                    </td>
                </tr>
            </table>
        {/if}
        
    </div>
{else}
    {t}Заказ не был создан или не удалось отправить запрос серверу.{/t}
{/if}