{* Блок, отображает общее количество товаров в сравнении *}

{if $THEME_SETTINGS.enable_compare}
    {addjs file="rs.compare.js"}
    {$total=$this_controller->api->getCount()}

    <div class="gridblock rs-compare-block {if $total} active{/if}" data-compare-url='{ "add":"{$router->getUrl('catalog-block-compare', ["cpmdo" => "ajaxAdd", "_block_id" => $_block_id])}", "remove":"{$router->getUrl('catalog-block-compare', ["cpmdo" => "ajaxRemove", "_block_id" => $_block_id])}", "removeAll":"{$router->getUrl('catalog-block-compare', ["cpmdo" => "ajaxRemoveAll", "_block_id" => $_block_id])}", "compare":"{$router->getUrl('catalog-front-compare')}" }'>
        <div class="cart-wrapper">
            <div class="cart-block">
                <div class="cart-block-wrapper">

                    <div class="icon-compare rs-do-compare">
                        <i class="i-svg i-svg-compare"></i>
                        <i class="counter rs-compare-items-count">{$total}</i>
                    </div>

                </div>
            </div>
        </div>
    </div>
{/if}