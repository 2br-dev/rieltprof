{$shop_config = ConfigLoader::byModule('shop')}

<div class="returnRulesWrapper dialogPadding">
    <h1>{t}Правила возврата товаров{/t}</h1>
    <article>
        {$shop_config.return_rules}
    </article>
</div>