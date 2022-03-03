<ng-template [ngIf]="list">
  {hook name="mobilesiteapp-blocksarticle:articleslistpage" title="{t}Мобильное приложение - статьи:блоки, страница со списком статей{/t}"}
    {literal}
    <ion-grid>
      <ion-row>
        <ion-col col-12 *ngFor="let article of list" tappable (click)="onSelectArticle(article)">
          <p class="date" [innerHTML]="article.date"></p>
          <h2 text-wrap [innerHTML]="article.title"></h2>
          <div *ngIf="article.preview_text.length" text-wrap><p [innerHTML]="article.preview_text"></p></div><br/>
          <div *ngIf="article.getImage()"  text-center>
            <img [src]="article.getImage()['big_url']">
          </div>
          <br/>
        </ion-col>
      </ion-row>
    </ion-grid>
    {/literal}
  {/hook}
</ng-template>

<div class="emptyList" *ngIf="!list || !list.length" text-center>
  <p>Список статей пуст.</p>
</div>

<div class="noProducts emptyList" *ngIf="!list_is_loaded" text-center>
  <p>Подождите идёт загрузка...</p>
</div>

