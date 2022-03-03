{* Шаблон отображает одну категорию на главной в виде отдельного блока *}

{$category = reset($categories)}
{if $category}

    <div class="card card-category" {$category->getDebugAttributes()}>
        <div class="card-image">
            <a href="{$category->getUrl()}"><img src="{$category->getMainImage(360,454, 'axy')}" alt="{$category.name}"></a>
        </div>
        <div class="card-text"><span class="card-title">{$category.name}</span>
            <a href="{$category->getUrl()}" class="link link-more pull-right">{t}Подробнее{/t}</a></div>
    </div>

{else}
    {include file="%THEME%/block_stub.tpl"  class="block-top-one-category" do=[
    [
        'title' => t("Добавьте категорию"),
        'href' => {adminUrl do=false mod_controller="catalog-ctrl"}
    ],
    [
        'title' => t("Настройте блок"),
        'href' => {$this_controller->getSettingUrl()},
        'class' => 'crud-add'
    ]
    ]}
{/if}