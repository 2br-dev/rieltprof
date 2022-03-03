<div class="orderDetails">
    <div class="leftSide">
        {hook name="mobilesiteapp-blocksusers:orderheader" title="{t}Мобильное приложение - заказ: верхний блок{/t}"}
        {literal}
        <ion-grid>
            <ion-row>
                <ion-col col-6 text-wrap>
                    <h2 no-margin>№ {{order.order_num}}</h2>
                </ion-col>
                <ion-col col-6 text-wrap text-right padding-top>
                    <span class="status" *ngIf="order.getStatus()" [ngStyle]="{color: order.getStatus().bgcolor}">{{order.getStatus().title}}</span>
                </ion-col>
            </ion-row>
            <ion-row>
                <ion-col col-12 text-wrap>
                    от <span>{{order.dateof_date}}</span>
                </ion-col>
            </ion-row>
        </ion-grid>
        {/literal}
        {/hook}
    </div>
    <div class="rightSide">
        {hook name="mobilesiteapp-blocksusers:order" title="{t}Мобильное приложение - заказ: блок, товаров{/t}"}
        {literal}
        <ion-list class="detailsList" no-border no-lines *ngIf="order.items.length">
            <ion-list-header >
                <h2>Состав заказа:</h2>
            </ion-list-header>
            <ion-item text-wrap *ngFor="let cartitem of order.items" class="order">
                <div class="orderImg">
                    <img *ngIf="cartitem.getImage()" src="{{cartitem.getImage()['small_url']}}"/>
                </div>
                <div class="orderInfo">
                    <p class="cartItemTitle" [innerHTML]="cartitem.title"></p>
                    <p>{{cartitem.barcode}}</p>
                    <div class="cartAdditionalInfo">
                        <div class="cartItemModel" *ngIf="cartitem.model" [innerHTML]="cartitem.model"></div>
                        <div class="cartItemMultioffer" *ngIf="cartitem.multioffers">
                            <div *ngFor="let multioffer of cartitem.multioffers">
                                <span [innerHTML]="multioffer.title"></span>: <b [innerHTML]="multioffer.value"></b>
                            </div>
                        </div>
                    </div>
                    <div class="price">{{cartitem.price_formatted}}</div>
                    <div class="amountWrapper">
                        Количество: {{cartitem.amount}} шт.
                    </div>
                </div>
            </ion-item>
        </ion-list>
        {/literal}
        {/hook}
    </div>
    <div class="leftSide">
        {hook name="mobilesiteapp-blocksusers:order" title="{t}Мобильное приложение - заказ: блок, дополнительных сведений{/t}"}
        {literal}
        <ion-list class="orderSubList" *ngIf="order.other_items.length" no-border no-lines text-wrap>
            <ion-list-header>
                <h2>Дополнительные сведения:</h2>
            </ion-list-header>
            <ion-item class="orderAdditionalDetails" text-wrap no-margin no-padding>
                <ion-grid>
                    <ion-row *ngFor="let cartitem of order.other_items">
                        <ion-col col-6>
                            <span [innerHTML]="cartitem.title"></span>
                        </ion-col>
                        <ion-col col-6>
                            {{cartitem.price_formatted}}
                        </ion-col>
                    </ion-row>
                    <ion-row *ngIf="order.payment">
                        <ion-col col-6>
                            Тип оплаты:
                        </ion-col>
                        <ion-col col-6>
                            <span [innerHTML]="payment.title"></span>
                        </ion-col>
                    </ion-row>
                    <ion-row *ngIf="order.payment && payment.isHaveDocs()">
                        <ion-col col-6>
                            Документы на оплату:
                        </ion-col>
                        <ion-col col-6>
                            <span *ngFor="let doc of payment.docs"><a (click)="window.open(doc.link, '_system', 'location=yes')" [innerHTML]="doc.title"></a>&nbsp;</span>
                        </ion-col>
                    </ion-row>
                    <ion-row *ngIf="order.isHavePickUpPointOrAddress()">
                        <ion-col col-6>
                            Адрес получения:
                        </ion-col>
                        <ion-col col-6>
                            <span *ngIf="(order.use_addr > 0)" [innerHTML]="address.full_address"></span><br *ngIf="(order.use_addr > 0)"/><span *ngIf="(order.warehouse > 0)"> Пункт выдачи:</span> <span *ngIf="(order.warehouse > 0)" [innerHTML]="warehouse.adress"></span>
                        </ion-col>
                    </ion-row>
                    <ion-row *ngIf="order.contact_person">
                        <ion-col col-6>
                            Контактное лицо:
                        </ion-col>
                        <ion-col col-6>
                            {{order.contact_person}}
                        </ion-col>
                    </ion-row>
                    <ion-row *ngIf="order.comments">
                        <ion-col col-6>
                            Комментарий к заказу:
                        </ion-col>
                        <ion-col col-6>
                            {{order.comments}}
                        </ion-col>
                    </ion-row>
                    <ion-row *ngIf="order.isHaveFiles()">
                        <ion-col col-6>
                            Прикреплённые файлы:
                        </ion-col>
                        <ion-col col-6>
                            <span *ngFor="let file of order.files"><a (click)="window.open(file.link, '_system', 'location=yes')" [innerHTML]="file.title"></a> </span>
                        </ion-col>
                    </ion-row>
                    <ion-row *ngIf="order.isHaveTrackUrl()">
                        <ion-col col-12>
                            <a (click)="window.open(order.track_url, '_system', 'location=yes')">Перейти к отслеживанию</a>
                        </ion-col>
                    </ion-row>
                    <ng-template [ngIf]="order.isHaveAdditionalFields()">
                        <ion-row *ngFor="let additional_field of order.additional_fields">
                            <ion-col col-6>
                                <span [innerHTML]="additional_field.title"></span>:
                            </ion-col>
                            <ion-col col-6>
                                <span [innerHTML]="additional_field.value"></span>
                            </ion-col>
                        </ion-row>
                    </ng-template>
                </ion-grid>
            </ion-item>
        </ion-list>
        <ion-grid *ngIf="authService.isAuth()">
            <ion-row>
                <ion-col col-8 offset-2 text-center>
                    <button ion-button block round class="app_btn" tappable (click)="repeatOrder()">Повторить заказ</button>
                </ion-col>
            </ion-row>
        </ion-grid>
        {/literal}
        {/hook}
    </div>
</div>




