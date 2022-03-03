{$mobile_config=ConfigLoader::byModule('mobilesiteapp')}
{hook name="mobilesiteapp-blockscategory:sortlist" title="{t}Мобильное приложение - категория:блок, сортировки и фильтров{/t}"}
  {literal}
  <ion-grid>
    <ion-row *ngIf="category.child && category.child.length > 0">
      <ion-col col-12>
        <ion-scroll class="subCategoryListWrapper" scrollX="true" zoom="false">
          <div class="subCategoryList">
            <button *ngFor="let subcategory of category.child; trackBy:trackByObject" round outline class="brand_button" ion-button tappable (click)="onSelectCategory(subcategory)" [innerHTML]="subcategory.name">
            </button>
          </div>
        </ion-scroll>
      </ion-col>
    </ion-row>
    <ion-row>
      <ion-col col-6>
        <ion-buttons start>
          <button class="flt_btn" clear color="dark" ion-button icon-left tappable (click)="openChangeSort()">
            <ion-icon name="options"></ion-icon>
            {{getCurrentSortName()}}
          </button>
        </ion-buttons>
      </ion-col>
      <ion-col col-6>
        <ion-buttons end *ngIf="category.is_filters_loaded">
          <button clear class="flt_btn" color="dark" icon-left ion-button tappable (click)="openFiltersPage()">
            <ion-icon name="funnel"></ion-icon>
            Фильтры <span *ngIf="category.getFiltersAppliedCount() > 0"> ({{category.getFiltersAppliedCount()}})</span>
          </button>
        </ion-buttons>
      </ion-col>
    </ion-row>
  </ion-grid>
  {/literal}
{/hook}

<ng-template [ngIf]="list">
  {hook name="mobilesiteapp-blockscategory:listproducts" title="{t}Мобильное приложение - категория:блок, список товаров{/t}"}
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

<div class="noProducts emptyList" *ngIf="list_is_loaded && (!list || !list.length)" text-center>
  <p>В данной категории нет товаров. Или неправильно введен поисковый запрос.</p>
</div>

<div class="noProducts emptyList" *ngIf="!list_is_loaded" text-center>
  <p>Подождите идёт загрузка...</p>
</div>

