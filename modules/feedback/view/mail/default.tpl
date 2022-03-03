<html>
    <body>
        <p>{t}Ответ на форму №{/t}{$mail->getForm()->id} ({$mail->getForm()->title}):</p>

        <table border="1" cellpadding="5" style="border-collapse:collapse;" >
            {foreach from=$mail->getFields() item=field}
                {if $field.field.show_type != 'captcha'}
                    <tr>
                        <td>
                           {$field.field.title}
                        </td>
                        <td>
                            {if is_array($field.value)}
                                {implode(', ', $field.value)}
                            {else}
                                {$field.value}
                            {/if}
                        </td>
                    </tr>
                {/if}
            {/foreach}
            {$hvalues = $mail->getForm()->getHiddenValues()}
            {if $hvalues}
               {foreach $hvalues as $k => $hv}
                    <tr>
                        <td>
                           {$k}
                        </td>
                        <td>
                           {$hv}
                        </td>
                    </tr>
               {/foreach}
            {/if}
        </table>

        <p>
            {t}Форма отправлена с сайта{/t} "{$mail->host_title}"<br>
            {t}URL с которого отравлена форма{/t}: {$url->getSelfUrl()}
        </p>
        <p>{$mail->host}</p>
    </body>
</html>