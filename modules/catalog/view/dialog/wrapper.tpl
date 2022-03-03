<div class="column-left">
    <div class="current-path" data-toggle-class="folders-open" data-target-closest=".selectProduct">
        <ol class="breadcrumb"></ol>
        <div class="flexitem">
            <a class="gray-around path-toggle">
                <i class="zmdi zmdi-chevron-right"></i>
            </a>
        </div>
    </div>
    <div class="admin-category">
        <ul>
            <li class="endminus" qid="0"><img src="{$Setup.IMG_PATH}/adminstyle/minitree/folder.png">
            <input type="checkbox" value="0" {if $hideGroupCheckbox}style="display:none"{/if}><a class="act">{t}Все{/t}</a>
            {include file="dialog/tree_branch.tpl" dirlist=$treeList open=true}
            </li>
        </ul>
    </div>
</div>
<div class="column-right">
    <form class="filter" onsubmit="return false;">
        <a class="filter-toggle" data-toggle-class="filter-open" data-target-closest=".filter"><i class="zmdi zmdi-search"></i> {t}Поиск по товарам{/t}</a>
        <div class="form-inline">
            <div class="form-group">
                <label>№</label><br>
                <input type="text" class="field-id" size="4">
            </div>

            <div class="form-group">
                <label>{t}Название:{/t}</label><br>
                <input type="text" class="field-title" size="20">
            </div>

            <div class="form-group">
                <label>{t}Артикул:{/t}</label><br>
                <input type="text" class="field-barcode" size="10">
            </div>

            <div class="form-group">
                <label>{t}Штрихкод:{/t}</label><br>
                <input type="text" class="field-sku" size="20">
            </div>

            <div class="form-group">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-default set-filter">
                    <i class="zmdi zmdi-filter-list" title="{t}Применить{/t}"></i> <span class="visible-xs-inline-block">{t}Применить{/t}</span>
                </button>
                <a class="btn btn-default clear-filter">
                    <i class="zmdi zmdi-block" title="{t}Очистить фильтр{/t}"></i> <span class="visible-xs-inline-block">{t}Очистить фильтр{/t}</span>
                </a>
            </div>
        </div>
        <input type="submit" style="display:none">
    </form>

    <div class="productblock">
        <div class="loader">
            <div class="overlay">&nbsp;</div>
            <div class="loooading">
                <div class="back"></div>
                <span>{t}Загрузка...{/t}</span>
            </div>
        </div>
        <div class="product-container">
        {$products}
        </div>
    </div>
</div>