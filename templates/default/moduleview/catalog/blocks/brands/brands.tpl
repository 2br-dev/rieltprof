{if !empty($brands)}
    <div class="brandLine">
        <h2><span>{t}Бренды{/t}</span></h2>
        <div class="wrapWidth">
            <ul> 
                {foreach $brands as $brand} 
                    {if $brand.image}
                        <li {$brand->getDebugAttributes()}>
                            <a href="{$brand->getUrl()}">
                                <img src="{$brand->__image->getUrl(100,100,'axy')}" alt="{$brand.title}"/>
                            </a>
                        </li>
                    {/if}
                {/foreach}
            </ul>
        </div>
        <a class="onemore" href="{$router->getUrl('catalog-front-allbrands')}">{t}Все бренды{/t}</a>
   </div>
   
{/if}