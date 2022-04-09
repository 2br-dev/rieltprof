{$shop_config = ConfigLoader::byModule('shop')}

{if $region_list}
    <select name="region_id">
        {foreach $region_list as $region}
            <option value="{$region.id}" {if $region.id == $shop_config->getDefaultRegionId()}selected{/if}>
                {$region.title}
            </option>
        {/foreach}
    </select>
{else}
    <input type="text" name="region">
{/if}