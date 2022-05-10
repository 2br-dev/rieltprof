{addjs file="%rieltprof%/admin/location_choose.js"}
<div class="regionChooseFromWrapper"
     data-url="{$router->getAdminUrl('getCities', ['ajax' => 1], 'rieltprof-tools')}"
     data-choose='[name="region"]'
     data-target-class='[name="city"]'
>
    {include file=$field->getOriginalTemplate() field=$elem.__region}
</div>
Город;
<div class="regionChooseFromWrapper"
     data-url="{$router->getAdminUrl('getCounty', ['ajax' => 1], 'rieltprof-tools')}"
     data-choose='[name="city"]'
     data-target-class='[name="district"]'
>
    <select name="city">
        {if !$elem.city}
            <option value="0">Выберие регион</option>
        {else}
{*            {$cityList=$api->getCituesByregionId($elem.region)}*}
{*            {foreach $cityList as $city}*}
{*                <option value="0" {if $elem.city==$city.id}selected{/if}>Выберие регион</option>*}
{*            {/foreach}*}
        {/if}
    </select>
</div>
