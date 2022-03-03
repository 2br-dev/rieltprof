<ng-template [ngIf]="list_by_divider">
  {hook name="mobilesiteapp-blocksbrands:brandslistpage" title="{t}Мобильное приложение - бренды:блоки, страница со списком брендов{/t}"}
    {literal}
      <ion-item-group *ngFor="let divider of list_by_divider">
        <ion-item-divider>
          <h1> {{divider.letter}} </h1>
        </ion-item-divider>
        <ion-item tappable (click)="onSelectBrand(brand)" *ngFor="let brand of divider.list" no-lines no-border no-padding>
          <ion-grid>
            <ion-row align-items-center>
              <ion-col col-4>
                <img *ngIf="brand.getIcon()" src="{{brand.getIcon()}}"/>
              </ion-col>
              <ion-col col-8>
                <h1 [innerHTML]="brand.title"></h1>
              </ion-col>
            </ion-row>
          </ion-grid>
        </ion-item>
      </ion-item-group>
    {/literal}
  {/hook}
</ng-template>

<div class="emptyList" *ngIf="list_is_loaded && (!list || !list.length)" text-center>
  <p>Список брендов пуст.</p>
</div>

<div class="noProducts emptyList" *ngIf="!list_is_loaded" text-center>
  <p>Подождите идёт загрузка...</p>
</div>

