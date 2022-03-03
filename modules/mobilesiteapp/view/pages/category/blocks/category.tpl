{* Мобильное меню *}
<ion-list *ngIf="list && list.length" class="categoryList" no-lines>
    {hook name="mobilesiteapp-blockscategory:category" title="{t}Мобильное приложение - категории:блок, категорий{/t}"}
    <ng-template ngFor let-item [ngForOf]="list">
        <a [attr.detail-push]="isHaveChild(item) ? true : null" tappable (click)="isHaveChild(item) ? onToggleCategory(item) : onSelectItem(item)" no-padding>
            <ng-template [ngIf]="item.getIcon()">
                <span class="blockImgWrapper">
                    <img src="{literal}{{item.getIcon()}}{/literal}" item-left class="blockImg">
                </span>
            </ng-template>
            <h2 class="parent" [innerHTML]="item.name"></h2>
        </a>
        <ion-item-options class="categorySubmenu" *ngIf="isHaveChild(item)" [ngClass]="{ 'open': isShowSubCategory(item)}">
            <a *ngFor="let subitem of item.child" tappable (click)="onSelectItem(subitem)">
                <ng-template [ngIf]="subitem.getIcon()">
                    <span class="blockImgWrapper">
                        <img src="{literal}{{subitem.getIcon()}}{/literal}" item-left class="blockImg"/>
                    </span>
                </ng-template>
                <h2 [innerHTML]="subitem.name"></h2>
            </a>
        </ion-item-options>
    </ng-template>
    {/hook}
</ion-list>

{* Планшетное меню *}
<div *ngIf="tablet_list && tablet_list.length" class="categoryTabletList" no-lines>
    {hook name="mobilesiteapp-blockscategory:category" title="{t}Мобильное приложение - категории:блок, категорий{/t}"}
    <div *ngFor="let col of tablet_list" class="categoryCol" [ngClass]="{ 'M': (col.length == 1) ? true : null}">
        <a tappable (click)="onSelectItem(category)" *ngFor="let category of col" [ngStyle]="{ 'background-image': (category.getBackgroundImage()) ? 'url(' + category.getBackgroundImage() + ')' : null,
        'background-color': category.mobile_background_color}" no-padding>
            <ng-template [ngIf]="!category.getBackgroundImage()">
                <h2 [innerHTML]="category.name"></h2>
            </ng-template>
        </a>
    </div>
    {/hook}
</div>