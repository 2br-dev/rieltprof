{$is_viewed = $cell->isViewed()}
<a data-id="{$cell->getObjectId()}"
   data-meter-id="{$cell->getMeterId()}"
   data-view-one-url="{$cell->getViewOneUrl()}"
   class="item-is-viewed{if !$is_viewed} new{/if}"
   title="{if $is_viewed}{t}Прочитано{/t}{else}{t}Не прочитано{/t}{/if}"
   data-viewed-text="{t}Прочитано{/t}"></a>