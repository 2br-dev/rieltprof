<div class="brandPage" margin>
  {hook name="mobilesiteapp-blocksarticle:articlepage" title="{t}Мобильное приложение - статьи:блоки, страница статьи{/t}"}
    <h1 [innerHTML]="article.title"></h1>
    <ng-template [ngIf]="article.getImage()">
      <div class="brandImage" *ngIf="article.getImage()" text-center>
        <img [src]="article.getImage()['small_url']"/>
      </div>
    </ng-template>
    <div class="description" *ngIf="article.content" [innerHTML]="article.content"></div>
  {/hook}
</div>

