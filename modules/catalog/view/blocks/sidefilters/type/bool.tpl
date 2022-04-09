{* Шаблон для фильтра с типом - Да/Нет *}
{$is_open = $filters[$prop.id] || $prop.is_expanded}
<div class="accordion-item rs-type-radio">
    <div class="accordion-header">
        <button class="accordion-button {if !$is_open}collapsed{/if}" type="button" data-bs-toggle="collapse"
                data-bs-target="#accordionFilter-{$prop.id}">
            <span class="me-2">{$prop.title}</span>
        </button>
    </div>
    <div id="accordionFilter-{$prop.id}" class="accordion-collapse collapse {if $is_open}show{/if}">
        <div class="accordion-body">
            <ul class="filter-list">
                <li>
                    <label class="check">
                        <input type="radio" {if !isset($filters[$prop.id])}checked{/if} name="pf[{$prop.id}]" value="" data-start-value class="radio">
                        <span>{t}Неважно{/t}</span>
                    </label>
                </li>
                <li>
                    <label class="check">
                        <input type="radio" {if $filters[$prop.id] == '1'}checked{/if} name="pf[{$prop.id}]" value="1" class="radio">
                        <span>{t}Есть{/t}</span>
                    </label>
                </li>
                <li>
                    <label class="check">
                        <input type="radio" {if $filters[$prop.id] == '0'}checked{/if} name="pf[{$prop.id}]" value="0" class="radio">
                        <span>{t}Нет{/t}</span>
                    </label>
                </li>
            </ul>
        </div>
    </div>
</div>