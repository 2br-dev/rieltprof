<ul class="deliveryTypeAdditionalHTML">
    <li>
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'getCallCourierHTML'])}" class="crud-edit crud-sm-dialog">
            {t}Вызов курьера{/t}
        </a>
    </li>
    <li>
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'getStatus'])}" class="crud-edit crud-sm-dialog">
            {t}Получить статусы заказа{/t}
        </a>
    </li>
    <li>
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'getInfo'])}" class="crud-edit crud-sm-dialog">
            {t}Отчёт - информация по заказу{/t}
        </a>
    </li>
    <li>
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'getPrintDocument'])}" class="crud-edit crud-sm-dialog">
            {t}Печатная форма квитанции к заказу{/t}
        </a>
    </li>
    <li>
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'getPrintLabelForm'])}" class="crud-edit crud-sm-dialog">
            {t}Печатная форма ШК-места{/t}
        </a>
    </li>

    <li style="margin-top: 30px;">
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'deleteOrder'])}" class="crud-edit crud-sm-dialog">
            {t}Удалить заказ из СДЭК{/t}
        </a>
    </li>
    <li>
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'recreateOrder'])}" class="crud-edit crud-sm-dialog">
            {t}Пересоздать заказ СДЭК{/t}
        </a>
        <div>
            <small>{t}Если используется пункт выдачи, то он должен быть<br/> предварительно выбран и сохранён у заказа.{/t}</small>
        </div>
    </li>
</ul>