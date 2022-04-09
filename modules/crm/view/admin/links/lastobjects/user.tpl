<div class="link-last-objects m-t-30">
    <h3>{t}Недавно зарегистрированные пользователи{/t}</h3>
    <p>{t}Нажмите на одного из представленных ниже пользователей, чтобы установить с ним связь.{/t}</p>
    <ul class="list-unstyled" style="columns:2;">
        {foreach $last_objects as $item}
            <li data-id="{$item.id}" class="m-b-10">
                <a class="link-last-this">
                    <span class="link-last-title">{$item->getFio()} ({$item.id})</span></a>
                <div>
                    <small class="c-gray">
                        {if $item.e_mail}
                            Email: {$item.e_mail}
                        {elseif $item.phone}
                            {t}Телефон{/t}: {$item.phone}
                        {/if}
                    </small>
                </div>
            </li>
        {/foreach}
    </ul>
</div>