<tr>
    <td>
        {$profile_data.profile.title}
    </td>
    <td>
        {if $profile_data['profile_vk_categories']}
            <select name="vk_dir[{$profile_data.profile.id}]" title="{$profile_data.title}" class="vk-link m-b-5">
                <option value="" selected>{t}-- Не выбрано --{/t}</option>
                {html_options options=$profile_data['profile_vk_categories'] selected=$export_vk_id}
            </select>
            <a class="btn btn-default vk-reload-category m-b-5"  data-link="{adminUrl mod_controller="export-vkctrl" do="GetVkCategoryList" profile_id=$profile_data.profile.id dir_id=$elem.id}">{t}Обновить категории VK{/t}</a>
        {else}
            <a class="btn btn-default vk-reload-category"  data-link="{adminUrl mod_controller="export-vkctrl" do="GetVkCategoryList" profile_id=$profile_data.profile.id dir_id=$elem.id}">{t}Загрузить категории VK{/t}</a>
        {/if}
    </td>
</tr>