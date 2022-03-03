<!--<div class="cartWrapper">-->
<ng-template [ngIf]="cartdata.items.length">
    {hook name="mobilesiteapp-blockscartpage:summary" title="{t}Мобильное приложение - Корзина:блок, Итог{/t}"}
    <ion-grid class="cartTopLine">
        <ion-row>
            <ion-col col-12 col-md-6>
                <h4 class="cartSummTitle">Общая сумма:</h4>
                <div class="cartSumm" text-right text-md-left>
                    <p [innerHTML]="cartdata.total"></p>
                    <p class="withoutDelivery">без учета доставки</p>
                </div>
            </ion-col>
            {hook name="mobilesiteapp-blockscartpage:buttons" title="{t}Мобильное приложение - Корзина:блок, кнопка заказать по телефону{/t}"}
            <ion-col class="cartButtonWrapper" col-10 col-md-3 offset-1 offset-md-0 *ngIf="!cartdata.has_error">
                <button ion-button round block color="orange" class="app_btn" tappable (click)="onCheckout()">
                    Оформить
                </button>
            </ion-col>
            <ion-col class="cartButtonWrapper" col-10 col-md-3 offset-1 offset-md-0 *ngIf="!cartdata.has_error">
                <button ion-button round block outline color="orange" class="tel_btn app_btn" tappable (click)="onOpenOneClickCart()">
                    Заказать по телефону
                </button>
            </ion-col>
            {/hook}
        </ion-row>

    </ion-grid>
    {/hook}

    <ion-grid class="cartContent" no-padding>
        <ion-row>
            <ion-col col-12 col-md-8 no-padding>
                {hook name="mobilesiteapp-blockscartpage:products" title="{t}Мобильное приложение - Корзина:блок, товары{/t}"}
                    {literal}
                    <ion-grid class="cartItems">
                        <ion-row *ngFor="let cartitem of cartdata.items">
                                <ion-col col-12 class="cartitem">
                                    <div class="itemImg">
                                        <img src="{{cartitem.getImage()['small_url']}}" item-left/>
                                    </div>
                                    <div class="itemDetails">
                                        <h2 [innerHTML]="cartitem.title"></h2>
                                        <p *ngIf="cartitem.barcode">{{cartitem?.barcode}}</p>
                                        <div class="error" *ngIf="cartitem.amount_error">
                                            {{cartitem.amount_error}}
                                        </div>
                                        <div class="cartAdditionalInfo">
                                            <div class="cartItemModel" *ngIf="cartitem.model && !cartitem.multioffers">{{cartitem.model}}</div>
                                            <div class="cartItemMultioffer" *ngIf="cartitem.multioffers">
                                                <div *ngFor="let multioffer of cartitem.multioffers">
                                                    <span [innerHTML]="multioffer.title"></span>: <b [innerHTML]="multioffer.value"></b>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mainAct">
                                            <div class="amountWrapper">
                                                <ion-icon name="remove-circle" color="primary" tappable (click)="onAmountDown(cartitem)"></ion-icon>
                                                <span>{{cartitem.amount}}</span>
                                                <ion-icon name="add-circle" color="primary" tappable (click)="onAmountUp(cartitem)"></ion-icon>
                                            </div>
                                            <button ion-button clear icon-only color="black" tappable (click)="onAskDeleteItem(cartitem)">
                                                <ion-icon class="trash" ios="ios-trash" md="ios-trash"></ion-icon>
                                            </button>
                                        </div>
                                        <div class="cartItemPrice">
                                            {{cartitem.cost}}
                                            <div class="discount" *ngIf="cartitem.isHaveDiscount()">
                                                скидка {{cartitem.discount}}
                                            </div>
                                        </div>
                                    </div>
                                </ion-col>
                                <ion-col col-12 class="subProducts" padding-bottom text-wrap *ngIf="cartitem.sub_products && cartitem.sub_products.length">
                                    <ion-grid>
                                        <ion-row *ngFor="let sub_product of cartitem.sub_products">
                                            <ion-col col-8 col-md-10>
                                                <div class="cartItemTitle" [innerHTML]="sub_product.title"></div>
                                                <div class="mainAct">
                                                    <div class="amountWrapper" *ngIf="sub_product.allow_concomitant_count_edit">
                                                        <ion-icon name="remove-circle" color="primary" tappable (click)="onAmountDown(sub_product)"></ion-icon>
                                                        <span>{{sub_product.amount}}</span>
                                                        <ion-icon name="add-circle" color="primary" tappable (click)="onAmountUp(sub_product)"></ion-icon>
                                                    </div>
                                                </div>
                                                <div class="cartItemPrice">
                                                    {{sub_product.cost}}
                                                    <div class="discount" *ngIf="sub_product.isHaveDiscount()">
                                                        скидка {{sub_product.discount}}
                                                    </div>
                                                </div>
                                            </ion-col>
                                            <ion-col col-4 col-md-2 text-right>
                                                <a *ngIf="!sub_product.checked" tappable (click)="onAddSubProduct(sub_product)" class="subProductAddRemove">Добавить</a>
                                                <a *ngIf="sub_product.checked" tappable (click)="onDeleteSubProduct(sub_product)" class="subProductAddRemove">Удалить</a>
                                            </ion-col>
                                        </ion-row>
                                    </ion-grid>
                                </ion-col>
                        </ion-row>
                    </ion-grid>
                    {/literal}
                {/hook}
                <ng-template [ngIf]="cartdata.errors && cartdata.errors.length">
                    <ion-grid class="cartItems">
                        <ion-row *ngFor="let error of cartdata.errors">
                            <ion-col col-12 class="error" text-center>
                                {literal}{{error}}{/literal}
                            </ion-col>
                        </ion-row>
                    </ion-grid>
                </ng-template>
            </ion-col>

            <ion-col class="couponsWrapper" col-12 col-md-4 no-padding>
                {hook name="mobilesiteapp-blockscartpage:bottom" title="{t}Мобильное приложение - Корзина:блок, подвал{/t}"}
                    {literal}
                    <div class="coupons" *ngIf="cartdata.coupons && cartdata.coupons.length > 0" padding-vertical>
                        <h5 text-center text-md-left>Хотите применить купон к этой покупке?</h5>
                        <div *ngFor="let coupon of cartdata.coupons" margin-top>
                            Скидочный купон <b>{{coupon.code}}</b>
                        </div>
                        <button ion-button round block outline margin-top color="graydark" class="app_btn" tappable (click)="onRemoveCoupon(cartdata.coupons[0])">
                            Удалить
                        </button>
                    </div>
                    <div class="coupons" *ngIf="cartdata.coupons && !cartdata.coupons.length" padding-vertical>
                        <h5 text-center text-md-left>Хотите применить купон к этой покупке?</h5>
                        <div padding-vertical>
                            <ion-item>
                                <ion-input type="text" text-center placeholder="Введите купон" [(ngModel)]="cartdata.add_coupon" [value]="cartdata.add_coupon" (keyup.enter)="onEnterCoupon()"></ion-input>
                            </ion-item>
                        </div>
                        <button ion-button round block margin-top color="primary" class="app_btn" tappable (click)="onEnterCoupon()">
                            Применить
                        </button>
                    </div>
                    {/literal}
                {/hook}
            </ion-col>
        </ion-row>
    </ion-grid>
</ng-template>

<div class="noProducts emptyList" *ngIf="list_is_loaded && !cartdata.items.length" text-center>
    <p>Ваша корзина пуста</p>
</div>

<div class="noProducts emptyList" *ngIf="!list_is_loaded" text-center>
    <p>Подождите идёт загрузка...</p>
</div>

