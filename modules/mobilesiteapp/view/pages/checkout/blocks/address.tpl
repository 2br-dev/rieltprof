<div class="checkoutAddressWrapper">
  {literal}
  <ion-list no-lines *ngIf="authService.isAuth()" class="userRegistered">
    <ion-item no-padding *ngIf="user.isCompany()">
      Название компании: <b>{{user.company}}</b>
    </ion-item>
    <ion-item no-padding *ngIf="user.isCompany()">
      ИНН: <b>{{user.company_inn}}</b>
    </ion-item>
    <ion-item no-padding>
      Фамилия: <b>{{user.surname}}</b>
    </ion-item>
    <ion-item no-padding>
      Имя: <b>{{user.name}}</b>
    </ion-item>
    <ion-item no-padding>
      Отчество: <b>{{user.midname}}</b>
    </ion-item>
    <ion-item no-padding>
      Телефон: <b>{{user.phone}}</b>
    </ion-item>
    <ion-item no-padding>
      E-mail: <b>{{user.e_mail}}</b>
    </ion-item>
  </ion-list>
  {/literal}
  <ng-template [ngIf]="!authService.isAuth()">
    {hook name="mobilesiteapp-blockscheckoutaddress:user" title="{t}Мобильное приложение - оформление адреса:сведения о пользователе{/t}"}
    {literal}
    <ion-grid>
      <ion-row>
        <ion-col col-12 offset-0 col-md-10 offset-md-1>
          <ion-segment [(ngModel)]="registerType">
            <ion-segment-button [value]="'register'">
              Регистрация
            </ion-segment-button>
            <ion-segment-button [value]="'noregister'">
              Без регистрации
            </ion-segment-button>
          </ion-segment>
        </ion-col>
      </ion-row>
    </ion-grid>
    <ion-list [ngSwitch]="registerType" no-lines no-border>
      <ng-template [ngSwitchCase]="'register'">
          <div class="iHaveAccountLink" text-center>
            <a tappable (click)="openAuth()">У меня уже есть аккаунт, войти</a>
          </div>
          <ion-item no-padding>
            <ion-label>Вы</ion-label>
            <ion-select [(ngModel)]="user.is_company" cancelText="Отмена" okText="Выбрать">
              <ion-option [value]="0">Частное лицо</ion-option>
              <ion-option [value]="1">Компания</ion-option>
            </ion-select>
          </ion-item>
          <ion-item *ngIf="user.isCompany()" no-padding>
            <ion-label floating>Название компании</ion-label>
            <ion-input type="text" [(ngModel)]="user.reg_company" [value]="user.reg_company"></ion-input>
          </ion-item>
          <ion-item *ngIf="user.isCompany()" no-padding>
            <ion-label floating>ИНН</ion-label>
            <ion-input type="text" [(ngModel)]="user.reg_company_inn" [value]="user.reg_company_inn"></ion-input>
          </ion-item>
          <ion-item no-padding>
            <ion-label floating>Фамилия</ion-label>
            <ion-input type="text" [(ngModel)]="user.reg_surname" [value]="user.reg_surname"></ion-input>
          </ion-item>
          <ion-item no-padding>
            <ion-label floating>Имя</ion-label>
            <ion-input type="text" [(ngModel)]="user.reg_name" [value]="user.reg_name"></ion-input>
          </ion-item>
          <ion-item no-padding>
            <ion-label floating>Отчество</ion-label>
            <ion-input type="text" [(ngModel)]="user.reg_midname" [value]="user.reg_midname"></ion-input>
          </ion-item>
          <ion-item no-padding>
            <ion-label floating>Телефон</ion-label>
            <ion-input type="text" [(ngModel)]="user.reg_phone" [value]="user.reg_phone"></ion-input>
          </ion-item>
          <ion-item no-padding>
            <ion-label floating>E-mail</ion-label>
            <ion-input type="text" [(ngModel)]="user.reg_e_mail" [value]="user.reg_e_mail"></ion-input>
          </ion-item>
          <ion-item no-padding>
            <ion-label>Получить пароль на E-mail</ion-label>
            <ion-toggle [(ngModel)]="user.reg_autologin"></ion-toggle>
          </ion-item>
          <ion-item *ngIf="!user.reg_autologin" no-padding>
            <ion-label floating>Пароль</ion-label>
            <ion-input type="text" [(ngModel)]="user.reg_openpass" [value]="user.reg_openpass"></ion-input>
          </ion-item>
          <ion-item *ngIf="!user.reg_autologin" no-padding>
            <ion-label floating>Повтор пароля</ion-label>
            <ion-input type="text" [(ngModel)]="user.reg_pass2" [value]="user.reg_pass2"></ion-input>
          </ion-item>
          <ion-item *ngFor="let field of additional_reg_fields" no-padding>
            <ion-label *ngIf="(!field.isBoolType())" [attr.floating]="!field.isListType()">{{field.title}}</ion-label>
            <ion-label *ngIf="(field.isBoolType())">{{field.title}}</ion-label>
            <ion-input *ngIf="field.isStringType()" type="text" [(ngModel)]="field.val" [value]="field.current_val"></ion-input>
            <ion-textarea *ngIf="field.isTextAreaType()" [(ngModel)]="field.val" [value]="field.current_val"></ion-textarea>
            <ion-select  *ngIf="field.isListType()" [(ngModel)]="field.val" cancelText="Отмена" okText="Выбрать">
              <ion-option *ngFor="let value of field.values_arr" [value]="value">{{value}}</ion-option>
            </ion-select>
            <ion-checkbox *ngIf="field.isBoolType()" [(ngModel)]="field.val"></ion-checkbox>
          </ion-item>
      </ng-template>
      <ng-template [ngSwitchCase]="'noregister'">
          <ion-item no-padding [ngClass]="{'noRegisterPhone' : (!configService.configs['shop']['require_email_in_noregister'] && !configService.configs['shop']['require_phone_in_noregister']) ? true : null}">
            <ion-label floating>Ф.И.О.</ion-label>
            <ion-input type="text" [(ngModel)]="user.user_fio" [value]="user.user_fio"></ion-input>
          </ion-item>
          <ion-item *ngIf="(configService.configs['shop']['require_email_in_noregister'] == 1)" [ngClass]="{'noRegisterPhone' : (!configService.configs['shop']['require_phone_in_noregister']) ? true : null}" no-padding>
            <ion-label floating>E-mail</ion-label>
            <ion-input type="text" [(ngModel)]="user.user_email" [value]="user.user_email"></ion-input>
          </ion-item>
          <ion-item *ngIf="(configService.configs['shop']['require_phone_in_noregister'] == 1)" class="noRegisterPhone" no-padding>
            <ion-label floating>Телефон</ion-label>
            <ion-input type="text" [(ngModel)]="user.user_phone" [value]="user.user_phone"></ion-input>
          </ion-item>
      </ng-template>
  </ion-list>

    {/literal}
    {/hook}
  </ng-template>
  {hook name="mobilesiteapp-blockscheckoutaddress:address" title="{t}Мобильное приложение - оформление адреса:адрес{/t}"}

    <ion-list no-border no-lines>
      <ion-item no-padding *ngIf="have_pickup_points">
        <ion-label>Выбрите способ забрать товар</ion-label>
        <ion-select [(ngModel)]="only_pickup_points" cancelText="Отмена" okText="Выбрать">
          <ion-option [value]="1">Самовывоз</ion-option>
          {if $client_version >= 1.2}
            <ion-option *ngIf="isShowAddressFields()" [value]="0">Доставка по адресу</ion-option>
          {else}
            <ion-option [value]="0">Доставка по адресу</ion-option>
          {/if}
        </ion-select>
      </ion-item>
    </ion-list>
  {literal}
    <ion-list radio-group no-border *ngIf="only_pickup_points==0" [(ngModel)]="use_addr">
      <ion-list-header *ngIf="(only_pickup_points==0)" no-padding>
        <h2 ion-text color="black">Адрес</h2>
      </ion-list-header>
      <ion-item no-padding *ngFor="let address of address_list">
        <ion-label text-wrap>{{address.full_address}}</ion-label>
        <ion-radio [value]="address.id" [checked]="(use_addr==address.id) ? true : null"></ion-radio>
      </ion-item>
      <ion-item no-padding *ngIf="address_list.length">
        <ion-label>Другой адрес</ion-label>
        <ion-radio [value]="0" [checked]="(!use_addr) ? true : null"></ion-radio>
      </ion-item>
    </ion-list>
  {/literal}
    <ion-list class="newAddressBlock" no-border no-lines>
      {if $client_version >= 1.2}
        <ion-item no-padding *ngIf="isShowAddressCountry()" tappable (click)="onChooseCountry()">
          <ion-label floating>Страна</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_country" (focus)="onChooseCountry()"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowAddressRegionSelector()" tappable (click)="onChooseRegion()">
          <ion-label floating>Регион/край</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_region" (focus)="onChooseRegion()"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowAddressRegion()">
          <ion-label floating>Регион/край</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_region"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowAddressCitySelector()" tappable (click)="onChooseCity()">
          <ion-label floating>Город</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_city" (focus)="onChooseCity()"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowAddressCity()">
          <ion-label floating>Город</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_city"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowAddressZipcode()">
          <ion-label floating>Индекс</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_zipcode"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowAddress()">
          <ion-label floating>Адрес</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_address"></ion-input>
        </ion-item>
        <ion-item class="contactPerson" *ngIf="(configService.configs['shop']['show_contact_person'] == 1)" no-padding>
          <ion-label floating>Контактное лицо</ion-label>
          <ion-input type="text" [(ngModel)]="contact_person"></ion-input>
        </ion-item>
      {else}
        <ion-item no-padding *ngIf="isShowNewAddress()" tappable (click)="onChooseCountry()">
          <ion-label floating>Страна</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_country" (focus)="onChooseCountry()"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowNewAddress() && isHaveRegions()" tappable (click)="onChooseRegion()">
          <ion-label floating>Регион/край</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_region" (focus)="onChooseRegion()"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowNewAddress() && !isHaveRegions()">
          <ion-label floating>Регион/край</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_region"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowNewAddress() && isHaveCity()" tappable (click)="onChooseCity()">
          <ion-label floating>Город</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_city" (focus)="onChooseCity()"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowNewAddress() && !isHaveCity()">
          <ion-label floating>Город</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_city"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="(isShowNewAddress() && (configService.configs['shop']['require_zipcode'] == 1))">
          <ion-label floating>Индекс</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_zipcode"></ion-input>
        </ion-item>
        <ion-item no-padding *ngIf="isShowNewAddress() && (configService.configs['shop']['require_address'] == 1)">
          <ion-label floating>Адрес</ion-label>
          <ion-input type="text" [(ngModel)]="new_address.addr_address"></ion-input>
        </ion-item>
        <ion-item class="contactPerson" no-padding>
          <ion-label floating>Контактное лицо</ion-label>
          <ion-input type="text" [(ngModel)]="contact_person"></ion-input>
        </ion-item>
      {/if}
    {literal}
      <ng-template [ngIf]="additional_order_fields.length">
        <ion-list-header no-padding>
          <h2 ion-text color="black">Дополнительные сведения</h2>
        </ion-list-header>
        <ion-item no-padding *ngFor="let field of additional_order_fields">
          <ion-label *ngIf="(!field.isBoolType() && !field.isListType())" floating>{{field.title}}</ion-label>
          <ion-label *ngIf="(field.isBoolType() || field.isListType())">{{field.title}}</ion-label>
          <ion-input *ngIf="field.isStringType()" type="text" [(ngModel)]="field.val" [value]="field.current_val"></ion-input>
          <ion-input *ngIf="field.isTextAreaType()" type="text" [(ngModel)]="field.val" [value]="field.current_val"></ion-input>
          <ion-select  *ngIf="field.isListType()" [(ngModel)]="field.val" cancelText="Отмена" okText="Выбрать">
            <ion-option *ngFor="let value of field.values_arr" [value]="value" [innerHTML]="value"></ion-option>
          </ion-select>
          <ion-checkbox *ngIf="field.isBoolType()" [(ngModel)]="field.val"></ion-checkbox>
        </ion-item>
      </ng-template>

    </ion-list>
  {/literal}
  {/hook}
  <ng-template>

  </ng-template>
  <ion-grid>
    <ion-row>
      <ion-col col-12 *ngIf="site_config.enable_agreement_personal_data">
        <p class="policy-agreement">Нажимая кнопку "Далее" я даю согласие на <a (click)="openFZ152()">обработку персональных данных</a>.</p>
      </ion-col>
      <ion-col col-8 offset-2>

        <button class="app_btn" ion-button tappable (click)="onNext()" round block>
          Далее
        </button>
      </ion-col>
    </ion-row>
  </ion-grid>
</div>


