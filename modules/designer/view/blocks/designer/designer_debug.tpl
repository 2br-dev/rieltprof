{addcss file="%designer%/app/designer.css"}
<script type="text/javascript">
    if (!designer_blocks){
        var designer_blocks = window['global']['designer']['blocks'] || [];
    }
    {$json=$this_controller->getJSONParams()}
    {if !empty($json)}
        designer_blocks.push({$json});
    {/if}
    window['global']['designer']['blocks'] = designer_blocks;
</script>
{$this_controller->setBlocksIsLoaded()}
<DesignerRenderBlock class="designer-block {if empty($settings.0.row.id)}d-empty{/if}" data-id="{$this_controller->getModuleId()}"></DesignerRenderBlock>