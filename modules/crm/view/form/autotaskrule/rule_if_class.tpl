{addjs file="%crm%/jquery.rs.autotaskrule.js"}
<select name="rule_if_class" data-current="{$field->get()}" data-load-form-url="{adminUrl do="AjaxGetRuleFormHtml"}">
    {html_options options=$field->getList() selected=$field->get()}
</select>

</td></tr>
<tbody class="crm_autotask_rule_form">
{if $rule_if_object = $elem->getRuleIfObject()}
    {include file="%crm%/form/autotaskrule/rule_data_form.tpl" rule_if_object=$rule_if_object}
{/if}
</tbody>
<tr><td>

<script>
    $.contentReady(function() {
        $('.crm_autotask_rule_form').autoTaskRule();
    });
</script>