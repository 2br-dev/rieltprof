{foreach $filters as $key => $filter}
    <div>
        <b>{$key}</b>, <i>{$filter.type}</i> - {$filter.title|default:$orm_object["__{$key}"]->getDescription()}
        {if $filter.values}
            <br>{t}Возможные значения:{/t} <i>{implode(',', $filter.values)}</i>
        {/if}
    </div>
{/foreach}