{if $list}
    <ul class="access_branch">
        {foreach $list as $item}
            {if $item->isGroup()}
                {$input_name = 'group_access'}
            {else}
                {$input_name = 'module_access'}
            {/if}
            <li class="access_item">
                <div class="access_oneRight {if $item->isGroup()}access_oneGroup{/if}">
                    <span>{$item->getTitle()}</span>
                    <div class="access_radioBlock">
                        <label class="access_radio">
                            <input type="radio" name="{$input_name}[{$row.class}][{$item->getAlias()}]" value="allow" title="{t}Разрешение{/t}" {if isset($row.access[$item->getAlias()]['allow'])}checked{/if}>
                        </label>
                        <label class="access_radio">
                            <input type="radio" name="{$input_name}[{$row.class}][{$item->getAlias()}]" value="disallow" title="{t}Запрещение{/t}" {if isset($row.access[$item->getAlias()]['disallow'])}checked{/if}>
                        </label>
                        <label class="access_radio">
                            <input type="radio" name="{$input_name}[{$row.class}][{$item->getAlias()}]" value="" title="{t}По умолчанию{/t}" {if !isset($row.access[$item->getAlias()]) && !$item->isGroup()}checked{/if}>
                        </label>
                    </div>
                </div>

                {if $item->isGroup()}
                    {include file="%users%/form/group/recursive_rights_branch.tpl" list=$item->getChilds()}
                {/if}
            </li>
        {/foreach}
    </ul>
{/if}
