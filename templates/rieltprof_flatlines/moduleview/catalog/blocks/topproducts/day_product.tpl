{* Шаблон отображает один товар на главной в виде отдельного блока *}

{if count($products)}
    {$main_image=$products.0->getMainImage()}
    <div class="card card-category">
        <div class="card-image">
            <a href="{$products.0->getUrl()}"><img src="{$main_image->getUrl(360,454, 'axy')}" alt="{$main_image.title|default:"{$products.0.title}"}"></a>
        </div>
        <div class="card-text"><span class="card-title">{$dir.name}</span>
            <a href="{$products.0->getUrl()}" class="link link-more pull-right">{t}Подробнее{/t}</a></div>
    </div>
{else}
    {include file="%THEME%/block_stub.tpl"  class="blockTopProducts" do=[
    [
    'title' => t("Добавьте категорию с товарами"),
    'href' => {adminUrl do=false mod_controller="catalog-ctrl"}
    ],
    [
    'title' => t("Настройте блок"),
    'href' => {$this_controller->getSettingUrl()},
    'class' => 'crud-add'
    ]
    ]}
{/if}