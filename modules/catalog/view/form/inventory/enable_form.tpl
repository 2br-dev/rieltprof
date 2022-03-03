<div class="import-csv">
    <form class="crud-form" method="POST" action="{$router->getAdminUrl('EnableControlBySteps', [], 'catalog-inventoryctrl')}" enctype="multipart/form-data" data-dialog-options='{ "width":"500", "height":"300" }'>
        <p>{t}При включении складского учета, произойдет формирование документов инвентаризации каждого склада для того, чтобы сохранить ваши текущие значения остатков товаров и их комплектаций.{/t}</p>
    </form>
</div>