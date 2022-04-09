{addjs file="%catalog%/rscomponent/searchline.js"}
<form class="head-search rs-search-line" action="{$router->getUrl('catalog-front-listproducts', [])}" method="GET">
    <input type="text" class="form-control {if !$param.hideAutoComplete} rs-autocomplete{/if}" placeholder="{t}поиск в каталоге{/t}" name="query" value="{$query}" autocomplete="off" data-source-url="{$router->getUrl('catalog-block-searchline', ['sldo' => 'ajaxSearchItems', _block_id => $_block_id])}">
    <div class="head-search__dropdown rs-autocomplete-result"></div>
    <button type="button" class="head-search__clear rs-autocomplete-clear {if !$query}d-none{/if}">
        <img src="{$THEME_IMG}/icons/close.svg" alt="">
    </button>
    <button class="head-search__btn" type="submit">
        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.8885 4C7.09202 4 4 7.092 4 10.8885C4 14.685 7.09202 17.7771 10.8885 17.7771C12.5475 17.7771 14.0726 17.1894 15.2633 16.2077H15.2703L18.8604 19.8048C19.1207 20.0651 19.5444 20.0651 19.8048 19.8048C20.0651 19.5444 20.0651 19.1276 19.8048 18.8673L16.2008 15.2703C16.2019 15.2681 16.1997 15.2647 16.2008 15.2634C17.1825 14.0727 17.7771 12.5476 17.7771 10.8886C17.7771 7.09207 14.6851 4.00007 10.8885 4.00007L10.8885 4ZM10.8885 5.33327C13.9645 5.33327 16.4438 7.81256 16.4438 10.8885C16.4438 13.9645 13.9645 16.4438 10.8885 16.4438C7.81258 16.4438 5.33327 13.9645 5.33327 10.8885C5.33327 7.81256 7.81258 5.33327 10.8885 5.33327Z"/>
        </svg>
    </button>
</form>