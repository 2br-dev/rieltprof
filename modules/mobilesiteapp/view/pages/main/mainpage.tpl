{$mobile_config=ConfigLoader::byModule('mobilesiteapp')}
<ion-header>
  <ion-toolbar color="primary">
    <button ion-button menuToggle icon-only>
      <ion-icon name="menu"></ion-icon>
    </button>
    <ion-title>
      {$SITE.title}
    </ion-title>
  </ion-toolbar>
</ion-header>

<ion-content>
  {hook name="mobilesiteapp-main:mainpage" title="{t}Мобильное приложение - главная страница:главная страница{/t}"}
    {if $mobile_config.banner_zone}
        <banners-blocks-slideshow class="contentBlock" [config]="{ zone: '{$mobile_config.banner_zone}'}" [slideOptions]="{ loop: false, pager: true}"></banners-blocks-slideshow>
    {/if}
    <category-blocks-category class="contentBlock" [config]="{ parent_id: {$mobile_config.root_dir}}" [tablet_sizes]="'{$mobile_config.tablet_root_dir_sizes}'"></category-blocks-category>
    {if $mobile_config.top_products_order}
      <category-blocks-topproducts class="contentBlock" [config]="{ filter: { dir: {$mobile_config.top_products_dir|default:0}}, sort: '{$mobile_config.top_products_order}', page: 1, pageSize: {$mobile_config.top_products_pagesize}}" [config_private]="{ showMoreProducts: false}"></category-blocks-topproducts>
    {/if}
  {/hook}
</ion-content>
