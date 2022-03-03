<tr>
    <td>{if $changeType}{$app->autoloadScripsAjaxBefore()}{/if}</td>
    <td>{$type_object->getDescription()}</td>
</tr>
{$type_object->getFormHtml()}
<tr>
    <td colspan="2">{if $changeType}{$app->autoloadScripsAjaxAfter()}{/if}</td>
</tr>