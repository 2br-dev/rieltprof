<form method="GET" action="{$router->getUrl('article-front-search')}">
    <div class="searchLine">
        <div class="queryWrap" id="queryBox">
            <input type="text" class="query{if !$param.hideAutoComplete} autocomplete{/if}" placeholder="{t}поиск статьи{/t}" name="query" value="{$query}" autocomplete="off" data-source-url="{$router->getUrl('article-block-searchline', ['sldo' => 'ajaxSearchItems', _block_id => $_block_id])}">
        </div>
        <input type="submit" class="find" value="">
    </div>
</form>