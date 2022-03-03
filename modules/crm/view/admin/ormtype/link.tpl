{addjs file="%crm%/jquery.rs.linkmanager.js"}
<div class="orm-type-link-manager">
    {$links = $field->getLinkedObjects($elem)}
    {$attributes=$field->getAttrArray()}

    <ul class="link-container list-unstyled" data-form-name="{$field->getName()}">
        {foreach $links as $link}
            <li>
                <input type="hidden" name="{$field->getName()}[{$link->getId()}][]" value="{$link->linked_object_id}">
                {$link->getLinkView()}
            </li>
        {/foreach}
    </ul>

    {if !$attributes.disabled}
        <a data-url="{adminUrl do="addLink" mod_controller="crm-linkctrl" link_types=$field->getAllowedLinkTypes()}" class="btn btn-success open-link-manager">{t}Добавить связь{/t}</a>
    {/if}
</div>