<h1>{t}Комментарий №{/t}{$data->comment->id} {t}от{/t} {$data->comment->dateof|dateformat:"@date @time"}</h1>

<p>{t}Тип комментария:{/t} {$data->comment->getTypeObject()->getTitle()}</p>
<p>{t}Объект комментирования:{/t} <a href="{$data->comment->getTypeObject()->getAdminUrl(true)}">{$data->comment->getTypeObject()->getLinkedObjectTitle()}</a></p>
<p>{t}Комментарий:{/t} {$data->comment->message}</p>
<p>{t}Автор:{/t} {$data->comment->user_name}</p>
<p>{t}Оценка:{/t} {$data->comment->rate}</p>