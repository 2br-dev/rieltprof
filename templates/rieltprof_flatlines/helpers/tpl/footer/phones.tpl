{* Сведения о телефонах *}
<div class="column">
    <div class="column_title"><span>{t}ТЕЛЕФОНЫ{/t}</span></div>
    <div class="column_text">
        <div class="column_contact">
            {if $THEME_SETTINGS.phone_number1}
                <a href="tel:{$THEME_SETTINGS.phone_number1|replace:['-','(',')']:""}">{$THEME_SETTINGS.phone_number1}</a>
                <small>{$THEME_SETTINGS.phone_description1}</small>
            {/if}

            {if $THEME_SETTINGS.phone_number2}
                <a href="tel:{$THEME_SETTINGS.phone_number2|replace:['-','(',')']:""}">{$THEME_SETTINGS.phone_number2}</a>
                <small>{$THEME_SETTINGS.phone_description2}</small>
            {/if}
        </div>
    </div>
</div>