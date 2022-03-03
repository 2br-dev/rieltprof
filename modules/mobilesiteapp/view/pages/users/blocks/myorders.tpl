<div class="myOrdersWrapper">
  <div *ngIf="list.length">
    {hook name="mobilesiteapp-blocksusers:myorders" title="{t}Мобильное приложение - авторизация: блок, мои заказы{/t}"}
      {literal}
      <ion-list no-lines>
          <ion-item text-wrap *ngFor="let order of list">
            <div class="productInfo" tappable (click)="onOpenOrder(order)">
                <h2>№{{ order.order_num }} от {{ order.dateof_date }}</h2>
                <p>Сумма: <b>{{ order.total }}</b></p>
            </div>
            <ion-note item-end text-right [ngStyle]="{'color': order.getStatus().bgcolor}">
                <div class="coloredStatus" tappable (click)="onOpenOrder(order)" [innerHTML]="order.getStatus().title">
                </div>
                <button class="needPayButton" *ngIf="order.isCanOnlinePay()" round ion-button color="danger" tappable (click)="onPayOrder(order)">
                  Оплатить
                </button>
            </ion-note>
          </ion-item>
      </ion-list>
      {/literal}
    {/hook}
  </div>
  <div class="noProducts emptyList" *ngIf="list_is_loaded && (!list || !list.length)" text-center>
    <p>У вас ещё нет заказов.</p>
  </div>
  <div class="noProducts emptyList" *ngIf="!list_is_loaded" text-center>
    <p>Подождите идёт загрузка...</p>
  </div>
</div>
