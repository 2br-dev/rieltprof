{$row=$cell->getRow()}
{if $cell->getRow('bgcolor') !== null}<div class="vertMiddle orderStatusColor" style="background: {$cell->getRow('bgcolor')}"></div>{/if} 
<span class="vertMiddle">{$cell->getValue()}</span>