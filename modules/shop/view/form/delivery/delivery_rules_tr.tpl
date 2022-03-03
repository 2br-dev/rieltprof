{addjs file="tmpl/tmpl.min.js" basepath="common"}
{addjs file="{$mod_js}deliveryrules.js" basepath="root"}

{strip}
{literal}
<script type="text/x-tmpl" id="rule-line">
    <tr class="ruleItem">
        <td class="title-td">
            <select name="data[rules][{%=o.id%}][zone]" data-selected="{%=o.zone%}">
                {/literal}
                {foreach from=$zones item=item key=key}
                    <option value="{$key}">{$item}</option>
                {/foreach}
                {literal}
            </select>
        </td>
        <td>
            <select name="data[rules][{%=o.id%}][ruletype]" data-selected="{%=o.ruletype%}">
                {/literal}
                {foreach from=$ruletypes item=item key=key}
                    <option value="{$key}">{$item}</option>
                {/foreach}
                {literal}
            </select>
        </td>
        <td>
            <input name="data[rules][{%=o.id%}][from]" type="text" value="{%=o.from%}" size="5">
        </td>
        <td>
            <input name="data[rules][{%=o.id%}][to]" type="text" value="{%=o.to%}" size="5">
        </td>
        <td>
            
            <select name="data[rules][{%=o.id%}][actiontype]" data-selected="{%=o.actiontype%}">
                {/literal}
                {foreach from=$actiontypes item=item key=key}
                    <option value="{$key}">{$item}</option>
                {/foreach}
                {literal}
            </select>
            
        </td>
        <td>
            <input name="data[rules][{%=o.id%}][value]" type="text" value="{%=o.value%}">
        </td>
        <td>
            <input name="data[rules][{%=o.id%}][interrupt]" type="checkbox" value="1" data-checked="{%=o.interrupt%}" title="{/literal}{t}Остановить обработку следующих правил, если текущее сработало{/t}{literal}">
        </td>
        <td class="item-tools">
            <a class="delete zmdi zmdi-close f-21 c-red" title="{/literal}{t}удалить{/t}{literal}"></a>
        </td>
    </tr>
</script>
{/literal}
{/strip}

<tr>
    <td colspan="2">
        {$app->autoloadScripsAjaxBefore()}
        <h2>{t}Правила расчета доставки{/t}</h2>
        
        <div class="notice-box no-padd">
            <div class="notice-bg">
                {t alias="Универсальная доставка, подсказка к формуле"}В качестве значений допустимо указывать формулу используя следующие переменные:<br>
                <b>$W</b> - Вес заказа в граммах<br>
                <b>$S</b> - Сумма заказа<br>
                Например следующая формула
                <b>round($W/500)*100</b> будет означать что стоимость доставки будет увеличиваться на 100 рублей каждые
                500 грамм{/t}
            </div>
        </div>
        <br>

        <div class="table-mobile-wrapper">
            <table id="deliveryrules" class="table" style="width:100%;">
                <tr class="table-header">
                    <th>{t}Зона{/t}</th>
                    <th>{t}Условие{/t}</th>
                    <th>{t}От{/t}</th>
                    <th>{t}До{/t}</th>
                    <th>{t}Тип надбавки{/t}</th>
                    <th>{t}Значение (формула){/t}</th>
                    <th></th>
                </tr>
            </table>
        </div>
    </td>
</tr>
<tr>
    <td colspan="2">
        <a class="add-rule button">{t}Добавить правило{/t}</a>
        <script>
            $.allReady(function() {
                $('#deliveryrules').deliveryrules();
                
                {foreach from=$data.rules item=item}
                    $('#deliveryrules').deliveryrules("addRule", {json_encode($item)});
                {/foreach}
            });        
        </script>
        {$app->autoloadScripsAjaxAfter()}
    </td>
</tr>
