{extends file="%install%/wrap.tpl"}
{block name="content"}
<div class="congratulation">{t}Поздравляем! ReadyScript успешно установлен.{/t}</div>
<h3>{t}Ваши данные{/t}</h3>
<div class="authdata">
    <span class="key">E-mail ({t}Логин{/t}):</span> <span class="value">{$email}</span><br>
    <span class="key">{t}Пароль{/t}:</span> <span class="value password" style="display:none">{$password}</span><a class="show-password">{t}показать пароль{/t}</a>
</div>

<br class="clearboth">
<br>
<br>
<p>{t}Перейдите по ссылке, чтобы попасть в административную панель{/t} <a href="http://{$Setup.DOMAIN}{$Setup.FOLDER}/{$admin_section}/">http://{$Setup.DOMAIN}{$Setup.FOLDER}/{$admin_section}/</a></p>
<p>{t}Перейдите по ссылке, чтобы попасть в клиентскую часть сайта{/t} <a href="http://{$Setup.DOMAIN}{$Setup.FOLDER}/">http://{$Setup.DOMAIN}{$Setup.FOLDER}/</a></p>
<p>{t}Эти сведения также отправлены на{/t} {$email}</p>
<br>
<p>{t}Спасибо, что выбрали ReadyScript{/t}</p>
<p class="center-box">
    <a class="complete" href="http://{$Setup.DOMAIN}{$Setup.FOLDER}/">{t}Перейти на сайт{/t}</a>
</p>
{/block}