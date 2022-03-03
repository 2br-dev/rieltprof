{hook name="mobilesiteapp-product:one_product" title="{t}Мобильное приложение - список товаров:блок, отображения товаров{/t}"}
<ion-card>
    <div class="inFavoriteLabel">
        <ng-template [ngIf]="!product.isInFavorite()">
            <a tappable (click)="toggleInFavorite(product)">
                <ion-icon color="graymilk" name="heart"></ion-icon>
            </a>
        </ng-template>

        <ng-template [ngIf]="product.isInFavorite()">
            <a tappable (click)="toggleInFavorite(product)">
                <ion-icon color="danger" name="heart"></ion-icon>
            </a>
        </ng-template>
    </div>
    <ion-card-content tappable (click)="onSelectItem(product)">
        <ng-template ngFor let-specdir [ngForOf]="product.specdirs">
            <div class="specDirs">
                <img *ngIf="specdir.getIcon()" [src]="specdir.getIcon()">
            </div>
        </ng-template>
        <div class="shopItemImg">
            <img class="img" [src]="product.getMainImage()['small_url']"/>
        </div>
        {literal}
        <div class="titleWrapper">
            <div class="title" [innerHTML]="product.title"></div>
        </div>
        <b class="cost">{{product?.cost_values.cost_format}}</b>
        {/literal}
    </ion-card-content>
</ion-card>
{/hook}