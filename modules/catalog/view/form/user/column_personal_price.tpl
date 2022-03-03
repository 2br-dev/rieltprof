{$user = $cell->getRow()}
{$user_cost = unserialize($user.cost_id)}

<table>
    {foreach $cell->property.site_list as $site_id=>$site}
        <tr>
            <td>{$site}: </td> 
            <td>
                {if $user_cost[$site_id]}
                    {$cell->property.cost_list[$user_cost[$site_id]]}
                {else}
                    {t}- По умолчанию -{/t}
                {/if}
            </td>
        </tr>
    {/foreach}
</table>