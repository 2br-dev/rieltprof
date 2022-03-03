{hook name="mobilesiteapp-blocksbanners:slideshow" title="{t}Мобильное приложение - баннеры:блок, слайдер{/t}"}
  <ion-slides #banners *ngIf="list && list.length" class="slideShowWrapper" [pager]="slideOptions['pager']" [loop]="slideOptions['loop']">
    <ion-slide *ngFor="let item of list" (click)="openBanner(item)">
        <img [src]="item.getUrl()" alt=""/>
    </ion-slide>
  </ion-slides>
{/hook}