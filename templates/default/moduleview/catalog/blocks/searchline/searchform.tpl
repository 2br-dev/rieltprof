<form method="GET" action="{$router->getUrl('catalog-front-listproducts', [])}">
    <div class="searchLine">
        <div class="queryWrap" id="queryBox">
            <input type="text" class="query{if !$param.hideAutoComplete} autocomplete{/if}" placeholder="{t}поиск в каталоге{/t}" name="query" value="{$query}" autocomplete="off" data-source-url="{$router->getUrl('catalog-block-searchline', ['sldo' => 'ajaxSearchItems', _block_id => $_block_id])}">
        </div>
        <input type="submit" class="find" value="">
    </div>
</form>