{$device=$smarty.cookies["page-constructor-device-bootstrap"]}
{if !$device}{$device="md"}{/if}
<ul class="device-selector">
    <li {if $device == 'xs'}class="act"{/if} data-device="xs"><i class="device-xs"></i> <span class="hidden-xs">{t}Телефон{/t}</span></li>
    <li {if $device == 'sm'}class="act"{/if} data-device="sm"><i class="device-sm"></i> <span class="hidden-xs">{t}Планшет{/t}</span></li>
    <li {if $device == 'md'}class="act"{/if} data-device="md"><i class="device-md"></i> <span class="hidden-xs">{t}Настольный ПК{/t}</span></li>
    <li {if $device == 'lg'}class="act"{/if} data-device="lg"><i class="device-lg"></i> <span class="hidden-xs">{t}Большое устройство{/t}</span></li>
</ul>
<div class="bg bootstrap">
    <div class="pageview {$device}">
        {include file="%templates%/gs/bootstrap/container.tpl"
                 section_tpl="%templates%/gs/bootstrap/section.tpl"}
    </div>
</div>