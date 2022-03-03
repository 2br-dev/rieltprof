{if !empty($html)} {* Содержимое блока *}
    {if !empty($js)}
        <script type="text/javascript">
            {$js}
        </script>
    {/if}
    {$html}
{else}
    <div class="d-empty-block">{t}Перейдите в режим правки для создания блока{/t}</div>
{/if}