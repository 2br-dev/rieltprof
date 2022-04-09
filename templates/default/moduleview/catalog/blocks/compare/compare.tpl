{addjs file="%catalog%/jquery.compare.js" basepath="root"}
<div class="sideBlock compareBlock{if !count($list)} hidden{else} active{/if}" id="compareBlock" data-compare-url='{ "add":"{$router->getUrl('catalog-block-compare', ["cpmdo" => "ajaxAdd", "_block_id" => $_block_id])}", "remove":"{$router->getUrl('catalog-block-compare', ["cpmdo" => "ajaxRemove", "_block_id" => $_block_id])}", "compare":"{$router->getUrl('catalog-front-compare')}" }'>
    <h2><span>{t}Товары для<br>сравнения{/t}</span></h2>
    <div class="wrapWidth">
        <ul class="compareProducts">
            {$list_html}                     
        </ul>
    </div>
    <a href="{$router->getUrl('catalog-front-compare')}" class="doCompare doCompareButton" target="_blank"><span>{t}сравнить{/t}</span></a>
</div>