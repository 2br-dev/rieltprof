{assign var=status value=$cell->getRow()->getStatus()}
<div class="orderStatusColor" style="background: {$status.bgcolor};"></div>{$status.title}
