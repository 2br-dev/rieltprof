<a href="{$cell->getHref($tool->getHrefPattern())}" title="{$tool->getTitle()}" class="tool {$tool->getClass()}" {$cell->getLineAttr($tool)}>
{if $tool->getIconClass()}
    <i class="zmdi zmdi-{$tool->getIconClass()}"></i>
{/if}
</a>