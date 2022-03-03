{addcss file="flatadmin/loader.css" basepath="common"}
{addcss file="%mobilesiteapp%/promo.css"}
{addjs file="%mobilesiteapp%/loading.js"}

<div id="rs-mobile">
    <div class="cssload-block" id="msa-loader" data-url="{adminUrl do="loadMsaData"}">

        <div class="cssload-maintext">
            <div class="cssload-container">
                <div class="cssload-loading"><i></i><i></i><i></i><i></i></div>
            </div>
            {t alias="Экран загрузки приложения - заголовок"}ReadyScript Mobile <sup>&reg;</sup>{/t}
        </div>

        <div class="cssload-subtext">{t}загрузка данных...{/t}</div>

    </div>

    <div class="rs-page-mobile"></div>
</div>