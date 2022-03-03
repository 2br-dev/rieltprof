{addcss file="%shop%/orderreport.css"}

<div class="orderReportWrapper">
    {if !empty($order_report_arr)}
       <h2 class="title">{t}Общая статистика{/t}</h2>
       <table>
         <tr>
            <td class="key">{t}Количество заказов{/t}:</td>
            <td class="value">{$order_report_arr.all.orderscount}</td>
         </tr>
         <tr>
            <td class="key">{t}Общая сумма заказов{/t}:</td>
            <td class="value">{$order_report_arr.all.totalcost|format_price} {$currency.stitle}</td>
         </tr>
         <tr>
            <td class="key">{t}Общая сумма без учёта суммы доставок{/t}:</td>
            <td class="value">{$order_report_arr.all.total_without_delivery|format_price} {$currency.stitle}</td>
         </tr>
         <tr>
            <td class="key">{t}Общая сумма за доставку{/t}:</td>
            <td class="value">{$order_report_arr.all.deliverycost|format_price} {$currency.stitle}</td>
         </tr>
       </table>
       
       <div class="reportColumns">
           <div class="col">
               {if !empty($order_report_arr.payment)}
                   <h2 class="title">{t}Статистика по типам оплаты{/t}</h2>
                   <h3>{t}Количество заказов{/t}:</h3>
                   <table class="reportSubTable">
                     {foreach $order_report_arr.payment as $payment_id => $item}
                         <tr>              
                            <td class="key">{$payments[$payment_id]}</td>
                            <td class="value">
                                {$item.orderscount}
                            </td>
                         </tr>
                     {/foreach}
                   </table>
                   
                   <h3>{t}Cумма заказов{/t}:</h3>
                   <table class="reportSubTable">
                     {foreach $order_report_arr.payment as $payment_id=>$item}
                         <tr>              
                            <td class="key">{$payments[$payment_id]}</td>
                            <td class="value">
                                {$item.totalcost|format_price} {$currency.stitle}
                            </td>
                         </tr>
                     {/foreach}
                   </table>
                   
                   <h3>{t}Cумма без учёта суммы доставок{/t}:</h3>
                   <table class="reportSubTable">
                     {foreach $order_report_arr.payment as $payment_id=>$item}
                         <tr>              
                            <td class="key">{$payments[$payment_id]}</td>
                            <td class="value">
                                {$item.total_without_delivery|format_price} {$currency.stitle}
                            </td>
                         </tr>
                     {/foreach}
                   </table>
                   
                   <h3>{t}Cумма за доставку{/t}:</h3>
                   <table class="reportSubTable">
                     {foreach $order_report_arr.payment as $payment_id=>$item}
                         <tr>              
                            <td class="key">{$payments[$payment_id]}</td>
                            <td class="value">
                                {$item.deliverycost|format_price} {$currency.stitle}
                            </td>
                         </tr>
                     {/foreach}
                   </table>
               {/if}
           </div>
           
           <div class="col">
               {if !empty($order_report_arr.delivery)}
                   <h2 class="title">{t}Статистика по типам доставки{/t}</h2>
                   <h3>{t}Количество заказов{/t}:</h3>
                   <table class="reportSubTable">
                     {foreach $order_report_arr.delivery as $delivery_id=>$item}
                         <tr>              
                            <td class="key">{$deliveries[$delivery_id]}</td>
                            <td class="value">
                                {$item.orderscount}
                            </td>
                         </tr>
                     {/foreach}
                   </table>
                   
                   <h3>{t}Cумма заказов{/t}:</h3>
                   <table class="reportSubTable">
                     {foreach $order_report_arr.delivery as $delivery_id=>$item}
                         <tr>              
                            <td class="key">{$deliveries[$delivery_id]}</td>
                            <td class="value">
                                {$item.totalcost|format_price} {$currency.stitle}
                            </td>
                         </tr>
                     {/foreach}
                   </table>
                   
                   <h3>{t}Cумма за доставку{/t}:</h3>
                   <table class="reportSubTable">
                     {foreach $order_report_arr.delivery as $delivery_id=>$item}
                         <tr>              
                            <td class="key">{$deliveries[$delivery_id]}</td>
                            <td class="value">
                                {$item.deliverycost|format_price} {$currency.stitle}
                            </td>
                         </tr>
                     {/foreach}
                   </table>
               {/if}
           </div>
       </div>
    {else}
       <p class="empty">{t}Не заданы параметры запроса списка заказов{/t}</p>
    {/if}
</div>