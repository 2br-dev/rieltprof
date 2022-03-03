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

{$iterator = $field->getTreeList()}
{$api = $iterator->getApi()}
{$attributes = $field->getAttrArray()}

{$multiple = !empty($attributes[$iterator::ATTRIBUTE_MULTIPLE])}
{$disallow_select_branches = !empty($attributes[$iterator::ATTRIBUTE_DISALLOW_SELECT_BRANCHES])}

{if $values && !is_array($values)}
    {$values = array($values)}
{/if}
{$path_to_first = []}
{$valid_values = []}
{foreach $values as $value}
    {if $path = $api->getPathToFirst($value)}
        {$path_to_first[$value] = $path}
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
                    {$last = end($path_to_first[$value])}
                    {if $last.is_spec_dir == 'N'}
                        {$path_ids = []}
                        {foreach $path_to_first[$value] as $orm}
                            {$path_ids[] = $orm[$api->getIdField()] + 0}
                        {/foreach}
                        <li class="tree-select_selected-value-item" data-id="{$value}" data-path-ids='{json_encode($path_ids)}'>
                            <input type="hidden" name="{$form_name}" value="{$value}">
                            <span class="tree-select_selected-value-item_title-path">
                            {foreach array_slice($path_to_first[$value], 0, -1) as $orm}
                                <span class="tree-select_selected-value-item_title-path-part">{$orm[$api->getNameField()]}</span>
                            {/foreach}
                        </span>
                            {$orm = end($path_to_first[$value])}
                            <span class="tree-select_selected-value-item_title-end-part">{$orm[$api->getNameField()]}</span>
                            <i class="tree-select_selected-value-item_remove zmdi zmdi-close"></i>
                        </li>
                    {/if}
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