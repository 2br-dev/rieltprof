{if $user['id']<0}
    {t}Неавторизованный пользователь{/t}
{else}
    <table class="sub-table" border="1">
        <tr>
            <td>ID:</td>
            <td>{$user.id} (<a href="/{$Setup.ADMIN_SECTION}/musers_adm_ctrl/?do=edit&id={$user.id}" target="_blank">{t}перейти{/t}</a>)</td>
        </tr>    
        <tr>
            <td>{t}Логин{/t}:</td>
            <td>{$user.login}</td>
        </tr>
        <tr>
            <td>{t}Ф.И.О{/t}:</td>
            <td>{$user.surname} {$user.name} {$user.midname}</td>
        </tr>
        {if !empty($user.e_mail)}
        <tr>
            <td>E-mail:</td>
            <td>{$user.e_mail}</td>
        </tr>
        {/if}
        {if !empty($user.phone)}
        <tr>
            <td>{t}Телефон{/t}:</td>
            <td>{$user.phone}</td>
        </tr>
        {/if}
        {if !empty($user.company)}
        <tr>
            <td>{t}Компания{/t}:</td>
            <td>{$user.company}</td>
        </tr>        
        {/if}
    </table>
{/if}