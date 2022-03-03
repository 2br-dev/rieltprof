{addjs file="flot/excanvas.js" basepath="common" before="<!--[if lte IE 8]>" after="<![endif]-->"}
{addjs file="flot/jquery.flot.min.js" basepath="common"}
{addjs file="flot/jquery.flot.tooltip.min.js" basepath="common"}
{addjs file="flot/jquery.flot.resize.js" basepath="common" waitbefore=true}
{addjs file="flot/jquery.flot.pie.js" basepath="common" waitbefore=true}
{addjs file="%shop%/orderstatuses.js" basepath="root" waitbefore=true}
{addcss file="%shop%/orderstatuses.css"}

<div class="order-statuses">
    {if $total}
        <div id="orderStatusesGraph" class="graph" style="height:300px"></div>
        <div class="flc-orderStatusesLegend"></div>
        
        <div class="orderStatusesData">
            <table width="100%">
                <tr align="center" style="font-weight:bold">
                    <td width="33%">{t}Всего{/t}</td>
                    <td width="33%">{t}Открыто{/t}</td>
                    <td width="33%">{t}Завершено{/t}</td>
                </tr>
                <tr align="center">
                    <td>{$total}</td>
                    <td>{$inwork}</td>
                    <td>{$finished}</td>
                </tr>            
            </table>
        </div>
        <script>
            $.allReady(function() {
                var data = {$json_data};
                initOrderStatusesWidget(data);
            });
        </script>    
    {else}
        <div class="empty-widget">
            {t}Нет ни одного заказа{/t}
        </div>
    {/if}
</div>