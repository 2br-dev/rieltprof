{if !empty($html)} {* Содержимое блока *}
    {if !empty($js)}
        <script>
            {$js}
        </script>
    {/if}
    {$html}
{else}
    {if $current_user->isAdmin()}
        <div class="d-empty-block">{t}Перейдите в режим правки для создания блока{/t}</div>
    {/if}
{/if}