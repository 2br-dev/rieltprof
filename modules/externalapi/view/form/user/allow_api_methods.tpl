{$allow_mehods = $elem->getExternalApiAllowMethods($site['id'])}
{$sites = $elem.__allow_api_methods->sites}
<div>
    {if count($sites)>1}
        <ul class="tab-nav" role="tablist">
            {foreach $sites as $site}
                <li {if $site@first}class="active"{/if}>
                    <a class="" data-target="#tab_allowapimethods_site_{$site.id}" role="tab" data-toggle="tab">{$site.title}</a>
                </li>
            {/foreach}
        </ul>
    {/if}
    <div class="tab-content">
        {foreach $sites as $site}
            <div class="tab-pane {if $site@first}active{/if}" id="tab_allowapimethods_site_{$site.id}" role="tabpanel">
                {foreach $elem.__allow_api_methods->getList() as $key => $data}
                    <label><input type="checkbox" name="allow_api_methods[{$site.id}][]" value="{$key}" {if isset($allow_mehods[$site.id]) && in_array($key, $allow_mehods[$site.id])}checked{/if}> {$data}</label><br>
                {/foreach}
            </div>
        {/foreach}
    </div>
</div>