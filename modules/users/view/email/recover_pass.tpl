{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {t alias="подан запрос на восстановление пароля.." host=$data.host recover=$data.recover_href}
    <p>Здравствуйте!</p>
    <p>На сайте %host подан запрос на восстановление пароля.
    Перейдите по ссылке, чтобы задать новый пароль:</p>
    <p><a href="%recover">%recover</a></p>
    <p>Если Вы не отправляли запрос на восстановление пароля, проигнорируйте данное письмо.</p>

    <p>Автоматическая рассылка %host.</p>{/t}
{/block}