{if $items}
<nav class="topMenuNav">
    <a class="topMenuShort rs-parent-switcher">{t}меню{/t}</a>
    <ul class="topMenu">
        {include file="blocks/menu/branch.tpl" menu_level=$items}
    </ul>
</nav>
{else}
    {include file="theme:default/block_stub.tpl"  class="noBack blockSmall blockLeft blockMenu" do=[
        {adminUrl do="add" mod_controller="menu-ctrl"} => t("Добавьте пункт меню")
    ]}
{/if}