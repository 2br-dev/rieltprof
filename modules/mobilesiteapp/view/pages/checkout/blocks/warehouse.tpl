<div class="checkoutAddressWrapper">
  {hook name="mobilesiteapp-blockscheckoutwarehouse:warehouse" title="{t}Мобильное приложение - оформление склады:склады{/t}"}
    {literal}
    <ion-list *ngIf="warehouse_list.length">
      <ion-item *ngFor="let warehouse of warehouse_list" tappable (click)="onSelect(warehouse)" text-wrap>
        <ion-avatar *ngIf="warehouse.getMainImage()" item-left>
          <ion-img [src]="warehouse.getMainImage()['micro_url']"></ion-img>
        </ion-avatar>
        <h2 [innerHTML]="warehouse.title"></h2>
        <p [innerHTML]="warehouse.description"></p>
        <p [innerHTML]="warehouse.adress"></p>
      </ion-item>
    </ion-list>
    {/literal}
  {/hook}
</div>
