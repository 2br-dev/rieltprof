{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
     {t alias = "Сообщение пользователю, восстановление пароля"
     site = $url->getDomainStr()
     login = {$data->user.login|default:$data->user.e_mail|default:$data->user.phone}
     pass = $data->password
     }

     <h3>Ваш пароль на сайте %site был изменен.</h3>
     <p>Теперь Вы можете войти в личный кабинет со следующими данными:</p>

     <p>Логин: %login<br>
     Пароль: %pass</p>{/t}
{/block}