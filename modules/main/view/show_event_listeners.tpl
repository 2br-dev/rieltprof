<div class="table-mobile-wrapper">
    <table class="rs-table">
        <thead>
            <th class="l-w-space"></th>
            <th>{t}Событие/подписчик{/t}</th>
            <th>{t}Приоритет{/t}</th>
            <th class="r-w-space"></th>
        </thead>
        {foreach $listeners as $event_name => $list}
            <tr class="bg-ace">
                <td class="l-w-space"></td>
                <td colspan="2" class="bg-l-gray"><i class="zmdi zmdi-notifications-active"></i>&nbsp;&nbsp; <strong>{$event_name}</strong></td>
                <td class="r-w-space"></td>
            </tr>
            {foreach $list as $key => $listener}
            <tr>
                <td class="l-w-space"></td>
                <td>&dot; &nbsp;&nbsp; {$listener.callback.0}::{$listener.callback.1}</td>
                <td>{$listener.priority}</td>
                <td class="r-w-space"></td>
            </tr>
            {/foreach}
        {/foreach}
    </table>
</div>