{$topic=$data->support->getTopic()}
{$user=$data->support->getUser()}

<h1>{t}Сообщение в поддержку{/t}</h1>

<p>{t}Дата{/t}: {$data->support.dateof|dateformat:"@date @time"}<br>
{t}Тема переписки{/t}: <strong>{$topic.title}</strong></p>

<h3>{t}Пользователь{/t}</h3>
{t}Ф.И.О.{/t}: <strong>{$user->getFio()}</strong><br>
{t}Телефон{/t}: <strong>{$user.phone}</strong><br>
E-mail: <strong>{$user.e_mail}</strong><br>

<h3>{t}Сообщение{/t}</h3>
{$data->support.message}