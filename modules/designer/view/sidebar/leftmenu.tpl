<div id="design-menu-app" @close-d-panel="close"></div>
<script type="text/javascript">
    //Добавим сведения о блока с настройками
    if (!window['global']['designer']){
        window['global']['designer'] = {};
    }
    if (!designer_blocks){
        var designer_blocks = window['global']['designer']['blocks'] || [];
    }
    designer_blocks.push({$settings_json});
    window['global']['designer']['blocks'] = designer_blocks;
</script>
<DesignerSettingsBlock id="designers-block-settings" style="display: none"></DesignerSettingsBlock>