<div class="checkoutAddressWrapper deliveryPaymentWrapper">

  {hook name="mobilesiteapp-blockscheckoutdeliverypayment:delivery" title="{t}Мобильное приложение - оформление доставки и оплаты:доставка{/t}"}
  {literal}
    <ion-list *ngIf="delivery_list.length" no-lines>
      <ion-list-header no-padding>
        <h2 ion-text color="black">Доставка</h2>
      </ion-list-header>
      <ion-item no-padding>
          <ion-label>Способ доставки</ion-label>
          <ion-select (ionChange)="onDeliveryChange()" [(ngModel)]="current_delivery" placeholder="-Не выбрано-" okText="Выбрать" cancelText="Отмена">
            <ion-option *ngFor="let delivery of delivery_list" [value]="delivery" text-wrap>{{delivery.title}} <span *ngIf="delivery.cost !== false">({{delivery.cost}})</span></ion-option>
          </ion-select>
      </ion-item>
      <ion-item no-padding>
        <div id="deliveryWrapper{{delivery.id}}" text-wrap *ngFor="let delivery of delivery_list">
          <div class="deliveryBlock" [ngClass]="{'show': isCurrentDelivery(delivery)}">
            <div class="infoBlock clear">
              <img class="image" *ngIf="delivery.getMainImage()" [src]="delivery.getMainImage()['nano_url']"/>
              <div *ngIf="!delivery.error" class="description" margin-bottom>{{delivery.description}}</div>
              <div *ngIf="delivery.error" class="error" margin-bottom>{{delivery.error}}</div>
            </div>
            <div class="warehouseChoose clear" *ngIf="delivery.isMySelfPickup() && isCanShowWarehousesList()">
              <b class="title">Пункт выдачи:</b>
              <div class="description">
                <span [innerHTML]="current_warehouse.title"></span>
                <button ion-button round tappable class="warehouseChooseButton" (click)="onWarehouseChange()">Выбрать</button>
              </div>
            </div>
            <div #additionalHTML id="deliveryAdditionalHTML{{delivery.id}}" class="additionalInfo clear">

            </div>
            <div class="cost clear" *ngIf="delivery.cost !== false">
              <b class="title">Стоимость:</b>
              <div class="description">
                {{delivery.cost}}
              </div>
            </div>
          </div>
        </div>
      </ion-item>
    </ion-list>
  {/literal}
  {/hook}

  <ng-template [ngIf]="isCanShowPaymentsList()">
    {hook name="mobilesiteapp-blockscheckoutdeliverypayment:payment" title="{t}Мобильное приложение - оформление доставки и оплаты:доставка{/t}"}
    {literal}
      <ion-list *ngIf="payment_list.length" no-lines>
        <ion-list-header no-padding>
          <h2 ion-text color="black">Оплата</h2>
        </ion-list-header>
        <ion-item no-padding>
          <ion-label>Способ оплаты</ion-label>
          <ion-select [(ngModel)]="current_payment" placeholder="-Не выбрано-" okText="Выбрать" cancelText="Отмена">
            <ion-option *ngFor="let payment of payment_list" [value]="payment" text-wrap>{{payment.title}} <span *ngIf="payment.isHaveCommission()">(комиссия: {{payment.commission}}%)</span></ion-option>
          </ion-select>
        </ion-item>
        <ion-item no-padding>
          <div id="paymentWrapper{{payment.id}}" text-wrap *ngFor="let payment of payment_list">
            <div class="paymentBlock" *ngIf="isCurrentPayment(payment)">
              <div class="infoBlock" tappable>
                <img class="image" *ngIf="payment.getMainImage()" [src]="payment.getMainImage()['nano_url']"/>
                <div *ngIf="!payment.error" class="description" margin-bottom>{{payment.description}}</div>
                <div *ngIf="payment.error" class="error" margin-bottom>{{payment.error}}</div>
              </div>
              <div class="cost clear" *ngIf="payment.isHaveCommission()">
                <b class="title">Комиссия:</b>
                <div class="description">
                  {{payment.commission}}%
                </div>
              </div>
            </div>
          </div>
        </ion-item>
      </ion-list>
    {/literal}
    {/hook}
  </ng-template>

  <ion-grid  *ngIf="isCanShowNext()">
    <ion-row>
      <ion-col col-8 offset-2>
        <button ion-button tappable (click)="onNext()" class="app_btn" round block>
          Далее
        </button>
      </ion-col>
    </ion-row>
  </ion-grid>
</div>

