<div class="form-error"></div>
{* Если загрузка темы не запрещена *}
{if !$smarty.const.CANT_UPLOAD_THEME}
    <form class="uploadTheme clearfix" method="POST" style="text-align:left" action="{adminUrl mod_ctroller="templates-selecttheme" do="uploadTheme"}">
        <div class="col-sm-6 text-xs-center">
            <label>{t}Загрузить новую тему{/t}</label>
            <span class="upload-theme-file btn btn-success"><input type="file" name="theme" class="fileinput" id="theme-file"> {t}Выбрать zip-файл{/t}</span>
        </div>
        <div class="col-sm-6 text-xs-center">
            <input type="checkbox" name="overwrite" value="1" class="overwrite" id="overwrite">&nbsp;<label for="overwrite" class="for_overwrite">{t}Заменить тему, если таковая уже существует{/t}</label>
        </div>
    </form>
{/if}

<div class="theme-container">
    {foreach $theme_list as $name => $item}
        {$info = $item->getInfo()}
        {$shades = $item->getShades()}

        {if $name == $current.theme}
            {$currentShade = $current.shade}
        {else}
            {if count($shades)}
                {$firstShade = reset($shades)}
                {$currentShade = $firstShade.id}
            {else}
                {$currentShade = ""}
            {/if}
        {/if}

        <div class="theme{if $name==$current.theme} current{/if}{if count($shades)} has-shades{/if}" data-theme-id="{$item->getName()}">
            <div class="title">{$info.name|default:t("Неизвестно")}</div>

            <div class="preview set-this">
                <a class="img"><img class="image" src="{$item->getPreviewUrl($currentShade)}"></a>
            </div>

            <div class="select-block">
                    <div class="colors">
                        {foreach $shades as $shade}
                            <a class="item{if $currentShade==$shade.id} act{/if}" style="background: {$shade.color}" title="{$shade.title}" data-shade-id="{$shade.id}" data-preview-url="{$item->getPreviewUrl($shade.id)}"><i></i></a>
                        {foreachelse}
                            <span>{t}Нет вариаций{/t}</span>
                        {/foreach}
                    </div>
                <a class="select set-this">{t}Выбрать{/t}</a>
            </div>
        </div>
    {/foreach}
</div>

<div class="theme-mp">
    {t}Темы из Marketplace{/t}
</div>

<div class="theme-container mp" data-url="{adminUrl do="loadMarketplace"}">
    <div class="loading-themes">{t}Идет загрузка данных из Marketplace ReadyScript...{/t}</div>
</div>