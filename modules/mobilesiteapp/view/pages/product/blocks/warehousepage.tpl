<div class="productWarehouseWrapper" margin>
    {hook name="mobilesiteapp-blockswarehousepage:warehousepage" title="{t}Мобильное приложение - товар: просмотр склада{/t}"}
      {literal}
      <h1 class="warehouseTitle" [innerHTML]="warehouse.title" margin-bottom></h1>
      <div class="description" *ngIf="(warehouse.description.length > 0)" [innerHTML]="warehouse.description" margin-bottom></div>
      <div class="address" *ngIf="(warehouse.adress.length > 0)" margin-bottom>Адрес: <span [innerHTML]="warehouse.adress"></span></div>
      <div class="phone" *ngIf="(warehouse.phone.length > 0)" margin-bottom>Тел.: <span [innerHTML]="warehouse.phone"></span></div>
      <div class="image" *ngIf="warehouse.getMainImage()" text-center>
        <img src="{{warehouse.getMainImage().original_url}}"/>
      </div>
      <ng-template [ngIf]="warehouse.isHaveMap()">
          <div id="warehouseMap" class="map" [ngStyle]="{'width.%' : 100, 'height.px': 400}"></div>
          <div #mapContainer></div>
      </ng-template>
      {/literal}
    {/hook}
</div>
