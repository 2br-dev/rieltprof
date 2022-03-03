{hook name="mobilesiteapp-blockscategory:searchline" title="{t}Мобильное приложение - категория:линия поиска{/t}"}
    <ion-searchbar
      [(ngModel)]="query"
      [placeholder]="'Поиск по товарам'"
      (keyup.enter)="onApplySearch()"
      [animated]="true">
    </ion-searchbar>
    <div class="buttons">
      <button ion-button round color="primary" class="app_btn" tappable (click)="onApplySearch()">Поиск</button>
      <button ion-button outline round color="primary" class="app_btn brandsListButton" tappable (click)="openBrandsPage()">Список брендов</button>
    </div>
{/hook}

