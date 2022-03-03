<table class="otable">
    {$field_manager=$field->getFieldsManager()}
    {$field_manager->setValues($elem[$field_manager->getArrayWrapper()])|devnull}

    {if $field_manager->notEmpty()}
        {foreach $field->getFieldsManager()->getStructure() as $fld}
            <tr>
                <td class="otitle">{$fld.title}</td>
                <td>
                    {$field_manager->getForm($fld.alias)}

                    {$errname=$field_manager->getErrorForm($fld.alias)}
                    {$error=$elem->getErrorsByForm($errname, ', ')}
                    {if !empty($error)}
                        <span class="form-error">{$error}</span>
                    {/if}
                </td>
            </tr>
        {/foreach}
    {/if}
</table>