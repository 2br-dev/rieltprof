{literal}
    <div class="me_info">
        {t}Выбрано элементов:{/t} <strong>{$param.sel_count}</strong>
    </div>
	{if count($param.ids)==0}
		<div class="me_no_select">
            {t}Для группового редактирования необходимо отметить несколько элементов.{/t}
		</div>
	{else}
{/literal}
{* Шаблон, для генерации шаблона ORM объекта *}
<div class="formbox" {$elem->getClassParameter('formbox_attr_line')}>
    {$groups=$prop->getGroups(true, $switch)}
    {$object_name=$elem->getShortAlias()}
    {if count($groups)>1}
    {* Форма с вкладками *}
    <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
            {foreach $groups as $i => $item}
                <li class="{if $item@first} active{/if}"><a data-target="#{$object_name}-tab{$i}" data-toggle="tab" role="tab">{literal}{$elem->getPropertyIterator()->getGroupName({/literal}{$i})}</a></li>
            {/foreach}
        </ul>
        <form method="POST" action="{literal}{urlmake}{/literal}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none">
            {foreach $groups as $i => $data}
            <div class="tab-pane{if $data@first} active{/if}" id="{$elem->getShortAlias()}-tab{$i}" role="tabpanel">
                {if count($data.items)}
                    {$issetUserTemplate=false}
                    {foreach $data.items as $name => $item}
                        {if $item|is_a:'RS\Orm\Type\UserTemplate'}
                            {literal}{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate(true) field=$elem.__{/literal}{$name}}
                            {$issetUserTemplate=true}
                        {/if}
                    {/foreach}
                    {if !$issetUserTemplate}
                        <table class="otable">
                            {foreach $data.items as $name => $item}
                            {literal}
                            <tr class="editrow">
                                <td class="ochk" width="20">
                                    <input id="me-{/literal}{$object_name}-{$name}{literal}" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__{/literal}{$name}{literal}->getName()}" {if in_array($elem.__{/literal}{$name}{literal}->getName(), $param.doedit)}checked{/if}></td>
                                <td class="otitle">
                                    <label for="me-{/literal}{$object_name}-{$name}{literal}">{$elem.__{/literal}{$name}{literal}->getTitle()}</label>&nbsp;&nbsp;{if $elem.__{/literal}{$name}{literal}->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__{/literal}{$name}{literal}->getHint()|escape}">?</a>{/if}
                                </td>
                                <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate(true) field=$elem.__{/literal}{$name}{literal}}</div></td>
                            </tr>{/literal}
                            {/foreach}
                        </table>
                    {/if}
                {/if}
            </div>
            {/foreach}
        </form>
    </div>
    {else}
        {* Простая форма, без вкладок*}
        <form method="POST" action="{literal}{urlmake}{/literal}" enctype="multipart/form-data" class="crud-form">
            <input type="submit" value="" style="display:none">
            <div class="notabs multiedit">
                {foreach $groups as $i => $data}
                    {foreach $data.items as $item}
                        {if $item|is_a:'RS\Orm\Type\UserTemplate'}
                            {literal}{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate(true) field=$elem.__{/literal}{$name}}
                            {$issetUserTemplate=true}
                        {/if}
                    {/foreach}
                {/foreach}
                
                {if !$issetUserTemplate}
                    <table class="otable">
                        {foreach $groups as $i => $data}
                            {foreach $data.items as $name => $item}
                                {literal}
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-{/literal}{$object_name}-{$name}{literal}" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__{/literal}{$name}{literal}->getName()}" {if in_array($elem.__{/literal}{$name}{literal}->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-{/literal}{$object_name}-{$name}{literal}">{$elem.__{/literal}{$name}{literal}->getTitle()}</label>&nbsp;&nbsp;{if $elem.__{/literal}{$name}{literal}->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__{/literal}{$name}{literal}->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate(true) field=$elem.__{/literal}{$name}{literal}}</div></td>
                                </tr>{/literal}
                            {/foreach}
                        {/foreach}
                    </table>
                {/if}
            </div>
        </form>
    {/if}
</div>
{literal}{/if}{/literal}