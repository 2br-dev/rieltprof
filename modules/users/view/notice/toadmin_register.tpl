{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <h1>{t}Уважаемый, администратор!{/t}</h1>
    <p>{t site = $url->getDomainStr()}На сайте %site зарегистрирован пользователь!{/t}</p>

    <p>ID: {$data->user.id}<br>
    {t}Ф.И.О.{/t}: {$data->user.surname} {$data->user.name} {$data->user.midname}<br>
    {if $data->user.login != ''}
        {t}Логин:{/t} {$data->user.login}<br>
    {/if}
    {if $data->user.e_mail != ''}
        E-mail: {$data->user.e_mail}<br>
    {/if}
    {if $data->user.phone != ''}
        {t}Телефон:{/t} {$data->user.phone}<br>
    {/if}
    {if $data->user.is_company}{t}Название организации{/t}: {$data->user.company}<br>
    {t}ИНН{/t}: {$data->user.company_inn}<br>
    {/if}
    -------------------------------------<br>
    {t}Логин{/t}: {$data->user.login}<br>
{/block}