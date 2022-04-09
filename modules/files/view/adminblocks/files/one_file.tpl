<tr data-id="{$linked_file.id}" class="item">
    <td class="chk"><input type="checkbox" value="{$linked_file.id}" name="files[]"></td>
    <td class="drag drag-handle"><a class="sort dndsort" data-sortid="{$linked_file.id}">
            <i class="zmdi zmdi-unfold-more"></i>
        </a></td>
    <td class="title clickable">{$linked_file.name}</td>
    <td class="description clickable">
        <span class="hidden-xs">{$linked_file.description|teaser:"40"|default:t('<span class="no">нет</span>')}</span>
    </td>
    <td class="size clickable">
        <span class="hidden-xs">{$linked_file.size|format_filesize}</span>
    </td>
    <td class="access">
        {foreach $linked_file->getLinkType()->getAccessTypes() as $access_key => $info}
        <label>
            <input type="radio" name="access_file[{$linked_file.id}]" class="access_file" value="{$access_key}" {if $linked_file.access == $access_key}checked{/if}>
            {if is_array($info)}
                <span class="label-text">{$info.title} <span class="help-icon" title="{$info.hint}">?</i></span>
            {else}
                <span class="label-text">{$info}</span>
            {/if}
        </label>
        {/foreach}
    </td>
    <td class="actions">
        <span class="loader"></span>

        <div class="inline-tools">
            <a class="tool file-edit" title="{t}редактировать{/t}">
                <i class="zmdi zmdi-edit"></i>
            </a>
            <a class="tool file-download" href="{$linked_file->getAdminDownloadUrl()}" title="{t}скачать{/t}">
                <i class="zmdi zmdi-download"></i>
            </a>
            <a class="tool file-delete delete c-red" title="{t}удалить{/t}">
                <i class="zmdi zmdi-delete"></i>
            </a>
        </div>
    </td>
</tr>