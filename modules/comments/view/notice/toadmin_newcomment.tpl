{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <h3>{t}Поступил новый комментарий на сайте{/t} {$url->getDomainStr()}!</h3>

    <p>{t}Тип комментария:{/t} {$data->comment->getTypeObject()->getTitle()}</p>
    <p>{t}Объект комментирования:{/t} <a href="{$data->comment->getTypeObject()->getAdminUrl(true)}">{$data->comment->getTypeObject()->getLinkedObjectTitle()}</a></p>
    <p>{t}Комментарий:{/t} {$data->comment->message}</p>
    <p>{t}Автор:{/t} {$data->comment->user_name}</p>
    <p>{t}Оценка:{/t} {$data->comment->rate}</p>

    <p><a href="{$router->getAdminUrl('edit', ["id" => $data->comment->id], 'comments-ctrl', true)}">{t}Перейти к просмотру{/t}</a></p>
{/block}