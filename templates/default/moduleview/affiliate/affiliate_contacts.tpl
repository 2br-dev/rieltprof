{addjs file="%affiliate%/affiliate.js"}
<div class="affiliatesContacts">
    <div class="currentCity">
        {t}Город:{/t} <span class="cityName">{$affiliate.title}</span> 
        (<a class="otherCity">{t}выбрать другой{/t}</a>)
    </div>
    
    <div class="affiliates">
        <div class="query">
            <input type="text" placeholder="{t}Быстрый поиск по названию{/t}" class="fastSearch">
        </div>
        <div class="affiliatesColumns">
            {$columns = array_chunk($affiliates->getItems(), ceil(count($affiliates)/3))}
            {foreach $columns as $chunk}
                <ul class="tree">
                {foreach $chunk as $item}
                    <li>
                        {if $item.fields.clickable}
                            <a class="city {if $item.fields.is_highlight}hl{/if}" 
                               data-is-default="{$item.fields.is_default}" 
                               href="{$router->getUrl('affiliate-front-contacts', ['affiliate' => {$item.fields.alias}])}">{$item.fields.title}</a>
                        {else}
                            <span class="city {if $item.fields.is_highlight}hl{/if}">{$item.fields.title}</span>
                        {/if}
                        
                        {* Второй уровень *}
                        {if $item.child}
                            <ul>
                            {foreach $item.child as $subitem}
                                <li>&sdot;&sdot;&sdot;
                                {if $subitem.fields.clickable}
                                    <a class="city {if $subitem.fields.is_highlight}hl{/if}" 
                                       data-is-default="{$item.fields.is_default}" 
                                       href="{$router->getUrl('affiliate-front-contacts', ['affiliate' => {$subitem.fields.alias}])}">{$subitem.fields.title}</a>
                                {else}
                                    <span class="city {if $subitem.fields.is_highlight}hl{/if}">{$subitem.fields.title}</span>
                                {/if}
                                </li>
                            {/foreach}
                            </ul>
                        {/if}
                        {* Конец второго уровня *}
                    </li>
                {/foreach}
                </ul>
            {/foreach}
        </div>
        
    </div>
</div>

<article>
{$affiliate.contacts}
</article>

<br><br>
{* Блок связанные склады *}
{moduleinsert name="Affiliate\Controller\Block\LinkedWarehouse"}