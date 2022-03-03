<div class="buyOneClickCartWrapper" margin>
    <div class="oneClickCart">
        {hook name="mobilesiteapp-blockscartpage:oneclickcartpage" title="{t}Мобильное приложение - Корзина:блок, купить в один клик{/t}"}
        {literal}
        <ion-list>

            <ion-item>
                <ion-label floating>Ваше имя</ion-label>
                <ion-input type="text" [(ngModel)]="name" [value]="name"></ion-input>
            </ion-item>

            <ion-item>
                <ion-label floating>Ваш телефон</ion-label>
                <ion-input type="tel" [(ngModel)]="phone" [value]="phone"></ion-input>
            </ion-item>

            <ion-item *ngFor="let field of additional_fields">
                <ion-label *ngIf="(!field.isBoolType())" floating>{{field.title}}</ion-label>
                <ion-label *ngIf="(field.isBoolType())">{{field.title}}</ion-label>
                <ion-input *ngIf="field.isStringType()" type="text" [(ngModel)]="field.val"
                           [value]="field.current_val"></ion-input>
                <ion-textarea *ngIf="field.isTextAreaType()" [(ngModel)]="field.val"
                              [value]="field.current_val"></ion-textarea>
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
                <ion-col col-8 offset-2 text-center>
                    <button ion-button block round tappable (click)="onSend()" class="app_btn">
                        Отправить
                    </button>
                </ion-col>
            </ion-row>
        </ion-grid>
    </div>
</div>

