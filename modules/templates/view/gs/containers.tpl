{if !empty($currentPage.template)}
    <div class="pageview-text">
        {t tpl=$currentPage.template link={adminUrl do="editPage" id=$currentPage.id} alias="Для текущей страницы задан шаблон.."}Для <a href="%link" class="crud-edit uline">текущей страницы</a> задан шаблон `<strong>%tpl</strong>`. Сборка элементов по сетке в этом случае невозможна.
        Все необходимые для данной странице модули должны быть указаны вручную в данном шаблоне.{/t}
    </div>
{else}
    {if $grid_system == 'none'}
        <div class="pageview-text">
            {t}Тема оформления не использует сетку. Все необходимые для страницы модули должны быть указаны вручную в шаблоне, установленном в <a class="crud-edit uline" href="{adminUrl do="editPage" id=$currentPage.id}">настройках страницы</a>.{/t}
        </div>
    {else}
        {$containers=$currentPage->getContainers()}
        {foreach $containers as $container}
        {if !$container.object}
            <div class="inherit">
                {if empty($defaultPage.template) && $container.defaultObject}
                    {t container = $container.defaultObject->getTitle()}Контейнер "%container" используется со страницы по умолчанию.{/t}
                    {t alias="Если Вы хотите придать другой вид этой части страницы.."
                    link = {adminUrl do="addContainer" page_id=$currentPage.id type=$container.type context=$context}
                    data_url = {adminUrl do="copyContainer" page_id=$currentPage.id type=$container.type context=$context}}
                    Если Вы хотите придать другой вид этой части страницы, <a class="crud-add make-container" href="%link">создайте новый контейнер</a> или
                    <a data-url="%data_url" class="crud-add make-container">скопируйте контейнер</a>, чтобы затем изменить его.{/t}
                {else}
                    {t link = {adminUrl do="addContainer" page_id=$currentPage.id type=$container.type context=$context} alias="Контейнер будет исключен для данной страницы.."}Контейнер будет исключен для данной сраницы.
                    Если Вы хотите его использовать, <a class="crud-add make-container" href="%link">создайте контейнер</a>.{/t}
                {/if}
            </div>
        {else}
            <div class="{block name="container_class"}{/block} gs-manager {if $smarty.cookies["page-constructor-disabled-{$container.object.id}"]}grid-disabled{/if} {if $smarty.cookies["page-visible-disabled-{$container.object.id}"]}visible-disabled{/if}" data-container-id="{$container.object.id}" data-section-id="-{$container.object.type}">
                <div class="commontools">
                    {$container.object->getTitle()}

                    <div class="container-tools">
                        {block name="container_tools"}{/block}
                        <a class="isettings itool crud-edit" title="{t}Настройки{/t}" href="{adminUrl do=editContainer id=$container.object.id page_id=$currentPage.id type=$container.object.type}">
                            <i class="zmdi zmdi-settings"></i>

                        </a>
                        {if $currentPage.route_id != 'default' || $container@last}
                            <a class="iremove itool crud-remove-one" title="{t}Удалить контейнер{/t}" href="{adminUrl do=removeContainer id=$container.object.id}">
                                <i class="zmdi zmdi-delete"></i>
                            </a>
                        {/if}
                    </div>

                    <div class="drag-handler"></div>
                    {block name="container_switchers"}{/block}
                    <div class="zmdi grid-switcher{if $smarty.cookies["page-constructor-disabled-{$container.object.id}"]} off{/if}" title="{t}Включить/Выключить сетку{/t}"></div>
                </div>
                <div class="workarea sort-sections {block name="container_workarea_class"}{/block}"> <!-- Рабочая область контейнера -->
                        {include file=$section_tpl item=$container.object->getSections()}
                </div> <!-- Конец рабочей области контейнера -->
            </div>
        {/if}        
        <div class="gs-sep"></div>
        {/foreach}
        <br>
        <div class="bottom-container-tools">
            <a class="crud-add make-container btn btn-success" href="{adminUrl do="addContainer" page_id=$currentPage.id type=$currentPage->max_container_type+1}">{t}добавить контейнер{/t}</a>
            <a class="crud-add make-container btn btn-default" data-url="{adminUrl do="copyContainer" page_id=$currentPage.id type=$currentPage->max_container_type+1 context=$context}">{t}Добавить контейнер клонированием{/t}</a>
            {if count($containers)}
                <a class="crud-remove-one btn btn-danger" href="{adminUrl do=removeLastContainer page_id=$currentPage.id}">{t}Удалить нижний контейнер{/t}</a>
            {/if}
        </div>
    {/if}
{/if}