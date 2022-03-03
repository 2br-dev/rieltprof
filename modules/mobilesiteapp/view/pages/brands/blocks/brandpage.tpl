<div class="brandPage" margin>
  {hook name="mobilesiteapp-blocksbrands:brandpage" title="{t}Мобильное приложение - бренды:блоки, страница брендов{/t}"}
    {literal}
    <ion-grid>
      <ion-row>
        <ion-col col-12 col-md-6 class="brandImage" text-center text-md-right>
          <ng-template [ngIf]="brand.getImage()">
            <img src="{{brand.getImage()['small_url']}}"/>
          </ng-template>
        </ion-col>
        <ion-col col-12 col-md-6>
          <div class="description" *ngIf="brand.description">
            <h2>Описание:</h2>
            <div [innerHTML]="brand.description"></div>
          </div>
        </ion-col>
      </ion-row>
    </ion-grid>

    <ion-grid *ngIf="brand.products_count > 0">
      <ion-row>
        <ion-col col-8 offset-2 text-center>
          <button ion-button block outline round class="app_btn" tappable (click)="openBrandProducts()">Товары бренда</button>
        </ion-col>
      </ion-row>
    </ion-grid>
    {/literal}
  {/hook}
</div>

