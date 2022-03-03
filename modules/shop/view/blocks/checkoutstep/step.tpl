{$steps=[["key" => "address", "text" => "{t}Адрес и<br> контакты{/t}"]]}
{$config=$this_controller->getModuleConfig()}
{if !$config.hide_delivery}{$steps[]=["key" => "delivery", "text" => "{t}Доставка{/t}"]}{/if}
{if !$config.hide_payment}{$steps[]=["key" => "payment", "text" => "{t}Оплата{/t}"]} {/if}
{$steps[]=["key" => "confirm", "text" => "{t}Подтверждение{/t}"]}
{$cnt=count($steps)}

<ul class="checkoutSteps">
    {foreach from=$steps key=n item=item}
    <li class="{$item.key}{if $step==$n+1 || ($n==$cnt-1 && $step>$cnt)} act{/if}{if $step>$n+1} already{/if}">
        {if $n+1>$step || $step>$cnt}<span class="item">{else}
        <a class="item" href="{$router->getUrl('shop-front-checkout', ['Act' => $item.key])}">
        {/if}
            <i class="icon"></i>
            <span class="title">{$item.text}</span>
        {if $n+1>$step || $step>$cnt}</span>{else}</a>{/if}
        <i class="corner"></i>
    </li>
    {/foreach}
</ul>
<div class="afterStepClear"></div>