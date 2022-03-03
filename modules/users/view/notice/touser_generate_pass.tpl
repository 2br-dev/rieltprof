{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {t alias = "Сообщение пользователю о сформированном новом пароле"
    site = $url->getDomainStr()
    pass = $data->password
    url = $router->getUrl('users-front-profile', [], true)}
        <h1>Уважаемый клиент!</h1>
    <p>В связи с обновлением технологической платформы нашего интернет-магазина,
    Вам автоматически был присвоен новый пароль на сайте %site.</p>
    <p><strong>Ваш новый пароль: %pass</strong></p>
    <p>Изменить пароль можно в любой момент в вашем <a href="%url">личном кабинете</a> на сайте %site.</p>{/t}
{/block}