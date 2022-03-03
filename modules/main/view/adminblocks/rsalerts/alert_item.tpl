<li>
    <a class="rs-alerts" data-urls='{ "list":"{adminUrl mod_controller="main-block-rsalerts" alerts_do="ajaxgetalerts"}" }'>
        <i class="rs-icon rs-icon-mail">
            {meter key="rs-notice" class="{if $counter_status == "warning"}bg-amber{/if}"}
            <i class="hi-count bg-amber">9</i>
        </i>
        <span>{t}Уведомления{/t}</span>
    </a>
</li>