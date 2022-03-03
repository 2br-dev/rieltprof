{addjs file="%export%/rs.vklink.js"}
{$link_data=$field->vk_tools->getCategoryLinksData($elem.id)}

<div class="vk-link" data-vk-urls='{ "getVkCategoryLine": "{adminUrl do="GetVkCategoryLine" mod_controller="export-vkctrl" dir_id=$elem.id}" }'>
    {if $link_data['vk_profiles']}
        <table class="rs-table">
            <thead>
                <th>{t}Профиль экспорта{/t}</th>
                <th>{t}Категория ВК{/t}</th>
            </thead>
            <tbody class="vk-link-lines" >
                {foreach $link_data['vk_profiles'] as $profile_data}
                    {$export_vk_id = \Export\Model\Orm\Vk\VkCategoryLink::getVkId($profile_data['profile']['id'], $elem.id)}
                    {include file="%export%/vk/vk_cat_line.tpl" profile_data=$profile_data export_vk_id=$export_vk_id}
                {/foreach}
            </tbody>
        </table>
    {else}
        {t href=$router->getAdminUrl(false, [], 'export-ctrl')}У Вас еще не создано ни одного профиля экспорта для ВКонтакте. Создайте его в разделе <a href="%href">Товары -> Экспорт</a>{/t}
    {/if}
</div>