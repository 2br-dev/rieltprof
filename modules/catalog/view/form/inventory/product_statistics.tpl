<form  data-dialog-options='{ "width":"900"}'>
    {if !$result}
        <h3>{t}Товар не состоит ни в одном архивном документе{/t}</h3>
    {else}
        {foreach $result as $warehouse_title => $info}
            <div style="margin-bottom: 20px">
                <h3>{t}Склад:{/t} {$warehouse_title}</h3>
                {foreach $info as $offer_id => $data}
                    <div style="margin: 20px 0">
                        <div class="offer-block hide-table">
                            <h4 style="display: inline;">{t}Комплектация:{/t} {if $data.offer.title}{$data.offer.title}{else}{t}Основная{/t}{/if}, {t}доступно:{/t} {$data.num},</h4>
                            <a data-toggle-class="hide-table" data-target-closest=".offer-block"><span class="nodisplay">{t}Развернуть{/t}</span><span class="show-table">{t}Свернуть{/t}</span></a>
                            <div class="updatable">
                                <table class="rs-space-table table m-t-20">
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
                                        {if $doc.archived != 1}
                                            <tr>
                                                <td>{$doc.date|dateformat:"@date @time:@sec"}</td>
                                                <td>{if $doc.type == $reserve_status || $doc.type == $write_off_status}-{else}+{/if}{abs($doc.amount)}</td>
                                                <td>{$document_titles[$doc.type]}</td>
                                                <td>{$doc.id}</td>
                                            </tr>
                                        {/if}
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
                        </div>
                    </div>
                {/foreach}
            </div>
        {/foreach}
    {/if}
</form>
<style>
    .hide-table .table{
        display: none;
    }
    div .hide-table .show-table{
        display: none;
    }
    .nodisplay{
        display: none;
    }
    div .hide-table .nodisplay{
        display: inline;
    }
</style>