{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <h1>{t}Уважаемый, администратор!{/t}</h1>
    <p>{t url = $url->getDomainStr()}В поддержку поступило сообщение (отправленное на сайте %url).{/t}</p>
    {assign var=topic value=$data->support->getTopic()}
    {assign var=user value=$data->support->getUser()}
    <p>{t}Дата{/t}: {$data->support.dateof|date_format}<br>
    {t}Тема переписки{/t}: <strong>{$topic.title}</strong></p>

    <h3>{t}Пользователь{/t}</h3>
    {t}Ф.И.О.{/t}: <strong>{$user->getFio()}</strong><br>
    {t}Телефон{/t}: <strong>{$user.phone}</strong><br>
    E-mail: <strong>{$user.e_mail}</strong><br>

    <h3>{t}Сообщение{/t}</h3>
    {$data->support.message}

    <p><a href="{$router->getAdminUrl(false, ['id' => $topic.id], 'support-supportctrl', true)}">{t}Ответить{/t}</a></p>
{/block}