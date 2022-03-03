{if !empty($brands)}
{$brands=$this_controller->api->divideByLanguage($brands)}
    <div class="brandList">
        <h1 class="fn">{t}Бренды{/t}</h1>
        
        {$number_key=0}
        {if !empty($brands.ENG)}
        <h2><span>ENG</span></h2>
            <div class="list">
                {assign var=letters value=array_keys($brands.ENG)}
                {foreach from=$letters item=letter}
                   {$number_key=$number_key+1}
                    <div class="letter">
                        <div class="lColl">{$letter}</div>
                        <ul class="rColl">
                            {foreach from=$brands['ENG'][$letter] item=brand}
                                <li>
                                    {if !empty($brand.description)}
                                        <a href="{$brand->getUrl()}">{$brand.title}</a>
                                    {else}
                                        {$brand.title}
                                    {/if}
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                    {if $number_key==5}
                        </div>
                        <div class="list">
                        {assign var=number_key value=0}
                    {/if}
                {/foreach}
            </div>
        {/if}

        
        {$number_key=0}
        {if !empty($brands.RU)}
            <h2><span>RU</span></h2>
            <div class="list">
                {assign var=letters value=array_keys($brands.RU)}
                {foreach from=$letters item=letter}
                    {$number_key=$number_key+1}
                    <div class="letter">
                        <div class="lColl">{$letter}</div>
                        <ul class="rColl">
                            {foreach from=$brands['RU'][$letter] item=brand}
                                {if !empty($brand.description)}
                                    <a href="{$brand->getUrl()}">{$brand.title}</a>
                                {else}
                                    {$brand.title}
                                {/if}
                            {/foreach}
                        </ul>
                    </div>
                    {if $number_key==5}
                        </div>
                        <div class="list">
                        {assign var=number_key value=0}
                    {/if}
                {/foreach}
            </div>
        {/if}

    </div>
{else}
    <p class="empty">{t}Нет ни одного бренда{/t}</p>
{/if}