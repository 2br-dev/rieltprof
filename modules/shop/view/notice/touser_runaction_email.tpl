{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {$data->action_template->client_email_message}
{/block}