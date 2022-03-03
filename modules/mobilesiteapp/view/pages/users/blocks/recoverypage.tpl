<div class="authWrapper">
  {hook name="mobilesiteapp-blocksusers:recoverypage" title="{t}Мобильное приложение - авторизация: блок, восстановление пароля{/t}"}
    {literal}
    <ion-list no-lines no-border>
      <ion-item>
        <ion-label floating>E-mail</ion-label>
        <ion-input type="text" [(ngModel)]="email"></ion-input>
      </ion-item>
    </ion-list>
    {/literal}
  {/hook}

  <ion-grid>
    <ion-row>
      <ion-col col-8 offset-2>
        <button ion-button block round text-capitalize tappable (click)="sendRecover()" class="app_btn">Восстановить</button>
      </ion-col>
    </ion-row>
  </ion-grid>

  <!--<div class="auth_buttons" margin text-center>-->
    <!--<button ion-button round text-capitalize tappable (click)="sendAuth()">Войти</button>-->
    <!--&lt;!&ndash;<button ion-button round text-capitalize class="register_button">Регистрация</button>&ndash;&gt;-->
  <!--</div>-->
</div>
