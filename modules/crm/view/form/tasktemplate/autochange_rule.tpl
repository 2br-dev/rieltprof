{addcss file="%crm%/autochange_rule.css"}
{addjs file="%crm%/jquery.rs.autochangerule.js"}

<div class="autochange-rules-block" data-urls='{
    "addRule": "{adminUrl do="AjaxGetAutochangeStatusRule" mod_controller="crm-autotaskrulectrl"}",
    "addGroupItem": "{adminUrl do="AjaxGetAutochangeStatusGroupItem" mod_controller="crm-autotaskrulectrl"}",
    "addOrItem": "{adminUrl do="AjaxGetAutochangeStatusOrItem" mod_controller="crm-autotaskrulectrl"}"
 }'>
    <div>
        <a class="btn btn-success add-autochange-rule">{t}Добавить правило смены статуса{/t}</a>
    </div>

    <div class="autochange-rules">
        {include file="%crm%/form/tasktemplate/autochange_rule_parts/rule_item.tpl" rules=$elem.autochange_status_rule_arr statuses=$elem.__status_id->getList()}
    </div>
</div>
<script>
    $.contentReady(function() {
        $('.autochange-rules-block').autoChangeRule();
    });
</script>