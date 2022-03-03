{if $order_info}
    <div class="titlebox">{t}Информация о заказе СДЭК{/t}</div>
    <div class="cdekStatuses">
        
        <table class="table">
            <tr class="hr">
                <td>
                   {t}Номер акта приема-передачи{/t}: 
                </td>
                <td>
                   {$order_info.ActNumber} 
                </td>
            </tr>
            <tr>
                <td>
                   {t}Номер отправления клиента{/t}:
                </td>
                <td>
                   {$order_info.Number} 
                </td>
            </tr>
            <tr class="hr">
                <td>
                   {t}Номер отправления СДЭК:<br/>(присваивается при импорте заказов){/t} 
                </td>
                <td>
                   {$order_info.DispatchNumber} 
                </td>
            </tr>
            {if isset($order_info.DeliveryDate) && !empty($order_info.DeliveryDate)}
                <tr>
                    <td>
                       {t}Дата доставки{/t}:
                    </td>
                    <td>
                       {$order_info.DeliveryDate} 
                    </td>
                </tr>
            {/if}
            {if isset($order_info.RecipientName) && !empty($order_info.RecipientName)}
                <tr class="hr">
                    <td>
                       {t}Получатель при доставке{/t}:
                    </td>
                    <td>
                       {$order_info.RecipientName} 
                    </td>
                </tr>
            {/if}
            {if isset($order_info.ReturnDispatchNumber) && !empty($order_info.ReturnDispatchNumber)}
                <tr>
                    <td>
                       {t alias="СДЕК, номер возвратного отправления"}Номер возвратного отправления:<br/> 
                       (номер накладной, в которой возвращается товар <br/>
                       интернет-магазину в случае статусов «Не вручен»,<br/> 
                       «Вручен» - «Частичная доставка»){/t}
                    </td>
                    <td>
                       {$order_info.ReturnDispatchNumber} 
                    </td>
                </tr>
            {/if}
        </table>
        <p><b>{t}Текущий статус заказа{/t}</b></p>

        <table class="table">
            <tr class="hr">
                <td>
                    {t}Дата статуса{/t}:
                </td>
                <td>
                    {$status_date=strtotime($order_info->Status.Date)}
                    {$status_date=date('d.m.Y H:i:s',$status_date)}
                    {$status_date} 
                </td>
            </tr>
            <tr>
                <td>
                    {t}Идентификатор статуса{/t}:
                </td>
                <td>
                    {$order_info->Status.Code} 
                </td>
            </tr>
            <tr class="hr">
                <td>
                    {t}Название статуса{/t}:
                </td>
                <td>
                    <b class="mainStatus">{$order_info->Status.Description}</b> 
                </td>
            </tr>
            <tr>
                <td>
                    {t}Город изменения статуса,<br/> код города по базе СДЭК{/t}:
                </td>
                <td>
                    {$order_info->Status.CityCode} 
                </td>
            </tr>
        </table>
        {if isset($order_info->Status->State)}
            <p><b>{t}История изменения статусов{/t}:</b></p>
            <ul class="statusesList">
                {foreach $order_info->Status->State as $state}
                   {$status_date=strtotime($state.Date)}
                   {$status_date=date('d.m.Y H:i:s',$status_date)}
                   <li><b>{t}Статус{/t}: {$state.Description}</b> ({$status_date}) [{$state.Code}-{$state.CityCode}] {$state.CityName}</li> 
                {/foreach}
            </ul>
        {/if}
        {* Текущий дополнительный статус *}
        {if isset($order_info->Reason) && !empty($order_info->Reason.Code)}
            <p><b>{t}Текущий дополнительный статус{/t}:</b></p>
            <ul class="statusesList">
                {foreach $order_info->Status->Reason as $state}
                   {$status_date=strtotime($state.Date)}
                   {$status_date=date('d.m.Y H:i:s',$status_date)}
                   <li><b>{t}Статус{/t}: {$state.Description}</b> ({$status_date}) [{$state.Code}]</li> 
                {/foreach}
            </ul>
        {/if}
        {* Текущая причина задержки *}
        {if isset($order_info->DelayReason) && !empty($order_info->DelayReason.Code)}
            <p><b>{t}Текущая причина задержки{/t}:</b></p>
            <ul class="statusesList">
                {foreach $order_info->Status->DelayReason as $state}
                   {$status_date=strtotime($state.Date)}
                   {$status_date=date('d.m.Y H:i:s',$status_date)}
                   <li><b>{t}Статус{/t}: {$state.Description}</b> ({$status_date}) [{$state.Code}]</li> 
                {/foreach}
            </ul>
            {* Текущая причина задержки в истории *}
            {if isset($order_info->DelayReason->State)}
                <p><b>{t}История причин задержек{/t}:</b></p>
                <ul class="statusesList">
                    {foreach $order_info->Status->DelayReason as $state}
                       {$status_date=strtotime($state.Date)}
                       {$status_date=date('d.m.Y H:i:s',$status_date)}
                       <li><b>{t}Статус{/t}: {$state.Description}</b> ({$status_date}) [{$state.Code}]</li> 
                    {/foreach}
                </ul>
            {/if}
        {/if}
        {* Упаковка - присутствуют только в конечном статусе «Вручен» в случае частичной доставки *}
        {if isset($order_info->Package)}
            <p><b>{t}Упаковка{/t} {$order_info->Package.Number}:</b></p>
            <span class="small">{t}присутствуют только в конечном статусе «Вручен» в случае<br/> частичной доставки{/t}</span>
            <ul class="statusesList">
                {foreach $order_info->Package->Item as $item}
                   <li><b>{$item.WareKey}<b>, {t}доставлено{/t} <b>{$item.DelivAmount}</b> {t}шт.{/t}</li> 
                {/foreach}
            </ul>
        {/if}
        
        {* Время доставки из расписания на доставку - присутсвует только в случае, если по условиям договора, ИМ самостоятельно предоставляет расписание доставки для СДЭК. Тэг содержит данные по неудачным попыткам доставки в разрезе предоставленного ИМ расписания доставки *}
        {if isset($order_info->Attempt)}
            <p><b>{t}Время доставки из расписания на доставку{/t}:</b></p>
            <span class="small">{t alias="CДЕК, время доставки - описание"}присутсвует только в случае, если по условиям договора,<br/> 
            ИМ самостоятельно предоставляет расписание доставки для СДЭК. Тэг содержит<br/> 
            данные по неудачным попыткам доставки в разрезе предоставленного ИМ расписания<br/> 
            доставки{/t}</span>
            <div>
                <b class="red">{$order_info->Attempt.ScheduleDescription}</b> - {$order_info->Attempt.ID} [{$order_info->Attempt.ScheduleCode}] 
            </div>
            <ul class="statusesList">
                {foreach $order_info->Package->Item as $item}
                   <li><b>{$item.WareKey}<b>, {t}доставлено{/t} <b>{$item.DelivAmount}</b> {t}шт.{/t}</li> 
                {/foreach}
            </ul>
        {/if}
        
        {* История прозвонов получателя *}
        {if isset($order_info->Call->CallGood->Good) || isset($order_info->Call->CallFail->Fail) || isset($order_info->Call->CallDelay->Delay)}
            <p><b>{t}История прозвонов получателя{/t}:</b></p>
            {if isset($order_info->Call->CallGood->Good) && !empty($order_info->Call->CallGood)}
                <p><b>{t}История удачных прозвонов{/t}:</b></p>
                <ul class="statusesList">
                    {foreach $order_info->Call->CallGood->Good as $call}
                       <li><b>{$call.Date}<b>, {t}договорились о доставке/самозаборе{/t} <b>{$call.DateDeliv}</b></li> 
                    {/foreach}
                </ul>
            {/if}
            
            {if isset($order_info->Call->CallFail->Fail) && !empty($order_info->Call->CallFail)}
                <p><b>{t}История неудачных прозвонов{/t}:</b></p>
                <ul class="statusesList">
                    {foreach $order_info->Call->CallFail->Fail as $call}
                       <li><b>{$call.ReasonDescription}</b> {$call.Date} [{$call.ReasonCode}]</li> 
                    {/foreach}
                </ul>
            {/if}
            
            {if isset($order_info->Call->CallDelay->Delay) && !empty($order_info->Call->CallDelay)}
                <p><b>{t}История переносов прозвона{/t}:</b></p>
                <ul class="statusesList">
                    {foreach $order_info->Call->CallDelay->Delay as $call}
                       <li>{$call.Date} {t}перенесли на{/t} <b>{$call.DateNext}</b></li> 
                    {/foreach}
                </ul>
            {/if}
        {/if}
        
    </div>
{else}
    {t}Заказ не был создан или не удалось отправить запрос серверу.{/t}
{/if}