{static_call var=delivery_period_list callback=['\Shop\Model\DeliveryApi', 'getZonesList']}

<div class="deliveryPeriodWrapper">

   <table id="deliveryPeriodList" class="deliveryPeriodList keyvalTable">
      {if !empty($elem.delivery_periods)}
          {foreach $elem.delivery_periods as $k=>$delivery_period}
              <tr class="deliveryPeriodWrapperRow">
                  <td class="kv-key">
                  <select class="list" name="delivery_periods[{$k}][zone]">
                      {foreach $delivery_period_list as $id=>$delivery_period_item}
                          <option value="{$id}" {if $delivery_period.zone==$id}selected{/if}>{$delivery_period_item}</option>
                      {/foreach}
                  </select></td>
                  <td class="kv-val"><input type="text" name="delivery_periods[{$k}][text]" class="deliveryPeriodText" value="{$delivery_period.text}" placeholder="{t}срок доставки{/t}"/></td>
                  <td class="kv-val"><input type="text" name="delivery_periods[{$k}][days_min]" class="deliveryPeriodDaysMin" value="{$delivery_period.days_min}" placeholder="{t}от, дней{/t}"/> </td>
                  <td class="kv-val"><input type="text" name="delivery_periods[{$k}][days_max]" class="deliveryPeriodDaysMax" value="{$delivery_period.days_max}" placeholder="{t}до, дней{/t}"/> </td>
                  <td class="kv-del" width="25"><a class="deliveryPeriodDelete" title="{t}Удалить{/t}"><i class="zmdi zmdi-close c-red"></i></a></td>
              </tr>
          {/foreach}
      {/if}
   </table>
   <button id="deliveryPeriodAdd" class="btn btn-default m-t-10">{t}Добавить ещё строку{/t}</button>
</div>

<table id="deliveryPeriodHiddenContainer" class="hidden deliveryPeriodHiddenContainer">
    <tr class="deliveryPeriodWrapperRow">
        <td class="kv-key">
        <select class="list" name="delivery_periods[0][zone]" disabled>
            {foreach $delivery_period_list as $id=>$delivery_period}
                <option value="{$id}">{$delivery_period}</option>
            {/foreach}
        </select>
        </td>
        <td class="kv-val">
            <input type="text" name="delivery_periods[0][text]" disabled class="deliveryPeriodText" placeholder="{t}срок доставки{/t}"/>
        </td>
        <td class="kv-val"><input type="text" name="delivery_periods[0][days_min]" class="deliveryPeriodDaysMin" disabled placeholder="{t}от, дней{/t}"/> </td>
        <td class="kv-val"><input type="text" name="delivery_periods[0][days_max]" class="deliveryPeriodDaysMax" disabled placeholder="{t}до, дней{/t}"/> </td>
        <td class="kv-del" width="25">
            <a class="deliveryPeriodDelete" title="{t}Удалить{/t}"><i class="zmdi zmdi-close c-red"></i></a>
        </td>
    </tr>
</table>

<script type="text/javascript">
    /**
     * Обновляет аттрибут name в форме
     */
    function reUpdatePeriodsNames()
    {
        $(".deliveryPeriodWrapperRow:not(disabled)").each(function(key) {
            $(".list", $(this)).attr('name', 'delivery_periods['+key+'][zone]');
            $("input.deliveryPeriodText", $(this)).attr('name', 'delivery_periods['+key+'][text]');
            $("input.deliveryPeriodDaysMin", $(this)).attr('name', 'delivery_periods['+key+'][days_min]');
            $("input.deliveryPeriodDaysMax", $(this)).attr('name', 'delivery_periods['+key+'][days_max]');
        });
    }

    $("#deliveryPeriodAdd").on('click', function(){
        var cloneRow = $("#deliveryPeriodHiddenContainer .deliveryPeriodWrapperRow").clone();
        var cnt      = $("#deliveryPeriodList .deliveryPeriodWrapperRow").length;
        $(".list", cloneRow).prop('disabled', false);
        $("input", cloneRow).prop('disabled', false);
        $("#deliveryPeriodList").append(cloneRow);
        reUpdatePeriodsNames();
        return false;
    });

    $("body").on('click', ".deliveryPeriodDelete", function(){
        var row = $(this).closest('.deliveryPeriodWrapperRow');
        row.remove();
        reUpdatePeriodsNames();
        return false;
    });
</script>
