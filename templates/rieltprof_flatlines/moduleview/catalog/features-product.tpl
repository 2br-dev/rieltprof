{if $product['quickly']}
    <div class="chip tooltipped bottom" data-tooltip="Срочная продажа" id="urgent">С<span class="hide-mobile">рочно</span></div>
{/if}
{if $product['mortgage']}
    <div class="chip tooltipped bottom" id="mortgage" data-tooltip="Ипотеку можно">И<span class="hide-mobile">потеку можно</span></div>
{else}
    {if $product['only_cash']}
        <div class="chip tooltipped bottom" id="mortgage" data-tooltip="Только наличные">Н<span class="hide-mobile">аличные</span></div>
    {/if}
{/if}
{if $product['exclusive']}
    <div class="chip tooltipped bottom" data-tooltip="Чистый эклсклюзив" id="exclusive">Э<span class="hide-mobile">ксклюзив</span></div>
{else}
    {if $product['advertise']}
        <div class="chip tooltipped bottom" data-tooltip="Рекламирую в интернете" id="exclusive">C<span class="hide-mobile">ам рекламирую</span></div>
    {/if}
{/if}
{if $product['mark']}
    <div class="chip tooltipped bottom" data-tooltip="Закладку можно/плачу комиссию" id="stowage">З<span class="hide-mobile">акладку можно/плачу комиссию</span></div>
{/if}
{if $product['breakdown']}
    <div class="chip tooltipped bottom breakdown" title="Разбивка" data-tooltip="Разбивка по сумме">Р<span class="hide-mobile">азбивка</span></div>
{/if}
{if $product['encumbrance']}
    <div class="chip tooltipped bottom encumbrance" title="Обременение" data-tooltip="Обременение: {$product['encumbrance_notice']}">О<span class="hide-mobile">бременение</span></div>
{/if}
{if $product['child']}
    <div class="chip tooltipped bottom child" title="Дети/Опека" data-tooltip="Дети/Опека">Д<span class="hide-mobile">ети/Опека</span></div>
{/if}
