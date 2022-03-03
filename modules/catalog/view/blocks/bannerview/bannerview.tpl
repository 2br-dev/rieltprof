{nocache}
{addjs file="{$mod_js}productasbanner.js" basepath="commmon"}
{/nocache}
{if $dirs}
    <div class="advBlock product-as-banner" data-block-url="{$router->getUrl('catalog-block-bannerview',['bndo' => 'getSlide', '_block_id' => $_block_id])}">
        <div class="wrapperContainer">
            {$element_html}        
        </div>
        <ul class="advList">
            {foreach from=$dirs item=item}
            <li {if $item.id == $current_dir}class="act"{/if}><i></i><a data-params='{ "dir":"{$item.id}" }'>{$item.name}</a></li>
            {/foreach}
        </ul>
    </div>

    <script type="text/javascript">
        $(function() {
            $('.advBlock').productsAsBanner();
        });
    </script>
{else}
    {include file="theme:default/block_stub.tpl"  class="blockBannerView" do=[
        [
            'title' => t("Добавьте спецкатегорию с товарами"),
            'href' => {adminUrl do=false mod_controller="catalog-ctrl"}
        ],
        [
            'title' => t("Настройте блок"),
            'href' => {$this_controller->getSettingUrl()},
            'class' => 'crud-add'
        ]
    ]}
{/if}