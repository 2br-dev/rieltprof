{$mobile_config=ConfigLoader::byModule('mobilesiteapp')}
<ng-template [ngIf]="list.length" class="topProductsWrapper">
  <ion-label margin class="topProductsLabel" text-center>
    <h2>Рекомендованные товары</h2>
  </ion-label>
  {hook name="mobilesiteapp-blocksproduct:recommended" title="{t}Мобильное приложение - категория:топ товаров{/t}"}
  <ion-grid class="categoryProductItem" no-lines>
    <ion-row align-items-start text-wrap wrap>

      <ion-col *ngFor="let product of list; trackBy:trackByObject" col-{$mobile_config.mobile_products_size} col-md-{$mobile_config.tablet_products_size} col-lg-3>

        <!-- Получим товар -->
        {include file="%MOBILEPATH%/view/pages/product/one_product.tpl"}

      </ion-col>
    </ion-row>
  </ion-grid>
  {/hook}
</ng-template>