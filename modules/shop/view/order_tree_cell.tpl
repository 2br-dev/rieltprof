{$row=$cell->getRow()}
{if $cell->getRow('bgcolor') !== null}<div class="vertMiddle orderStatusColor" style="background: {$cell->getRow('bgcolor')}"></div>{/if} 
<span class="vertMiddle">{$cell->getValue()}</span>
<sup>{if is_object($row)}{$row->getOrdersCount()}{else}{$row.orders_count}{/if}</sup>