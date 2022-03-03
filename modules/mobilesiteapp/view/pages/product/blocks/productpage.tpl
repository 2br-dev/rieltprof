{$shop_config=ConfigLoader::byModule('shop')}
{$check_quantity=$shop_config.check_quantity}
{$catalog_config=ConfigLoader::byModule('catalog')}

<ion-grid>
    <ion-row>
        <ion-col class="leftSide" col-12 col-md-6>
            <div class="productTitleWrappper" padding>
                <h1 class="productTitle" [innerHTML]="product.title" margin-bottom></h1>
                <span>Артикул:</span> <b>{literal}{{product.barcode}}{/literal}</b>
            </div>
            <div class="top_buttons" padding>
                <div class="inFavoriteLabel">
                    <ng-template [ngIf]="!product.isInFavorite()">
                        <a tappable (click)="toggleInFavorite()">
                            <ion-icon color="graymilk" name="heart"></ion-icon>
                        </a>
                    </ng-template>

                    <ng-template [ngIf]="product.isInFavorite()">
                        <a tappable (click)="toggleInFavorite()">
                            <ion-icon color="danger" name="heart"></ion-icon>
                        </a>
                    </ng-template>
                </div>
            </div>
            <div class="pos_r">
                <ng-template ngFor let-specdir [ngForOf]="product.specdirs">
                    <div class="specDirs" *ngIf="specdir.image && specdir.image.nano_url" style="width: 50px; height: 50px">
                        <img [src]="specdir.image.nano_url">
                    </div>
                </ng-template>
            </div>
            <div class="topImageWrapper">
                {hook name="mobilesiteapp-blocksproductpage:images" title="{t}Мобильное приложение - товар:блок, картинки{/t}"}
                {literal}
                <ng-template [ngIf]="isShowImages()">
                    <div *ngFor="let offer of product.offers">
                        <ion-slides *ngIf="(offer.id == product.offer.id && product.getImagesByOffer(offer).length>0)" class="slideShowWrapper productImages"
                                    [pager]="slideOptions['pager']" [loop]="slideOptions['loop']">
                            <ion-slide *ngFor="let image of product.getImagesByOffer(offer);">
                                <img [src]="image.big_url"/>
                            </ion-slide>
                        </ion-slides>
                    </div>
                </ng-template>
                {/literal}
                {/hook}
            </div>
            <div class="productLeftInfoSection" *ngIf="product.description.length">
                {hook name="mobilesiteapp-blocksproductpage:description" title="{t}Мобильное приложение - товар: блок описания товара{/t}"}
                <div item-content [innerHTML]="product.description | safeHtml" class="description" padding-top padding-bottom></div>
                {/hook}
            </div>
        </ion-col>
        <ion-col class="rightSide" col-12 col-md-6>
            <div class="productSecondTitleWrappper" padding>
                <h1 class="productTitle" margin-bottom [innerHTML]="product.title"></h1>
                <span>Артикул:</span> <b>{literal}{{product.barcode}}{/literal}</b>
            </div>
            {hook name="mobilesiteapp-blocksproductpage:short_description" title="{t}Мобильное приложение - товар:блок, краткая информации{/t}"}
            <div class="productShortDescription" margin-bottom *ngIf="product.short_description" [innerHTML]="product.short_description"></div>
            {/hook}
            {if $client_version>=1.0}
                {hook name="mobilesiteapp-blocksproductpage:warehouse" title="{t}Мобильное приложение - товар:блок, со складами{/t}"}
                {literal}
                    <div class="productWarehouseInfo" margin-horizontal padding *ngIf="product.isHaveWarehousesInfo()">
                        <div class="titleDiv">Наличие</div>
                        <div class="warehouseRow" *ngFor="let warehouse of product.stick_info.warehouses" tappable (click)="openWarehouse(warehouse)">
                            <div class="title" [innerHTML]="warehouse.title"></div>
                            <div class="stickWrap">
                                <span *ngFor="let stick_num of product.stick_info.stick_ranges" class="stick"
                                      [ngClass]="{'filled': (product.getStickNumForWarehouse(warehouse) >= stick_num)}"
                                      [ngStyle]="{'width.%': product.getWarehouseOneStickWidth()}"></span>
                            </div>
                        </div>
                    </div>
                {/literal}
                {/hook}
            {/if}

            <div class="productActionsWrapper" *ngIf="isOffersLoaded()">
                <!-- Подключим комплектации -->
                {include file="%MOBILEPATH%/view/pages/product/blocks/offers.tpl"}

                {literal}
                <div class="productCost" text-center text-md-left margin-bottom>
                    {{product.getCost()}}
                    <span class="productOldCost" *ngIf="product.isHaveOldCost()">{{product?.getOldCost()}}</span>
                </div>
                <!-- Сопутствующие товары -->
                <div *ngIf="product.isHaveConcominant()" class="concomitantsWrapper">
                    <ion-label no-margin class="topProductsLabel" text-center>
                        <h2>Сопутсвующие товары</h2>
                    </ion-label>

                    <ion-list padding>
                        <ion-item no-padding *ngFor="let concomitant of product.concomitant">
                            <ion-label class="title">{{concomitant.title}} (+{{concomitant.cost_values['cost_format']}})
                            </ion-label>
                            <ion-checkbox [(ngModel)]="concomitant.checked" no-padding></ion-checkbox>
                        </ion-item>
                    </ion-list>
                </div>
                {/literal}
                <div *ngIf="!isAvaliable()" class="notAvaliable" padding-horizontal text-center text-md-left margin-bottom>
                    Нет в наличии
                </div>
                {hook name="mobilesiteapp-blocksproductpage:buttons" title="{t}Мобильное приложение - товар:блок, кнопок{/t}"}
                <!-- Кнопки действия -->
                <ion-grid class="productButtons">
                    {if $shop_config}
                    <ion-row *ngIf="isBuyButtonShow()">
                        <ion-col col-8 offset-2 offset-md-0>
                            <button text-capitalize block ion-button round color="orange" class="app_btn" tappable (click)="addToCart()">Купить</button>
                        </ion-col>
                    </ion-row>
                    <ion-row *ngIf="isReservationButtonShow()">
                        <ion-col col-8 offset-2 offset-md-0>
                            <button text-capitalize class="app_btn oneclick" block round outline color="orange" ion-button tappable (click)="reserve()">Заказать</button>
                        </ion-col>
                    </ion-row>
                    {/if}
                    {if $catalog_config.buyinoneclick}
                    <ion-row *ngIf="isBuyOneClickShow()">
                        <ion-col col-8 offset-2 offset-md-0>
                            <button text-capitalize class="app_btn oneclick tel_btn" block round outline color="orange" ion-button tappable (click)="buyOneClick()">Купить в 1 клик
                            </button>
                        </ion-col>
                    </ion-row>
                    {/if}
                </ion-grid>
                {/hook}
            </div>

            <ion-list class="productInfoSection" no-padding>
                <ion-list-header padding-horizontal class="additionalDataHeader descriptionTab" *ngIf="product.description.length" tappable (click)="toggleTab('description')">
                    <span>Подробное описание</span>
                    <ion-icon *ngIf="!isTabVisible('description')" ios="ios-arrow-down" md="ios-arrow-down"></ion-icon>
                    <ion-icon *ngIf="isTabVisible('description')" ios="ios-arrow-up" md="ios-arrow-up"></ion-icon>
                </ion-list-header>
                <ng-template [ngIf]="isTabVisible('description')">
                    <ion-item no-margin padding-horizontal *ngIf="product.description.length" class="nolabel descriptionTab" no-border no-lines>
                        {hook name="mobilesiteapp-blocksproductpage:description" title="{t}Мобильное приложение - товар: блок описания товара{/t}"}
                        {if $client_version > 1.6}
                            <div item-content [innerHTML]="product.description | safeHtml" class="description" text-justify padding-top padding-bottom></div>
                        {else}
                            <div item-content [innerHTML]="product.description" class="description" text-justify padding-top padding-bottom></div>
                        {/if}
                        {/hook}
                    </ion-item>
                </ng-template>
                {hook name="mobilesiteapp-blocksproductpage:proptabs" title="{t}Мобильное приложение - товар: блок, переключатель характеристик{/t}"}
                <ion-list-header *ngIf="product.property_values.length" padding-horizontal class="additionalDataHeader" tappable (click)="toggleTab('property_values')">
                    <span>Характеристики</span>
                    <ion-icon *ngIf="!isTabVisible('property_values')" ios="ios-arrow-down" md="ios-arrow-down"></ion-icon>
                    <ion-icon *ngIf="isTabVisible('property_values')" ios="ios-arrow-up" md="ios-arrow-up"></ion-icon>
                </ion-list-header>
                <ng-template [ngIf]="isTabVisible('property_values')">
                    <ion-item padding-horizontal class="nolabel" no-border no-lines>
                        {hook name="mobilesiteapp-blocksproductpage:props" title="{t}Мобильное приложение - товар:блок, характеристики{/t}"}
                        {literal}
                        <ion-grid item-content no-margin no-padding>
                            <ng-template ngFor let-property_value [ngForOf]="product?.property_values">
                                <ion-row *ngIf="(property_value.hidden == 0)" align-items-center class="propGroupFld">
                                    <ion-col col-12 [innerHTML]="property_value?.title"></ion-col>
                                </ion-row>
                                <ng-template ngFor let-prop_val [ngForOf]="property_value.list">
                                    <ion-row align-items-center class="propFld">
                                        <ion-col col-6 [innerHTML]="prop_val?.title"></ion-col>
                                        <ion-col col-6 text-right [innerHTML]="prop_val?.text_value"></ion-col>
                                    </ion-row>
                                </ng-template>
                            </ng-template>
                        </ion-grid>
                        {/literal}
                        {/hook}
                    </ion-item>
                </ng-template>
                {/hook}
            </ion-list>
        </ion-col>
    </ion-row>
</ion-grid>
