{addcss file="%templates%/uploadfiles.css"}
{addcss file="common/lightgallery/css/lightgallery.min.css" basepath="common"}
{addjs file="lightgallery/lightgallery-all.min.js" basepath="common"}

{addjs file="{$mod_js}tplmanager.js" basepath="root"}

<div class="common-column viewport tmanager">
    <div class="margvert10">
            <div class="category-filter dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {if $list.epath.type == 'theme'}
                   {t}Тема{/t}:{$root_sections.themes[$list.epath.type_value].title}
                {else}
                   {t}Модуль{/t}:{$root_sections.modules[$list.epath.type_value].title}
                {/if}
                <span class="caret"></span></button>

                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2" style="max-height:400px; overflow:auto;">
                   <li class="dropdown-header">{t}Темы{/t}</li>
                   {foreach $root_sections.themes as $key => $item}
                       <li><a class="call-update" href="{adminUrl mod_controller="templates-filemanager" path="theme:{$key}"}">{$item.title}</a></li>
                   {/foreach}

                   <li class="dropdown-header">{t}Модули{/t}</li>
                   {foreach $root_sections.modules as $key => $item}
                       <li><a class="call-update" href="{adminUrl mod_controller="templates-filemanager" path="module:{$key}"}">{$item.title}</a></li>
                   {/foreach}
                </ul>
            </div>

            <div class="folderpath">
                <a class="root call-update" title="{t}корневая папка{/t}" href="{adminUrl mod_controller="templates-filemanager" path="{$list.epath.type}:{$list.epath.type_value}/"}"></a>
                {foreach from=$list.epath.sections item=section key=key name="fp"}
                    <a class="call-update" href="{adminUrl mod_controller="templates-filemanager" path="{$key}"}">{$section}</a> /
                {/foreach}
                <span class="filetypes">*.{foreach from=$list.allow_extension item=one_ext name="extlist"}{if !$smarty.foreach.extlist.first},{/if}{$one_ext}{/foreach}</span>
            </div>
    </div>

    {if $list.items || $list.epath.sections}
        <div class="file-list-container" data-current-folder="{$list.epath.public_dir}">
            <ul class="file-list">
                {if $list.epath.sections}
                    <li class="dir"><a class="call-update" href="{adminUrl mod_controller="templates-filemanager" path=$list.epath.parent}">..&nbsp;&nbsp;</a></li>
                {/if}
                {foreach $list.items as $item}
                    {if $item.type == 'dir'}
                        <li class="item dir" data-path="{$item.link}" data-name="{$item.name}">
                            <div class="name">
                                <a class="call-update" href="{adminUrl mod_controller="templates-filemanager" path=$item.link}">{$item.name}</a>
                            </div>
                            <span class="tools">
                                <a class="rename" data-old-value="{$item.name}" data-url="{adminUrl mod_controller="templates-filemanager" do="rename" path=$item.link}" title="{t}переименовать{/t}"><i class="zmdi zmdi-comment-edit"></i></a>
                                <a class="delete" href="{adminUrl mod_controller="templates-filemanager" do="delete" path=$item.link}" title="{t}удалить{/t}"><i class="zmdi zmdi-delete"></i></a>
                            </span>
                        </li>
                    {else}
                         <li class="item file {$item.ext}" data-path="{$item.link}" data-name="{$item.name}.{$item.ext}">
                            <div class="name">
                                {if isset($allow_edit_ext[$item.ext])}
                                    <a class="crud-edit" href="{adminUrl mod_controller="templates-filemanager" do="edit" path=$item.path file=$item.filename}">{$item.name}.<span class="ext">{$item.ext}</span></a>
                                {else}
                                    <a rel='lightbox-image-tour' href="{$list.epath.relative_rootpath}/{$item.filename}">{$item.name}.<span class="ext">{$item.ext}</span></a>
                                {/if}
                            </div>
                            <span class="tools">
                                <a target="_blank" href="{adminUrl mod_controller="templates-filemanager" do="ajaxDownload" path=$item.link}" title="{t}скачать{/t}"><i class="zmdi zmdi-download"></i></a>
                                <a class="rename" data-old-value="{$item.name}.{$item.ext}" data-url="{adminUrl mod_controller="templates-filemanager" do="rename"}" title="{t}переименовать{/t}"><i class="zmdi zmdi-comment-edit"></i></a>
                                <a class="delete" href="{adminUrl mod_controller="templates-filemanager" do="delete" path=$item.link}" title="{t}удалить{/t}"><i class="zmdi zmdi-delete"></i></a>
                            </span>
                        </li>
                    {/if}
                {/foreach}
            </ul>
        </div>
    {else}
        <div class="empty-folder">
            {t}Пустой каталог{/t}
        </div>
    {/if}
    <div class="footerspace"></div>
</div>