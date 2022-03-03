{* Шаблон отображает текущий шаг оформления заказа *}

{$steps=[["key" => "address", "text" => "Контакты"]]}
{$config=$this_controller->getModuleConfig()}
{if !$config.hide_delivery}{$steps[]=["key" => "delivery", "text" => "Доставка"]}{/if}
{if !$config.hide_payment}{$steps[]=["key" => "payment", "text" => "Оплата"]} {/if}
{$steps[]=["key" => "confirm", "text" => "Подтверждение"]}
{$cnt=count($steps)}

<div class="t-registration-steps_wrapper hidden-xs">
    {foreach $steps as $n=>$item}
        {if $n+1>$step || $step>$cnt}<span class="t-registration-step{if $step>=$n+1} active{/if}">{else}
            <a class="t-registration-step{if $step>=$n+1} active{/if}" href="{$router->getUrl('shop-front-checkout', ['Act' => $item.key])}">
        {/if}

        <span class="step-image {$item.key}"></span>
        <span>{$item.text}</span>

        {if $n+1>$step || $step>$cnt}</span>{else}</a>{/if}
    {/foreach}
</div>