{if $product['quickly']}
    <div class="feature tooltipped urgent" title="Срочно" data-tooltip="Срочная продажа">С<span class="ext">рочно</span></div>
{/if}
{if $product['exclusive']}
    <div class="feature tooltipped exclusive" title="Эксклюзив" data-tooltip="Эксклюзив чистый">Э<span class="ext">ксклюзив</span></div>
{else}
    {if $product['advertise']}
        <div class="feature tooltipped exclusive" title="Сам рекламирую" data-tooltip="Рекламиру в интернете">С<span class="ext">ам рекламирую</span></div>
    {/if}
{/if}
{if $ad['mortgage']}
    <div class="feature tooltipped mortgage" title="Ипотека" data-tooltip="Ипотеку можно">И<span class="ext">потеку можно</span></div>
{else}
    {if $ad['only_cash']}
        <div class="feature tooltipped mortgage" title="Наличные" data-tooltip="Наличные">Н<span class="ext">личные</span></div>
    {/if}
{/if}
{if $product['mark']}
    <div class="feature tooltipped stowage" title="Закладка" data-tooltip="Закладку можно/плачу комиссию">З<span class="ext">акладку можно/плачу комиссию</span></div>
{/if}
{if $product['breakdown']}
    <div class="feature tooltipped breakdown" title="Разбивка" data-tooltip="Разбивка по сумме">Р<span class="ext">азбивка</span></div>
{/if}
{if $product['encumbrance']}
    <div class="feature tooltipped encumbrance" title="Обременение" data-tooltip="Обременение: {$product['encumbrance_notice']}">О<span class="ext">бременение</span></div>
{/if}
{if $product['child']}
    <div class="feature tooltipped child" title="Дети/Опека" data-tooltip="Дети/Опека">Д<span class="ext">ети/Опека</span></div>
{/if}
