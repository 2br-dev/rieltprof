{addjs file="%modcontrol%/jquery.rs.searchoptions.js"}
{addcss file="%modcontrol%/searchoptions.css"}

<div class="search-options-block" data-search-url="{adminUrl do=searchOptionsByTerm}">
    <p class="hidden-xs">{t}Воспользуйтесь поиском, чтобы быстро перейти к настройке необходимой опции. Поиск найдет модуль и вкладку, где искомая опция располагается.{/t}</p>
    <form class="search-options bg-search">
        <input type="text" placeholder="{t}Начните набирать название опции{/t}" class="term w-100">
    </form>

    <h3>{t}Результат{/t}</h3>
    <div class="result-zone-wrapper">
        <div class="result-zone">
            {t}Здесь будет результат поиска{/t}
        </div>
    </div>
</div>