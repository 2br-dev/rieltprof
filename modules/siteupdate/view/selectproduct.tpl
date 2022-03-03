{include file="head.tpl"}

<div class="viewport">
    <div class="sp-block">
        <p class="sp-text">{t}Текущая комплектация системы:{/t} <strong>{$Setup.SCRIPT_TYPE}</strong>.<br>
        {t}Вы можете применить обновление следующих комплектаций продукта:{/t}</p>
        <p><select id="update-product">
            {foreach $data.products as $item}
            <option {if $item === $Setup.SCRIPT_TYPE}selected{/if}>{$item}</option>
            {/foreach}
        </select>
        <a href="{adminUrl do="selectProduct"}" class="btn btn-success submit">{t}Выбрать{/t}</a>
        </p>
        <p>{t}При выборе более старшей комплектации, произойдет обновление системы до соответствующей комплектации.{/t}</p>
    </div>
</div>