{t alias="Уведомление об успешной установке"
url={$url->getDomainStr()}
login={$data->data.supervisor_email}
pass= {$data->data.supervisor_password}
domain={$data->data.domain}
admin={$data->data.admin_section}
}
<p>ReadyScript успешно установлен на сайте %url!</p>

<p>Ваши данные для доступа в административную часть сайта:</p>

<p>E-mail (Логин): %login<br>
Пароль: %pass</p>

<p>Перейдите по ссылке, чтобы попасть в административную панель <a href="http://%domain/%admin/">http://%domain/%admin/</a><br>
Перейдите по ссылке, чтобы попасть в клиентскую часть сайта <a href="http://%domain">http://%domain</a></p>

<p>Спасибо, что выбрали ReadyScript.</p>{/t}