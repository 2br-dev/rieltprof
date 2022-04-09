{foreach from=$list item=product}
<li data-compare-id="{$product.id}">
    <a class="remove" title="{t}Исключить из сравнения{/t}"></a>
    {$main_image=$product->getMainImage()}
    <a href="{$product->getUrl()}" class="image"><img src="{$main_image->getUrl(64,64)}" alt="{$main_image.title|default:"{$product.title}"}"/><!-- 60x75 --> </a>
    <a href="{$product->getUrl()}" class="title">{$product.title}</a>
    <a href="{$product->getMainDir()->getUrl()}" class="categoryName">{$product->getMainDir()->name}</a>
</li>
{/foreach}