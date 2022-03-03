{*
@param \RS\Orm\Type\AbstractType $field - поле у котороого указан древовидный список
@param object $elem - объект которму принадлежит поле $field
@param string $form_name - (не обязательный) имя формы
@param int|int[] $values - (не обязательный) выбранное значение/список значений
*}
{addjs file="jquery.rs.treeselect.js" basepath="common"}

{if $form_name === null}
    {$form_name = $field->getFormName()}
{/if}
{if $values === null}
    {$values = $field->get()}
{/if}
{if $attributes === null}
    {$attributes = $field->getAttrArray()}
{/if}

{$iterator = $field->getTreeList()}
{$first_elments = $iterator->getFirstElements()}

{$multiple = !empty($attributes[$iterator::ATTRIBUTE_MULTIPLE])}
{$disallow_select_branches = !empty($attributes[$iterator::ATTRIBUTE_DISALLOW_SELECT_BRANCHES])}

{if $values && !is_array($values)}
    {$values = array($values)}
{/if}
{$path_from_root = []}
{$valid_values = []}

{foreach $values as $value}
    {$path = $iterator->getPathFromRoot($value)}
    {if $path}
        {$path_from_root[$value] = $path}
        {$valid_values[] = $value}
    {/if}
{/foreach}

{$method_name = ''}
{if $elem instanceof \RS\Orm\FormObject || $elem instanceof \RS\Orm\ControllerParamObject}
    {$class = get_class($elem->getParentObject())}
    {$method_name = $elem->getParentParamMethod()}
{else}
    {$class = get_class($elem)}
{/if}

{$tree_list_params = [
    'class' => $class,
    'method_name' => $method_name,
    'property' => $field->getName()
]}
{$tree_list_url = $router->getAdminUrl('getTreeChilds', $tree_list_params, 'main-ormfieldrequester')}
<div class="tree-select" data-form-name="{$form_name}" data-tree-list-url="{$tree_list_url}"
        {if $multiple}{$iterator::ATTRIBUTE_MULTIPLE}="1"{/if}
        {if $disallow_select_branches}{$iterator::ATTRIBUTE_DISALLOW_SELECT_BRANCHES}="1"{/if}
>
    <div class="tree-select_selected-box">
        <ul class="tree-select_selected-values">
            {if $valid_values}
                {foreach $valid_values as $value}
                    {$path_ids = []}
                    {foreach $path_from_root[$value] as $node}
                        {$path_ids[] = $node->getID()}
                    {/foreach}
                    <li class="tree-select_selected-value-item" data-id="{$value}" data-path-ids='{json_encode($path_ids)}'>
                        <input type="hidden" name="{$form_name}" value="{$value}">
                        <span class="tree-select_selected-value-item_title-path">
                            {foreach array_slice($path_from_root[$value], 0, -1) as $node}
                                <span class="tree-select_selected-value-item_title-path-part">{$node->getName()}</span>
                            {/foreach}
                        </span>
                        {$node = end($path_from_root[$value])}
                        <span class="tree-select_selected-value-item_title-end-part">{$node->getName()}</span>
                        <i class="tree-select_selected-value-item_remove zmdi zmdi-close"></i>
                    </li>
                {/foreach}
            {else}
                <li class="tree-select_selected-value-stub">{t}- Ничего не выбрано -{/t}</li>
            {/if}
        </ul>
        <div class="tree-select_drop-chevron-box">
            <i class="tree-select_drop-chevron zmdi zmdi-chevron-down"></i>
        </div>
    </div>

    <div class="tree-select_drop-box">
        <div class="tree-select_search-box">
            <input class="tree-select_search-input" placeholder="{t}поиск{/t}">
            <i class="tree-select_search-input-icon zmdi zmdi-search"></i>
        </div>
        <ul class="tree-select_list">
            {include file="%system%/coreobject/type/form/treelistbox_branch.tpl" iterator=$iterator}
        </ul>
    </div>
</div>
{include file="%system%/coreobject/type/form/block_error.tpl"}