{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "class"}modal-xl{/block}
{block "title"}{t}Правила возврата товаров{/t}{/block}
{block "body"}
    {$shop_config = ConfigLoader::byModule('shop')}
    {$shop_config.return_rules}
{/block}