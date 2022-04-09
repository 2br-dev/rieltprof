{if $url->request('dialogMode', $smarty.const.TYPE_INTEGER)}
    <div class="contentbox no-bottom-toolbar">
            <div class="titlebox">{t}Выберите шаблон{/t}</div>
            <div class="middlebox crud-ajax-group">
            <div class="updatable select-product-box" data-url="{adminUrl only_themes=$only_themes}">
{/if}
                    <div class="tmanager">
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
                                        <li><a class="call-update no-update-hash" href="{adminUrl mod_controller="templates-selecttemplate" path="theme:{$key}" only_themes=$only_themes}">{$item.title}</a></li>
                                    {/foreach}

                                    {if !empty($root_sections.modules)}
                                        <li class="dropdown-header">{t}Модули{/t}</li>
                                        {foreach $root_sections.modules as $key => $item}
                                            <li><a class="call-update no-update-hash" href="{adminUrl mod_controller="templates-selecttemplate" path="module:{$key}" only_themes=$only_themes}">{$item.title}</a></li>
                                        {/foreach}
                                    {/if}
                                </ul>
                            </div>

                            <div class="folderpath">
                                <a class="root call-update no-update-hash" title="корневая папка" href="{adminUrl mod_controller="templates-selecttemplate" path="{$list.epath.type}:{$list.epath.type_value}/" only_themes=$only_themes}"></a>
                                {foreach $list.epath.sections as $key => $section}
                                    <a class="call-update no-update-hash" href="{adminUrl mod_controller="templates-selecttemplate" path="{$key}" only_themes=$only_themes}">{$section}</a> /
                                {/foreach}
                                <span class="filetypes">*.{foreach from=$list.allow_extension item=one_ext name="extlist"}{if !$smarty.foreach.extlist.first},{/if}{$one_ext}{/foreach}</span>

                                <a class="rt makedir" data-url="{adminUrl mod_controller="templates-filemanager" do="makedir" path=$list.epath.public_dir file="noname.tpl"}" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }'>
                                    <i class="zmdi zmdi-folder visible-xs f-18" title="{t}Создать папку{/t}"></i>
                                    <span class="hidden-xs"">{t}папку{/t}</span>
                                </a>
                                <span class="rt">|</span>
                                <a class="rt crud-add maketpl" href="{adminUrl mod_controller="templates-filemanager" do="add" path=$list.epath.public_dir file="noname.tpl"}" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }'>
                                    <i class="zmdi zmdi-file visible-xs f-18" title="{t}Создать шаблон{/t}"></i>
                                    <span class="hidden-xs">{t}создать шаблон{/t}</span>
                                </a>
                            </div>
                        </div>

                        {if isset($list.items)} {$listitems_count = true} {else} {$listitems_count = false} {/if}
                        {if isset($list.epath.sections)}{$listepathsections_count = true} {else} {$listepathsections_count = false} {/if}

                        {if $listitems_count || $listepathsections_count}
                        <div class="file-list-container" data-current-folder="{$list.epath.public_dir}">
                            <ul class="file-list">
                                {if $listepathsections_count}
                                    <li class="dir"><a class="call-update no-update-hash" href="{adminUrl mod_controller="templates-selecttemplate" path=$list.epath.parent only_themes=$only_themes}">..</a></li>
                                {/if}                            
                                {foreach $list.items as $item}
                                    {if $item.type == 'dir'}
                                        <li class="item dir" data-path="{$item.link}" data-name="{$item.name}">
                                            <a class="call-update no-update-hash" href="{adminUrl mod_controller="templates-selecttemplate" path=$item.link only_themes=$only_themes}">{$item.name}</a>
                                            <span class="tools">
                                                <a class="rename" data-old-value="{$item.name}" data-url="{adminUrl mod_controller="templates-filemanager" do="rename" path=$item.link}" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }' title="{t}переименовать{/t}"><i class="zmdi zmdi-comment-edit"></i></a>
                                                <a class="delete" href="{adminUrl mod_controller="templates-filemanager" do="delete" path=$item.link}" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }' title="{t}удалить{/t}"><i class="zmdi zmdi-delete"></i></a>
                                            </span>                                                                                        
                                        </li>
                                    {else}
                                         <li class="item file {$item.ext}{if "{$item.name}.{$item.ext}"==$start_struct.filename} current{/if}" data-path="{$item.link}" data-name="{$item.name}.{$item.ext}">
                                             <div class="name">
                                                <a class="canselect">{$item.name}.<span class="ext">{$item.ext}</span></a>
                                             </div>
                                            <span class="tools">
                                                <a href="{adminUrl mod_controller="templates-filemanager" do="edit" path=$item.path file=$item.filename}" class="tool edit crud-edit" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }'><i class="zmdi zmdi-edit"></i></a>
                                                <a class="rename" data-old-value="{$item.name}.{$item.ext}" data-url="{adminUrl mod_controller="templates-filemanager" do="rename"}" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }' title="{t}переименовать{/t}"><i class="zmdi zmdi-comment-edit"></i></a>
                                                <a class="delete" href="{adminUrl mod_controller="templates-filemanager" do="delete" path=$item.link}" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }' title="{t}удалить{/t}"><i class="zmdi zmdi-delete"></i></a>
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
                    </div>

{if $url->request('dialogMode', $smarty.const.TYPE_INTEGER)}
        </div>
    <p>
        <br>
        <input type="checkbox" id="use-relative"> <label for="use-relative">{t}Не привязывать к конкретной теме{/t}</label>
    </p>
    </div>
</div>
{/if}