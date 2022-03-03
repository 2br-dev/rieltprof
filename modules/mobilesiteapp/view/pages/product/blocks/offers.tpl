
<div class="offersWrapper" *ngIf="isOffersLoaded() && (product.isOffersUse() || product.isMultiOffersUse() || product.isVirtualMultiOffersUse())">
  <!--Если есть многомерные комплектации -->
  <div class="multiOffers" *ngIf="product.isMultiOffersUse() || product.isVirtualMultiOffersUse()" margin-bottom>
    {hook name="mobilesiteapp-blocksproductoffers:multioffers" title="{t}Мобильное приложение - комплектации товара:Многомерные комплектации{/t}"}
    {literal}
    <div class="multiOffer" *ngFor="let multioffer of product.multioffers;let i = index">

      <!--Если стоит отображать как фото-->
       <ng-template [ngIf]="multioffer.isPhoto()">
         <ion-scroll class="offerPhotoWrapper" margin-bottom scrollX="true" zoom="false">
           <div class="offerPhotoList" padding-left>
            <a class="offerPhoto" tappable (click)="multioffer.setCurrentValue(multioffer_value); changeMultiOffer()"
                 [ngClass]="{'checked': multioffer.isCurrent(multioffer_value)}"
                 [ngStyle]="{'background-image': multioffer_value['images']['small_url'] ? 'url(' + multioffer_value['images']['small_url'] + ')' : null}"
                 *ngFor="let multioffer_value of multioffer.values">
                 <ng-template *ngIf="!multioffer_value['images']['small_url']">
                   {{multioffer_value['value']}}
                 </ng-template>
            </a>
           </div>
         </ion-scroll>
       </ng-template>


      <!--Если стоит отображать многомерные комплектации как список-->
      <ng-template [ngIf]="!multioffer.isPhoto()">
        <!--Список в виде радиокнопок многомерных комплектаций-->
        <ion-list class="radioOffer" radio-group *ngIf="multioffer.property_type == 'radio'" [(ngModel)]="multioffer.current_value">
          <ion-list-header>
            {{multioffer.title}}
          </ion-list-header>
          <ion-item *ngFor="let multioffer_value of multioffer.values">
            <ion-label>{{multioffer_value.value}}</ion-label>
            <ion-radio tappable (click)="multioffer.setCurrentValue(multioffer_value); changeMultiOffer()" [value]="multioffer_value"></ion-radio>
          </ion-item>
        </ion-list>


        <!--Список в виде цветов и картинок многомерных комплектаций-->
        <ng-template [ngIf]="multioffer.property_type == 'color' || multioffer.property_type == 'image'">
          <h2>{{multioffer.title}}</h2>
          <ion-scroll class="offerPhotoWrapper" [ngClass]="{'color' : (multioffer.property_type == 'color') ? true : null}" margin-bottom scrollX="true" zoom="false">
            <div class="offerPhotoList" padding-left>
              <a class="offerPhoto" tappable (click)="multioffer.setCurrentValue(multioffer_value); changeMultiOffer()"
                 [ngClass]="{'color' : (multioffer.property_type == 'color') ? true : null, 'checked': multioffer.isCurrent(multioffer_value)}"
                 [ngStyle]="{'background-color': multioffer_value.color, 'background-image': multioffer_value['images'] ? 'url(' + multioffer_value['images']['small_url'] + ')' : null}"
                 *ngFor="let multioffer_value of multioffer.values" >
              </a>
            </div>
          </ion-scroll>
        </ng-template>

        <!--Простой список многомерных комплектаций-->
        <ion-item *ngIf="multioffer.property_type == 'list'" no-padding>
          <ion-label>{{multioffer.title}}</ion-label>
          <ion-select (ionChange)="multioffer.setCurrentValueFromListValue(); changeMultiOffer()" (click)="setCanChooseVirtualMultiOffers()" [cancelText]="'Отмена'" [okText]="'Выбрать'" [(ngModel)]="multioffer.list_value" [selectOptions]="product.offersSelectOptions">
            <ion-option *ngFor="let multioffer_value of multioffer.values" [value]="multioffer_value.value" [innerHTML]="multioffer_value.value"></ion-option>
          </ion-select>
        </ion-item>
      </ng-template>
    </div>
    {/literal}
    {/hook}
  </div>


  <!-- Если есть простые комплектации -->
  <ion-item class="singleOffer" *ngIf="!product.isMultiOffersUse() && product.isOffersUse()" no-padding>
    {hook name="mobilesiteapp-blocksproduct:offers" title="{t}Мобильное приложение - комплектации товара:Простые комплектации комплектации{/t}"}
      {literal}
        <ion-label>{{product.getOfferCaption()}}</ion-label>
        <ion-select (ionChange)="product.setOfferByListValue(); changeOffer()" [cancelText]="'Отмена'" [okText]="'Выбрать'" [(ngModel)]="product.offer_list_value" [selectOptions]="product.offersSelectOptions">
          <ion-option *ngFor="let offer of product.offers; let i=index" [value]="offer.title" [attr.selected]="(i==0) ? true : null" [innerHTML]="offer.title"></ion-option>
        </ion-select>
      {/literal}
    {/hook}
  </ion-item >
  <!-- Если есть простые комплектации -->
</div>

