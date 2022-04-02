<div class="feature tooltipped {if $product['quickly']}urgent{/if}" {if $product['quickly']}title="Срочно" data-tooltip="Срочная продажа"{/if}>
    {if $product['quickly']}
        С<span class="ext">рочно</span>
    {/if}
</div>
<div
    class="feature tooltipped {if $product['exclusive'] || $product['advertise']}exclusive{/if}"
    {if $product['exclusive'] || $product['advertise']}
        title="{if $product['exclusive']}Эксклюзив{/if}{if $product['advertise']}Рекламирую{/if}"
        data-tooltip="{if $product['exclusive']}Эксклюзив чистый{/if}{if $product['advertise']}Рекламирую в интернете{/if}"
    {/if}
>
    {if $product['exclusive'] || $product['advertise']}
        {if $product['exclusive']}
            Э<span class="ext">ксклюзив</span>
        {/if}
        {if $product['advertise']}
            С<span class="ext">ам рекламирую</span>
        {/if}
    {/if}
</div>
<div
        class="feature tooltipped {if $product['mortgage'] || $product['only_cash']}mortgage{/if}"
        {if $product['mortgage'] || $product['only_cash']}
            title="{if $product['mortgage']}Ипотека{/if}{if $product['only_cash']}Наличные{/if}"
            data-tooltip="{if $product['mortgage']}Ипотеку можно{/if}{if $product['only_cash']}Наличные{/if}"
        {/if}
>
    {if $product['mortgage']}
        И<span class="ext">потеку можно</span>
    {else}
        {if $product['only_cash']}
            Н<span class="ext">аличные</span>
        {/if}
    {/if}
</div>
<div class="feature tooltipped {if $product['mark']}stowage{/if}" {if $product['mark']}title="Закладка" data-tooltip="Закладку можно/плачу комиссию"{/if}>
    {if $product['mark']}
        З<span class="ext">акладку можно/плачу комиссию</span>
    {/if}
</div>
<div class="feature tooltipped {if $product['breakdown']}breakdown{/if}" {if $product['breakdown']}title="Разбивка" data-tooltip="Разбивка по сумме"{/if}>
    {if $product['breakdown']}
        Р<span class="ext">азбивка</span>
    {/if}
</div>
<div class="feature tooltipped {if $product['encumbrance']}encumbrance{/if}" {if $product['encumbrance']}title="Обременение" data-tooltip="Обременение: {$product['encumbrance_notice']}"{/if}>
    {if $product['encumbrance']}
        О<span class="ext">бременение</span>
    {/if}
</div>
<div class="feature tooltipped {if $product['child']}child{/if}" {if $product['child']}title="Дети/Опека" data-tooltip="Дети/Опека"{/if}>
    {if $product['child']}
        Д<span class="ext">ети/Опека</span>
    {/if}
</div>
