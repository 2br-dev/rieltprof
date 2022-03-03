{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
     {t alias = "Сообщение пользователю регистрация"
          site = $url->getDomainStr()
          login = {$data->user.login|default:$data->user.e_mail|default:$data->user.phone}
          pass = $data->password
          user_link = $router->getUrl('users-front-profile', [], true)
     }

         <p>Здравствуйте!<br>
         Вы успешно зарегистрированы!<br>
         Мы рады приветствовать Вас на сайте rieltprof.ru. <br>
         Ваш логин: %login<br>
         Ваш пароль: %pass<br>
         С наилучшими пожеланиями,<br>
         rsrnavigator@gmail.com<br>
         Александр Ихсанов<br>
         <a href="tel:+79882466992">8(988)2466992</a><br>
         rieltprof.ru</p>{/t}
{/block}
