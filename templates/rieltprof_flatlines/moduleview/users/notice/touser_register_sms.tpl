{t}Вы успешно зарегистрированы!{/t}
{t}Логин{/t}: {$data->user.login|default:$data->user.e_mail|default:$data->user.phone}
{t}Пароль{/t}: {$data->password}
{$url->getDomainStr()}