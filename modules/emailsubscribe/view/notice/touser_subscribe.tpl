{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {$subscribe=$data->subscribe}
    {$user=$subscribe.user}
    {$config=ConfigLoader::byModule('emailsubscribe')}

    {t alias="Email подписка"
        email = $user.email
        dateof = $user.dateof|dateformat:"@date @time"
        link_subscribe = $user->getSubscribeActiveUrl()
        link_unsubscribe = $user->getSubscribeDeActivateUrl()
        site_link = $url->getDomainStr()}
        <p>Спасибо, что подписались на нашу почтовую рассылку, теперь вам будут приходить самые свежие новости от нас.</p>
        <p>Дополнительная информация о подписке:</p>
        <pre>
    Адрес подписки (email) .............. %email<br>
    Дата добавления...................... %dateof</pre>

        {if $config.send_confirm_email}
            <p>Для подтверждения подписки перейдите по следующей ссылке:</p>
            <p><a href="%link_subscribe">Подписаться на рассылку</a></p>
            <p>Внимание! Вы не будете получать сообщения рассылки, пока не подтвердите свою подписку.</p>
        {/if}
        <p>---------------------------------------------------------------------</p>

        <p>Для отписки от рассылки пройдите по ссылке:</p>
        <p><a href="%link_unsubscribe">%link_unsubscribe</a></p>{/t}
{/block}