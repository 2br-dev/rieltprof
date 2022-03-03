{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <h1>{t}Уважаемый, пользователь!{/t}</h1>
    <p>{t domain = $url->getDomainStr()}Из службы поддержки поступило сообщение (отправленное с сайта %domain).{/t}</p>
    {assign var=topic value=$data->support->getTopic()}
    {assign var=user value=$data->user}
    <p>{t}Дата{/t}: {$data->support.dateof}<br>
    {t}Тема переписки{/t}: <strong>{$topic.title}</strong></p>


    <h3>{t}Сообщение{/t}</h3>
    {$data->support.message}

    <p><a href="{$router->getUrl('support-front-support', ['Act' => 'ViewTopic', 'id' => $topic.id], true)}">{t}Ответить{/t}</a></p>
{/block}