</td></tr>
{if $elem.conf_userfields->notEmpty()}
    {foreach from=$elem.conf_userfields->getStructure() item=fld}
    <tr>
        <td class="otitle">{$fld.title}</td>
        <td>
            {$elem.conf_userfields->getForm($fld.alias)}
            {assign var=errname value=$elem.conf_userfields->getErrorForm($fld.alias)}
            {assign var=error value=$elem->getErrorsByForm($errname, ', ')}
            {if !empty($error)}
                <span class="form-error">{$error}</span>
            {/if}
        </td>
    </tr>
    {/foreach}
{/if}