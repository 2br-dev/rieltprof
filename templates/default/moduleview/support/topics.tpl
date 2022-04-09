{if count($list)>0}
<table class="supportTopics">
    <thead>
        <tr>
            <td>{t}Обновлено{/t}</td>
            <td>{t}Тема{/t}</td>
            <td>{t}Сообщений{/t}</td>
            <td></td>
        </tr>
    </thead>
    <tbody>
        {foreach from=$list item=item}
        <tr data-id="{$item.id}">
            <td class="dateCol">
                <div class="time">{$item.updated|date_format:"%H:%M"}</div>
                <div class="date">{$item.updated|date_format:"%d.%m.%Y"}</div>
            </td>
            <td class="topic"><a href="{$router->getUrl('support-front-support', [Act=>"viewTopic", id => $item.id])}">{$item.title}</a></td>
            <td class="msgCount"><span class="text">{t}сообщений{/t}:</span> {$item.msgcount}{if $item.newcount>0} ({t}новых{/t}: {$item.newcount}){/if}</td>
            <td class="toolsCol">
                <a href="{$router->getUrl('support-front-support', ["Act" => "delTopic", "id" => $item.id])}" class="remove" title="{t}Удалить переписку по этой теме{/t}"></a>
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/if}

<form method="POST" class="supportForm">
    <i class="corner"></i>
    <ul class="adaptForm">
        <li class="error">
            {foreach from=$supp->getErrors() item=err}
            <p>{$err}</p>
            {/foreach}
        </li>    
        <li>
            <div class="text">
                <div class="caption">{t}Тема{/t}</div>
                <div class="field">
                    {if count($list)>0}
                        <select name="topic_id" id="topic_id">        
                            {foreach from=$list item=item}
                            <option value="{$item.id}" {if $item.id == $supp.topic_id}selected{/if}>{$item.title}</option>
                            {/foreach}
                            <option value="0" {if $supp.topic_id == 0}selected{/if}>{t}Новая тема...{/t}</option>
                        </select><br>
                    {/if}
                    <div id="newtopic" {if $supp.topic_id>0}style="display:none"{/if}>
                        <input type="text" name="topic" class="newtopic" value="{$supp.topic}">
                    </div>                    
                </div>
            </div>
        </li>
        <li>
            <div class="text">
                <div class="caption">{t}Вопрос{/t}</div>
                <div class="field">{$supp.__message->formView()}</div>
            </div>
        </li>
        <li>
            <div class="submit">
                <input type="submit" value="{t}Отправить{/t}">
            </div>
        </li>
    </ul>
</form>

<script>
    $(function() {
        $('#topic_id').change(function() {
            $('#newtopic').toggle( $(this).val() == 0 );
        });
        
        $('.supportTopics .remove').click(function(){
            if (!confirm('{t}Вы действительно хотите удалить переписку по теме?{/t}')) return false;
            var block = $(this).closest('[data-id]').css('opacity', 0.5);
            var topic_id = block.data('id');
            
            $.getJSON($(this).attr('href'), function(response) {
                if (response.success) {
                    location.reload();
                }
            });
            return false;
        });
    });
</script>