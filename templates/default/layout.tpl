{* Основной шаблон *}
{strip}
{$css_version=7}
{addcss file="/rss-news/" basepath="root" rel="alternate" type="application/rss+xml" title="t('Новости')"}
{addcss file="//fonts.googleapis.com/css?family=Neucha&amp;subset=cyrillic" basepath="root" no_compress=true}
{addcss file="960gs/reset.css?v={$css_version}"}
{addcss file="style.css?v={$css_version}"}
{addcss file="960gs/960_orig.css?v={$css_version}" before="<!--[if lte IE 8]>" after="<![endif]-->"}
{addcss file="960gs/mobile.css?v={$css_version}"}
{addcss file="960gs/720.css?v={$css_version}"}
{addcss file="960gs/960.css?v={$css_version}"}
{addcss file="960gs/1200.css?v={$css_version}"}
{addcss file="colorbox.css?v={$css_version}"}
{addcss file="custom_styles.css?v={$css_version}"} {* Файл для кастомных стилей *}
{addcss file="bootstrap.popover.min.css?v={$css_version}"}
{addcss file="%users%/verification.css"}

{addjs file="html5shiv.js" unshift=true header=true}
{addjs file="jquery.min.js" name="jquery" basepath="common" unshift=true header=true}
{addjs file="bootstrap/bootstrap.min.js" basepath="common"}
{addjs file="lab/lab.min.js" basepath="common"}

{addjs file="jquery.autocomplete.js"}
{addjs file="jquery.form/jquery.form.js" basepath="common"}
{addjs file="jquery.cookie/jquery.cookie.js" basepath="common"}
{addjs file="jquery.switcher.js"}
{addjs file="jquery.ajaxpagination.js"}
{addjs file="jquery.colorbox-min.js"}
{addjs file="jquery.activetabs.js"}
{addjs file="jquery.formstyler.min.js"}
{addjs file="jcarousel/jquery.jcarousel.min.js"}
{addjs file="jcarousel/jquery.jcarousel-control.min.js"}
{addjs file="jquery.touchswipe.min.js"}
{addjs file="modernizr.touch.js"}
{addjs file="common.js"}
{addjs file="theme.js"}
{addjs file="%users%/verification.js"}
{assign var=shop_config value=ConfigLoader::byModule('shop')}
{if $shop_config}
    {addjs file="%shop%/jquery.oneclickcart.js"}
{/if}
{addmeta http-equiv="X-UA-Compatible" content="IE=Edge" unshift=true}
{$app->meta->add(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0'])|devnull}


{if $shop_config===false}{$app->setBodyClass('shopBase', true)}{else}{$app->setBodyClass('noShopBase', true)}{/if}

{$app->setDoctype('HTML')}
{/strip}
{$app->blocks->renderLayout()}

{* Подключаем файл scripts.tpl, если он существует в папке темы. В данном файле 
рекомендуется добавлять JavaScript код, который должен присутствовать на всех страницах сайта *}
{tryinclude file="%THEME%/scripts.tpl"}