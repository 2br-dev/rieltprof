{if $categories}
    {* Шаблон отображает список отобранных категорий для главной *}
    {* m - middle, s - small. Категории будут отображаться последовательно согласно схеме *}
    {$classSchema = ['M' => [
                                'imgSize' => [286, 257],
                                'imgScale' => 'cxy',
                                'class' => 'card-category-middle'
                            ],
                     's' => [
                                'imgSize' => [250, 150],
                                'imgScale' => 'cxy',
                                'class' => 'card-category-mini']
                    ]}

    {$sizeSchema = 'Msssssssssssss'} {* Схема расстановки блоков для категорий *}
    {$i=0}

    {foreach $categories as $category}
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
            {if $i > (strlen($sizeSchema)-1)}{$i=0}{/if}
            {$schemaItem = $classSchema[$sizeSchema[$i]]}
            <div class="card {$schemaItem['class']} text-center" {$category->getDebugAttributes()}>
                <div class="card-image">
                    <a href="{$category->getUrl()}"><img src="{$category->getMainImage($schemaItem['imgSize'].0, $schemaItem['imgSize'].1)}" alt="{$category.name}"></a>
                </div>
                <div class="card-text"><a href="{$category->getUrl()}"><span>{$category.name}</span></a></div>
            </div>
        </div>

        {$i = $i + 1}
        {if $i>strlen($sizeSchema)}{$i=0}{/if}
    {/foreach}
{else}
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-12">
        {include file="%THEME%/block_stub.tpl"  class="block-top-categories" do=[
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
    </div>
{/if}