<input type="checkbox" name="setban" value="1" title="{t}заблокировать{/t}" id="setban" {if $elem.ban_expire}checked{/if}>
<span class="ban-reason">
    {include file=$elem.__ban_expire->getOriginalTemplate() field=$elem.__ban_expire}<br>
    <p>{t}Причина блокировки{/t}</p>
    {include file=$elem.__ban_reason->getOriginalTemplate() field=$elem.__ban_reason}
</span>

<script>
    $(function() {        
        $('#setban').change(function() {
            var context = $(this).closest('td');
            if ($(this).is(':checked')) {
                $('.ban-reason', context).show();
            } else {
                $('[name="ban_expire"]', context).val('');
                $('[name="ban_reason"]', context).val('');
                $('.ban-reason', context).hide();
            }
        }).change();
    });
</script>