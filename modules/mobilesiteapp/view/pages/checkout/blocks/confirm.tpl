<div class="checkoutAddressWrapper confirmWrapper cartWrapper">
  <ion-grid class="confirmGrid" no-padding no-lines no-border>
    {hook name="mobilesiteapp-blockscheckoutconfirm:confirm" title="{t}Мобильное приложение - оформление подтверждения:подтверждение заказа{/t}"}
    {literal}
    <ion-row>
      <ion-col col-12>
        <h2 ion-text color="black">Сведения о заказе</h2>
      </ion-col>
    </ion-row>
    <ion-row>
      <ion-col col-6>
        Кол-во товаров к оплате
      </ion-col>
      <ion-col col-6 text-right>
        <b>{{cartdata?.checkcount}}</b>
      </ion-col>
    </ion-row>
    <ion-row *ngIf="(cartdata.total_discount_unformatted>0)">
      <ion-col col-6>
        Общая скидка:
      </ion-col>
      <ion-col col-6 text-right>
        <b>{{cartdata?.total_discount}}</b>
      </ion-col>
    </ion-row>
    <ion-row *ngFor="let tax of cartdata?.taxes">
      <ion-col col-6>
        {{tax?.title}}:
      </ion-col>
      <ion-col col-6 text-right>
        <b>{{tax?.cost}}</b>
      </ion-col>
    </ion-row>
    <ion-row *ngIf="cartdata?.delivery">
      <ion-col col-6>
        Доставка: <span [innerHTML]="cartdata?.delivery['title']"></span>
      </ion-col>
      <ion-col col-6 text-right>
        <b>{{cartdata?.delivery['cost']}}</b>
      </ion-col>
    </ion-row>
    <ion-row *ngIf="isNeedToShowPickPoint()">
      <ion-col col-6>
        Пункт самовывоза:
      </ion-col>
      <ion-col col-6 text-right text-wrap>
        <b [innerHTML]="cartdata?.warehouse?.adress"></b>
      </ion-col>
    </ion-row>
    <ion-row *ngIf="isNeedToShowAddress()">
      <ion-col col-6>
        Адрес:
      </ion-col>
      <ion-col col-6 text-right text-wrap>
        <b [innerHTML]="cartdata?.address?.full_address"></b>
      </ion-col>
    </ion-row>
    <ion-row *ngIf="cartdata.payment">
      <ion-col col-6>
        Оплата:
      </ion-col>
      <ion-col col-6 text-right>
        <b [innerHTML]="cartdata?.payment['title']"></b>
      </ion-col>
    </ion-row>
    <ion-row *ngIf="cartdata.payment_commission">
      <ion-col col-6 text-wrap>
        Коммиссия за оплату через "<span [innerHTML]="cartdata.payment_commission['title']"></span>"
      </ion-col>
      <ion-col col-6 text-right>
        <b>{{cartdata.payment_commission['cost']}}</b>
      </ion-col>
    </ion-row>
    <ion-row>
      <ion-col col-6>
        Итого:
      </ion-col>
      <ion-col col-6 text-right>
        <b class="itogo">{{cartdata.total}}</b>
      </ion-col>
    </ion-row>
    <ion-row>
      <ion-col col-12>
        <ion-item no-padding>
          <ion-label floating>Добавить комментарий</ion-label>
          <ion-input [(ngModel)]="comments"></ion-input>
        </ion-item>
      </ion-col>
    </ion-row>

    {/literal}
    {/hook}
  </ion-grid>

  <ion-grid class="confirmGrid" no-padding no-lines no-border>
    <ion-row *ngIf="shop_config.require_license_agree">
      <ion-col col-12>
        <ion-checkbox [(ngModel)]="iagree" class="check"></ion-checkbox> С правилами согласен (<a tappable (click)="showAgreement()">Ознакомиться с правилами</a>)
      </ion-col>
    </ion-row>
    <ion-row>
      <ion-col col-8 offset-2>
        <button ion-button tappable (click)="onConfirm()" class="app_btn" round block>
          Подтвердить
        </button>
      </ion-col>
    </ion-row>
  </ion-grid>
</div>

