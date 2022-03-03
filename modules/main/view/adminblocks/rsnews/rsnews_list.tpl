{if $paginator->total}
    <div class="updatable" data-update-block-id="side-news" data-update-replace>
        <ul class="side-news">
            {foreach $news_data.news as $item}
                <li class="side-news__item{if !$item.is_viewed} new{/if}" data-id="{$item.id}">
                    <div class="side-news__date">
                        <span class="m-r-5 view-circle" data-placement="right" title="{if $item.is_viewed}{t}Прочитано{/t}{else}{t}Не прочитано{/t}{/if}"></span>
                        {$item.dateofcreate|dateformat:"@date @time"}
                    </div>
                    <a class="side-news__title" href="{$item.href}" target="_blank">{$item.title}</a>
                    <article class="side-news__text">{$item.short_description}</article>
                </li>
            {/foreach}
        </ul>
        {include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line" noUpdateHash=true}
    </div>
{else}
    <div class="rs-side-panel__empty">
        {$error|default:t('Нет новостей')}
    </div>
{/if}