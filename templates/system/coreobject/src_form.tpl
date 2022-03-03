{* Шаблон, для генерации шаблона ORM объекта *}
<div class="formbox" {$elem->getClassParameter('formbox_attr_line')}>
    {$groups=$prop->getGroups(false, $switch)}
    {if count($groups)>1}
    {* Форма с вкладками *}
    <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
        {foreach $groups as $i => $item}
            <li class="{if $item@first} active{/if}"><a data-target="#{$elem->getShortAlias()}-tab{$i}" data-toggle="tab" role="tab">{literal}{$elem->getPropertyIterator()->getGroupName({/literal}{$i})}</a></li>
        {/foreach}
        </ul>
        <form method="POST" action="{literal}{urlmake}{/literal}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none"/>
            {foreach $groups as $i => $data}
            <div class="tab-pane{if $data@first} active{/if}" id="{$elem->getShortAlias()}-tab{$i}" role="tabpanel">
                {if count($data.items)}
                    {$issetUserTemplate=false}
                    {foreach $data.items as $name => $item}
                        {if $item|is_a:'RS\Orm\Type\UserTemplate'}
                            {literal}{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}}
                            {$issetUserTemplate=true}
                        {/if}
                        {if $item->isHidden()}
                            {literal}{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}}
                        {/if}
                    {/foreach}
                    {if !$issetUserTemplate}
                        <table class="otable">
                            {foreach $data.items as $name => $item}
                                {if !$item->isHidden()}
                                {literal}
                                <tr>
                                    <td class="otitle">{$elem.__{/literal}{$name}{literal}->getTitle()}&nbsp;&nbsp;{if $elem.__{/literal}{$name}{literal}->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__{/literal}{$name}{literal}->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}{literal}}</td>
                                </tr>
                                {/literal}
                                {/if}
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
            <div class="notabs">
                {foreach $groups as $i => $data}
                    {foreach $data.items as $name=>$item}
                        {if $item|is_a:'RS\Orm\Type\UserTemplate'}
                            {literal}{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}}
                            {$issetUserTemplate=true}
                        {/if}
                        {if $item->isHidden()}
                            {literal}{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}}
                        {/if}                        
                    {/foreach}
                {/foreach}
                
                {if !$issetUserTemplate}
                    <table class="otable">
                        {foreach $groups as $i => $data}
                            {foreach $data.items as $name => $item}
                                {if !$item->isHidden()}
                                {literal}
                                <tr>
                                    <td class="otitle">{$elem.__{/literal}{$name}{literal}->getTitle()}&nbsp;&nbsp;{if $elem.__{/literal}{$name}{literal}->getHint() != ''}<a class="help-icon" title="{$elem.__{/literal}{$name}{literal}->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__{/literal}{$name}{literal}->getRenderTemplate() field=$elem.__{/literal}{$name}{literal}}</td>
                                </tr>{/literal}
                                {/if}
                            {/foreach}
                        {/foreach}
                    </table>
                {/if}
            </div>
        </form>
    {/if}
</div>