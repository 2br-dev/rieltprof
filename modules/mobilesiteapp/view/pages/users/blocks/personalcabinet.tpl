{hook name="mobilesiteapp-blocksusers:parsonalcabinet" title="{t}Мобильное приложение - личный кабинет: блок, личный кабинет{/t}"}
{literal}
<ion-list no-lines no-border>
  <ion-item no-padding>
    <ion-label>Вы</ion-label>
    <ion-select [(ngModel)]="user.is_company" cancelText="Отмена" okText="Выбрать">
      <ion-option [value]="0">Частное лицо</ion-option>
      <ion-option [value]="1">Компания</ion-option>
    </ion-select>
  </ion-item>
  <ion-item no-padding *ngIf="user.isCompany()">
    <ion-label floating>Название компании</ion-label>
    <ion-input type="text" [(ngModel)]="user.company" [value]="user.company"></ion-input>
  </ion-item>
  <ion-item no-padding *ngIf="user.isCompany()">
    <ion-label floating>ИНН</ion-label>
    <ion-input type="text" [(ngModel)]="user.company_inn" [value]="user.company_inn"></ion-input>
  </ion-item>
  <ion-item no-padding>
    <ion-label floating>Фамилия</ion-label>
    <ion-input type="text" [(ngModel)]="user.surname" [value]="user.surname"></ion-input>
  </ion-item>
  <ion-item no-padding>
    <ion-label floating>Имя</ion-label>
    <ion-input type="text" [(ngModel)]="user.name" [value]="user.name"></ion-input>
  </ion-item>
  <ion-item no-padding>
    <ion-label floating>Отчество</ion-label>
    <ion-input type="text" [(ngModel)]="user.midname" [value]="user.midname"></ion-input>
  </ion-item>
  <ion-item no-padding>
    <ion-label floating>Телефон</ion-label>
    <ion-input type="text" [(ngModel)]="user.phone" [value]="user.phone"></ion-input>
  </ion-item>
  <ion-item no-padding>
    <ion-label floating>E-mail</ion-label>
    <ion-input type="text" [(ngModel)]="user.e_mail" [value]="user.e_mail"></ion-input>
  </ion-item>
  <ion-item no-padding>
    <ion-label>Сменить пароль</ion-label>
    <ion-toggle [(ngModel)]="user.changepass" mode="md"></ion-toggle>
  </ion-item>
  <ion-item no-padding *ngIf="user.changepass">
    <ion-label floating>Текущий пароль</ion-label>
    <ion-input type="text" [(ngModel)]="user.pass" [value]="user.pass"></ion-input>
  </ion-item>
  <ion-item no-padding *ngIf="user.changepass">
    <ion-label floating>Новый пароль</ion-label>
    <ion-input type="text" [(ngModel)]="user.openpass" [value]="user.openpass"></ion-input>
  </ion-item>
  <ion-item no-padding *ngIf="user.changepass">
    <ion-label floating>Повтор нового пароля</ion-label>
    <ion-input type="text" [(ngModel)]="user.openpass_confirm" [value]="user.openpass_confirm"></ion-input>
  </ion-item>
  <ion-item no-padding *ngFor="let field of additional_reg_fields">
    <ion-label *ngIf="(!field.isBoolType())" floating>{{field.title}}</ion-label>
    <ion-label *ngIf="(field.isBoolType())">{{field.title}}</ion-label>
    <ion-input *ngIf="field.isStringType()" type="text" [(ngModel)]="field.val" [value]="field.current_val"></ion-input>
    <ion-textarea *ngIf="field.isTextAreaType()" [(ngModel)]="field.val" [value]="field.current_val"></ion-textarea>
    <ion-select *ngIf="field.isListType()" [(ngModel)]="field.val" cancelText="Отмена" okText="Выбрать">
      <ion-option *ngFor="let value of field.values_arr" [value]="value">{{value}}</ion-option>
    </ion-select>
    <ion-checkbox *ngIf="field.isBoolType()" [(ngModel)]="field.val"></ion-checkbox>
  </ion-item>
</ion-list>
{/literal}
{/hook}

<ion-grid>
  <ion-row>
    <ion-col col-8 offset-2>
      <button ion-button block round tappable (click)="onSave()" class="app_btn">Сохранить</button>
    </ion-col>
  </ion-row>
</ion-grid>