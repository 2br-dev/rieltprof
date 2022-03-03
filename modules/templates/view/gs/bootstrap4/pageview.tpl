{$device=$smarty.cookies["page-constructor-device-bootstrap4"]}
{if !$device}{$device="lg"}{/if}
<ul class="device-selector bootstrap4">
    <li {if $device == 'xs'}class="act"{/if} data-device="xs"><i class="device-xs"></i> <span class="hidden-xs">{t}<576px{/t}</span></li>
    <li {if $device == 'sm'}class="act"{/if} data-device="sm"><i class="device-sm"></i> <span class="hidden-xs">{t}≥576px{/t}</span></li>
    <li {if $device == 'md'}class="act"{/if} data-device="md"><i class="device-md"></i> <span class="hidden-xs">{t}≥768px{/t}</span></li>
    <li {if $device == 'lg'}class="act"{/if} data-device="lg"><i class="device-lg"></i> <span class="hidden-xs">{t}≥992px{/t}</span></li>
    <li {if $device == 'xl'}class="act"{/if} data-device="xl"><i class="device-xl"></i> <span class="hidden-xs">{t}≥1200px{/t}</span></li>
</ul>
<div class="bg bootstrap bootstrap4">
    <div class="pageview {$device}">
        {include file="%templates%/gs/bootstrap4/container.tpl"
                 section_tpl="%templates%/gs/bootstrap4/section.tpl"}
    </div>
</div>