{extends file="%templates%/gs/containers.tpl"}

{block name="container_class"}b4container bs_col{$container.object.columns}{/block}
{block name="container_workarea_class"}container-workarea{/block}

{block name="container_tools"}
    <a class="iplusrow itool crud-add" title="{t}Добавить строку{/t}" href="{adminUrl do=addSection page_id=$currentPage.id parent_id=-$container.object.type element_type="row"}">
        <i class="zmdi zmdi-plus"></i>
    </a>
{/block}

{block name="container_switchers"}
    <div class="zmdi visible-switcher{if $smarty.cookies["page-visible-disabled-{$container.object.id}"]} off{/if}" title="{t}Включить/Выключить отображение visible-*, hidden-* секций{/t}"></div>
{/block}