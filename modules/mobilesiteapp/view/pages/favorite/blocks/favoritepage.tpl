{$mobile_config=ConfigLoader::byModule('mobilesiteapp')}
<ng-template [ngIf]="list">
  {hook name="mobilesiteapp-blocksfavorite:favoritepage" title="{t}Мобильное приложение - избранное:блок, избранное{/t}"}
  <ion-grid class="categoryProductItem">
    <ion-row align-items-start text-wrap wrap justify-content-start>
      <ion-col *ngFor="let product of list; trackBy:trackByObject" col-{$mobile_config.mobile_products_size}
               col-md-{$mobile_config.tablet_products_size} col-lg-3>

        <!-- Получим товар -->
        {include file="%MOBILEPATH%/view/pages/product/one_product.tpl"}

      </ion-col>
    </ion-row>
  </ion-grid>
  {/hook}
</ng-template>

<div class="noProducts emptyList" *ngIf="list_is_loaded && (!list || !list.length)" text-center>
  <p>В избранном нет товаров.</p>
</div>

<div class="noProducts emptyList" *ngIf="!list_is_loaded" text-center>
  <p>Подождите идёт загрузка...</p>
</div>


