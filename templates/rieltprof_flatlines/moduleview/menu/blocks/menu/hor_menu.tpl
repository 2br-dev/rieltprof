{* Меню в шапке *}
{if $items}
    <nav>
        <ul class="theme-list left hidden-xs top-menu">
            {include file="blocks/menu/branch.tpl" menu_level=$items}
        </ul>
    </nav>
{else}
    {include file="%THEME%/block_stub.tpl"  class="noBack blockSmall blockLeft blockMenu" do=[
    {adminUrl do="add" mod_controller="menu-ctrl"} => t("Добавьте пункт меню")
    ]}
{/if}