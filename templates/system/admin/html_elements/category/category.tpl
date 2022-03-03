{addjs file="nestedSortable/jquery.mjs.nestedSortable.js" basepath="common"}
{addjs file="jquery.rs.categoryview.js" basepath="common"}
        
<div class="activecategory category">
    <ul class="categoryhead">
        <li>
            {if !$category->options.noCheckbox}
                <div class="chk"><input type="checkbox" class="select-page" data-name="{$category->getCheckboxName()}"></div>
            {/if}
            {foreach $category->getHeadButtons() as $button}
                {if $button.tag}{$tag=$button.tag}{else}{$tag="a"}{/if}
                <{$tag} {foreach from=$button.attr|default:array() key=key item=value} {$key}="{$value}"{/foreach}>{$button.text}</{$tag}>
            {/foreach}
        </li>
    </ul>

    <ul class="categorybody root{if $category->options.sortable} categorysort{/if}" data-sort-url="{$category->options.sortUrl}">
        {foreach $category->getData() as $key => $item}
            {$is_disabled = (isset($category->options.disabledField) && $item[$category->options.disabledField] === $category->options.disabledValue) ? 'disabled' : ''}
            {$is_current = (isset($category->options.activeField) && $category->options.activeValue == $item[$category->options.activeField]) ? 'current' : ''}
            {$class_field = ($item[$category->options.classField]) ? $item[$category->options.classField] : ''}
            {$is_root = ($item.is_root_element) ? 'root noDraggable' : ''}

            {$item_id = $item[$category->options.sortIdField]}

            <li class="{$is_disabled} {$is_current} {$class_field} {$is_root}" {if !empty($item.noDraggable)}data-notmove="notmove"{/if} data-id="{$item_id}">
                <div class="item">
                    <div class="chk" unselectable="on">
                        {if !$item.noCheckbox && !$category->options.noCheckbox}
                            <input type="checkbox" name="{$category->getCheckboxName()}" value="{$item[$category->options.activeField]}" {if $category->isChecked($item[$category->options.activeField])}checked{/if} {if $item.fdisabledCheckbox}disabled{/if}>
                        {/if}
                    </div>
                    <div class="line">
                        {if $category->options.sortable}<div class="move{if !empty($item.noDraggable)} no-move{/if}"><i class="zmdi zmdi-unfold-more"></i></div>{/if}
                        {if !$item.noRedMarker}
                            <div class="redmarker"></div>
                        {/if}
                        <div class="data">
                            <div class="textvalue">
                                {$cell=$category->getMainColumn($item)}
                                {if isset($cell->property.href)}<a href="{$cell->getHref()}" class="call-update">{/if}
                                    {include file=$cell->getBodyTemplate() cell=$cell}
                                    {if isset($cell->property.href)}</a>{/if}
                            </div>
                            {if empty($item.noOtherColumns)}
                                {if isset($item.categoryTools)}
                                    {$item.categoryTools->setRow($item)|devnull}
                                    {include file=$item.categoryTools->getBodyTemplate() cell=$item.categoryTools}
                                {else}
                                    {if $category->getTools()}
                                        {include file=$category->getTools()->getBodyTemplate() cell=$category->getTools($item)}
                                    {/if}
                                {/if}
                            {/if}
                        </div>
                    </div>
                </div>
            </li>
        {/foreach}

        {if !count($category->getData())}
            <li class="empty-category-row">{t}Нет элементов{/t}</li>
        {/if}
    </ul>
</div>