{include file=$elem.__auto_change_status->getOriginalTemplate() field=$elem.__auto_change_status}
</td></tr>
<tr class="auto-close">
    <td class="otitle">{$elem.__auto_change_timeout_days->getDescription()}</td>
    <td>
    {include file=$elem.__auto_change_timeout_days->getOriginalTemplate() field=$elem.__auto_change_timeout_days}
    </td>
</tr>
<tr class="auto-close">
    <td class="otitle">{$elem.__auto_change_from_status->getDescription()}</td>
    <td>
    {include file=$elem.__auto_change_from_status->getRenderTemplate() field=$elem.__auto_change_from_status}
<tr class="auto-close">
    <td class="otitle">{$elem.__auto_change_to_status->getDescription()}</td>
    <td>
    {include file=$elem.__auto_change_to_status->getRenderTemplate() field=$elem.__auto_change_to_status}
    <script>
        $(function() {
            $('[name="auto_change_status"]').change(function() {
                $('tr.auto-close').toggle( $(this).is(':checked') );
            }).change();
        });
    </script>