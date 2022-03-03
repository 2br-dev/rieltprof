<div class="categoryFilters">
    {hook name="mobilesiteapp-blockscategory:filterspagecost" title="{t}Мобильное приложение - фильтры:фильтр по
    цене{/t}"}
    {literal}
        <ion-list *ngIf="filters.price && filters.price.isShow()" no-lines no-border>
            <ion-list-header>
                <h2>Цена</h2>
            </ion-list-header>
            <ion-item padding>
                <div item-content class="range-labels">
                    <ion-label text-left>{{filters.price.interval_from | number:'1.0-2'}}</ion-label>
                    <ion-label text-right>{{filters.price.interval_to | number:'1.0-2'}} {{filters.price.unit}}</ion-label>
                </div>
                <ion-range [dualKnobs]="true" [(ngModel)]="filters.price.value" [min]="filters.price.interval_from"
                           [max]="filters.price.interval_to" [step]="1" [pin]="true">
                </ion-range>
            </ion-item>
        </ion-list>
    {/literal}
    {/hook}

    {hook name="mobilesiteapp-blockscategory:filterspagebrands" title="{t}Мобильное приложение - фильтры:фильтр по
    бренду{/t}"}
    {literal}
        <ion-list *ngIf="filters.brands" no-lines no-border>
            <ion-list-header>
                <h2>Производители</h2>
            </ion-list-header>
            <ion-grid class="categoryProductItem" no-lines>
                <ion-row align-items-start text-wrap wrap>
                    <ion-col *ngFor="let brand of filters.brands" col-6 col-md-3 col-lg-2>
                        <ion-item class="cb">
                            <ion-label [innerHTML]="brand.title"></ion-label>
                            <ion-checkbox [(ngModel)]="brand.checked"></ion-checkbox>
                        </ion-item>
                    </ion-col>
                </ion-row>
            </ion-grid>
        </ion-list>
    {/literal}
    {/hook}
    {hook name="mobilesiteapp-blockscategory:filterspageprops" title="{t}Мобильное приложение - фильтры:фильтр по
    характеристикам{/t}"}
    {literal}
        <ng-template [ngIf]="filters.property">
            <ng-template ngFor let-property_group [ngForOf]="filters.property">
                <ng-template [ngIf]="property_group.properties">
                    <ng-template ngFor let-property [ngForOf]="property_group.properties">
                        <!-- Характеристика Список -->
                        <ion-list *ngIf="property.type == 'list'" no-lines no-border>
                            <ion-list-header>
                                <h2 [innerHTML]="property.title"></h2>
                            </ion-list-header>
                            <ion-grid class="categoryProductItem" no-lines>
                                <ion-row align-items-start text-wrap wrap>
                                    <ion-col *ngFor="let allowed_value of property.allowed_values" col-6 col-md-3 col-lg-2>
                                        <ion-item class="cb">
                                            <ion-label [innerHTML]="allowed_value.value"></ion-label>
                                            <ion-checkbox [(ngModel)]="allowed_value.checked"></ion-checkbox>
                                        </ion-item>
                                    </ion-col>
                                </ion-row>
                            </ion-grid>
                        </ion-list>
                        <!-- Характеристика Цвет или Картинка -->
                        <ion-list *ngIf="property.type == 'color' || property.type == 'image'">
                            <ion-list-header>{{property.title}}</ion-list-header>
                            <ion-scroll class="offerPhotoWrapper" [ngClass]="{'color' : (property.type == 'color') ? true : null}" margin-bottom scrollX="true" zoom="false">
                                <div class="offerPhotoList" padding-left>
                                    <a class="offerPhoto" [ngClass]="{'color' : (property.type == 'color') ? true : null, 'checked': property.isCheckedAllowedValue(allowed_value)}"
                                       tappable (click)="property.onTriggerChecked(allowed_value);"
                                       [ngStyle]="{'background-color': allowed_value.color, 'background-image': allowed_value['image'] ? 'url(' + allowed_value['image']['small_url'] + ')' : null}"
                                       *ngFor="let allowed_value of property.allowed_values">
                                    </a>
                                </div>
                            </ion-scroll>
                        </ion-list>
                        <!-- Характеристика Диапозон и её можно показывать -->
                        <ion-list *ngIf="property.type == 'int' && property.isShowInterval()">
                            <ion-list-header>
                                <h2 [innerHTML]="property.title"></h2>
                            </ion-list-header>
                            <ion-item padding>
                                <div item-content class="range-labels">
                                    <ion-label text-left>{{property.interval_from}}</ion-label>
                                    <ion-label text-right>{{property.interval_to}} {{property.unit}}</ion-label>
                                </div>
                                <ion-range [dualKnobs]="true" [(ngModel)]="property.value" [min]="property.interval_from"
                                           [max]="property.interval_to" [step]="1" [pin]="true">
                                </ion-range>
                            </ion-item>
                        </ion-list>
                        <!-- Характеристика Строка  -->
                        <ion-list *ngIf="property.type == 'string'">
                            <ion-list-header>
                                <h2 [innerHTML]="property.title"></h2>
                            </ion-list-header>
                            <ion-item>
                                <ion-input type="text" placeholder="Введите текст" [(ngModel)]="property.value"></ion-input>
                            </ion-item>
                        </ion-list>
                    </ng-template>
                </ng-template>
            </ng-template>
        </ng-template>
    {/literal}
    {/hook}

    <ion-grid>
        <ion-row>
            <ion-col col-8 offset-2>
                <button ion-button class="app_btn" round block tappable (click)="applyFilters()">Применить</button>
            </ion-col>
        </ion-row>
        <ion-row>
            <ion-col col-8 offset-2>
                <button ion-button class="app_btn" round outline block tappable (click)="clearFilters()">Сбросить</button>
            </ion-col>
        </ion-row>
    </ion-grid>
</div>
