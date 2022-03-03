{$url->getDomainStr()}
{t}Логин{/t}: {$data->user.login|default:$data->user.e_mail|default:$data->user.phone}
{t}Пароль{/t}: {$data->password}
