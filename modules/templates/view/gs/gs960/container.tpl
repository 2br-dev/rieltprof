{extends file="%templates%/gs/containers.tpl"}

{block name="container_class"}container_{$container.object.columns} col{$container.object.columns}{/block}

{block name="container_tools"}
    <a class="iplus itool crud-add" title="{t}Добавить секцию{/t}" href="{adminUrl do=addSection page_id=$currentPage.id parent_id=-$container.object.type}">
        <i class="zmdi zmdi-plus"></i>
    </a>
{/block}