<div class="updatable">
    <table class="rs-space-table table">
        <thead>
        <tr>
            <th>{t}Дата{/t}</th>
            <th>{t}Количество{/t}</th>
            <th>{t}Тип документа{/t}</th>
            <th>{t}Номер документа{/t}</th>
        </tr>
        </thead>
        <tbody>
        {if $data.archived_num}
            <tr>
                <td><b>{t}В архиве{/t}</b></td>
                <td>{$data.archived_num}</td>
                <td></td>
                <td></td>
            </tr>
        {/if}
        {foreach $data.docs as $doc}
            <tr>
                <td>{$doc.date|dateformat:"@date @time:@sec"}</td>
                <td>{if $doc.type == $reserve_status || $doc.type == $write_off_status}-{else}+{/if}{abs($doc.amount)}</td>
                <td>{$document_titles[$doc.type]}</td>
                <td>{$doc.id}</td>
            </tr>
        {/foreach}
        <tr>
            <td><b>{t}Итого{/t}</b></td>
            <td>{$data.num}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4">
                {include file="%SYSTEM%/admin/widget/paginator.tpl" paginator = $data.paginator}
            </td>
        </tr>
        </tbody>
    </table>
</div>