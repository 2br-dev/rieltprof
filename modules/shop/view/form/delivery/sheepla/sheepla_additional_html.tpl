<ul class="deliveryTypeAdditionalHTML">
    <li>
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'getShortInfo'])}" class="crud-edit">
            {t}Краткие сведения заказа Sheepla{/t}
        </a>
    </li>
    <li>
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'getInfo'])}" class="crud-edit">
            {t}Сведения доставки Sheepla{/t}
        </a>
    </li>
    <li>
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'getStatus'])}" class="crud-edit">
            {t}Сведения заказа Sheepla{/t}
        </a>
    </li>
    <li>
        <a href="{$router->getAdminUrl('orderQuery',['order_id'=>$order.id,'type'=>'delivery','method'=>'getHistory'])}" class="crud-edit">
            {t}История заказа Sheepla{/t}
        </a>
    </li>
</ul>

<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.0.js"></script>