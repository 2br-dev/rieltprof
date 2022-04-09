{literal}
{addcss file="%templates%/previewblock.css"}
{addjs file="%templates%/previewblock.js"}
{/literal}
<div class="previewConstructor">
    {include file="%SYSTEM%/coreobject/src_form.tpl"}
    <div class="previewCode" data-url="{literal}{adminUrl element="{$elem.element_type}" do="AjaxRenderPreview" mod_controller="templates-blockctrl" page_id=$elem.page_id}{/literal}">
        <div>
            <p><strong>{t}Предварительный просмотр HTML-кода{/t}</strong></p>
            <p>Для данного элемента будет автоматически сгенерирован следующий код</p>
        </div>
        <div class="previewBody">
            <div class="gray-c text-center">Загрузка...</div>
        </div>
    </div>
</div>