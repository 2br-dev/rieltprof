{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Условия предоставления услуг{/t}{/block}
{block "class"}modal-xl{/block}
{block "body"}
    {$shop_config.license_agreement}
{/block}