<div class="btn-group">
    <a class="tool dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="zmdi zmdi-more"></i></a>
    <ul class="dropdown-menu dropdown-menu-right">
        {foreach $tool->getItems() as $item}
            {if !$tool->isItemHidden($item)}
            <li {if $item@first}class="first"{/if}>
                <a {foreach $item.attr as $key => $val}{if $key[0]=='@'}{$key|substr:"1"}{else}{$key}{/if}="{if $key[0]=='@'}{$cell->getHref($val)}{else}{$val}{/if}" {/foreach}>{$item.title}</a></li>
            {/if}
        {/foreach}
    </ul>
</div>