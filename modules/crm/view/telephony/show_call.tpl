{addcss file="%crm%/show_call.css"}
<div class="call-page text-center">

    <div class="call-circle">
        <i class="zmdi zmdi-{if $call_history.call_flow == 'in'}phone{else}phone-forwarded{/if}"></i>
    </div>

    <h4 class="flow">
        {if $call_history.call_flow == 'in'}
            {t}Входящий вызов{/t}
        {else}
            {t}Исходящий вызов{/t}
        {/if}
    </h4>
    <h4 class="phone-number m-b-20"><strong>{$call_history->getOtherUser()->phone|phone}</strong></h4>

    <table class="table table-va-center">
        <tbody>
        {if $call_history.record_id}
            <tr>
                <td align="right" style="vertical-align: middle;">{t}Запись{/t}</td>
                <td align="left">
                    {if $url=$call_history->getRecordUrl()}
                        <audio src="{$url}" controls class="audio"></audio>
                    {else}
                        {t}Нет{/t}
                    {/if}
                </td>
            </tr>
        {/if}
        {if $call_history.event_time}
            <tr>
                <td align="right">{t}Время звонка{/t}</td>
                <td align="left">
                    {$call_history.event_time|dateformat:"@date @time:@sec"}
                </td>
            </tr>
        {/if}
        {if $call_history.duration}
            <tr>
                <td align="right">{t}Время разговора{/t}</td>
                <td align="left">
                    {$call_history->getDurationString()}
                </td>
            </tr>
        {/if}

        {foreach $call_history as $key => $property}
            {if $property->isVisible()}
                {if $property->get()}
                    <tr>
                        <td align="right" width="50%">{$property->getDescription()}</td>
                        <td align="left" width="50%">{$property->textView()}</td>
                    </tr>
                {/if}
            {/if}
        {/foreach}
        </tbody>
    </table>

</div>