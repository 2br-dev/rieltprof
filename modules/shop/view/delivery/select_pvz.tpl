{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}
{block class}modal-xxl{/block}
{block "title"}{t}Выберите ПВЗ{/t}{/block}
{block "body"}
    <div id="pvzMapWrapper" class="selectPvz {if empty($delivery_pvz_list)}selectPvz_empty{else}rs-selectPvz{/if}" data-pvz-json='{$pvz_json}'>
        {if $delivery_pvz_list}
            <div class="selectPvz_pvzSearch">
                <input type="text" class="selectPvz_pvzSearchInput rs-selectPvz_pvzSearchInput" placeholder="{t}Поиск{/t}">
            </div>
            <div class="selectPvz_pvzList">
                {foreach $delivery_pvz_list as $delivery_id => $pvz_list}
                    {foreach $pvz_list as $pvz}
                        <div class="selectPvz_pvzListItem rs-selectPvz_pvzListItem" data-pvz-code="{$pvz->getCode()}" data-delivery-id="{$delivery_id}" data-search-string='{$pvz->getTitle()}, {$pvz->getAddress()}'>
                            <div><b>{$pvz->getTitle()}</b></div>
                            <div>{$pvz->getAddress()}</div>
                        </div>
                    {/foreach}
                {/foreach}
            </div>
            <div id="pvzMap" class="selectPvz_pvzMap rs-selectPvz_map" data-load-text="{t}Загрузка карты...{/t}"></div>
        {else}
            {t}В указанном населённом пункте ПВЗ не найдены{/t}
        {/if}
    </div>
{/block}