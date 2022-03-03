<div class="buyOneClickCartWrapper" margin>
  <div class="oneClickCart">
    {hook name="mobilesiteapp-blocksreservationpage:reservationpage" title="{t}Мобильное приложение - товар: блок, заказа товара{/t}"}
      {literal}
      <ion-list>

        <ion-item>
          <ion-label floating>Ваш E-mail</ion-label>
          <ion-input type="text" [(ngModel)]="email" [value]="email"></ion-input>
        </ion-item>

        <ion-item>
          <ion-label floating>Ваш телефон</ion-label>
          <ion-input type="tel" [(ngModel)]="phone" [value]="phone"></ion-input>
        </ion-item>

      </ion-list>
      {/literal}
    {/hook}

    <ion-grid>
      <ion-row>
        <ion-col col-8 offset-2 text-center>
          <button ion-button block round tappable (click)="onSend()" class="app_btn">
            Заказать
          </button>
        </ion-col>
      </ion-row>
    </ion-grid>
  </div>
</div>
