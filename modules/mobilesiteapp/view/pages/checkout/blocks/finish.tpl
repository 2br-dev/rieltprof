<div class="checkoutAddressWrapper finishWrapper">
  {hook name="mobilesiteapp-blockscheckoutfinish:finish" title="{t}Мобильное приложение - оформление финишная страница:финиш{/t}"}
  {literal}
  <ion-list no-lines no-border>
    <ion-list-header no-padding text-wrap>
      <h1 ion-text color="black" text-center>Спасибо!</h1>
    </ion-list-header>
    <ion-item no-padding padding-bottom text-wrap text-center>
      Заказ №{{order.order_num}} от {{order.dateof_date}} оформлен!
    </ion-item>
    <ion-item no-padding text-wrap *ngIf="authService.isAuth()" text-center>
      Все детали заказа Вам будут доступны в Вашем <a class="myOrders" tappable (click)="openMyOrder()">Мои заказы</a>
    </ion-item>
    <ion-item no-padding *ngIf="payment.isHaveDocs()">Документы на оплату</ion-item>
    <ion-item no-padding class="orderDocuments" *ngIf="payment.isHaveDocs()">
      <div *ngFor="let doc of payment.docs">
        <a (click)="window.open(doc.link, '_system', 'location=yes')" [innerHTML]="doc.title"></a><br>
      </div>
    </ion-item>
    <ion-item no-padding *ngIf="order.isHaveFiles()">Доступные файлы</ion-item>
    <ion-item no-padding *ngIf="order.isHaveFiles()">
      <div *ngFor="let file of order.files">
        <a (click)="window.open(file.link, '_system', 'location=yes')" [innerHTML]="file.title"></a><br/>
      </div>
    </ion-item>
  </ion-list>
  <ion-grid margin-top>
    <ion-row>
      <ion-col col-8 offset-2>
        <button class="app_btn" ion-button tappable (click)="onFinish()" round block>
          Завершить заказ
        </button>
      </ion-col>
    </ion-row>
    <ion-row *ngIf="isCanPayOnline()">
      <ion-col col-8 offset-2>
        <button class="app_btn" ion-button tappable (click)="onPayOnline()" round block outline>
          Оплатить
        </button>
      </ion-col>
    </ion-row>
  </ion-grid>
  {/literal}
  {/hook}
</div>
