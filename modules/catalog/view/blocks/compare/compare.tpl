{if $THEME_SETTINGS.enable_compare}
    {addjs file="%catalog%/rscomponent/compare.js"}
    <a class="{$param.custom_class|default:"head-icon-link"} rs-compare-block{if count($list)} active{/if} rs-do-compare"
       data-compare-url='{ "add":"{$router->getUrl('catalog-block-compare', ["cpmdo" => "ajaxAdd", "_block_id" => $_block_id])}", "remove":"{$router->getUrl('catalog-block-compare', ["cpmdo" => "ajaxRemove", "_block_id" => $_block_id])}", "compare":"{$router->getUrl('catalog-front-compare')}" }' >
        <span class="position-relative">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M23.25 19.9274H22.7688V3.32812C22.7688 2.91394 22.433 2.57812 22.0188 2.57812H17.4441C17.0299 2.57812 16.6941 2.91394 16.6941 3.32812V19.9274H15.0375V6.828C15.0375 6.41382 14.7017 6.078 14.2875 6.078H9.71283C9.29865 6.078 8.96283 6.41382 8.96283 6.828V19.9274H7.30627V10.3281C7.30627 9.91388 6.97046 9.57806 6.55627 9.57806H1.98157C1.56738 9.57806 1.23157 9.91388 1.23157 10.3281V19.9274H0.75C0.335815 19.9274 0 20.2632 0 20.6774C0 21.0916 0.335815 21.4274 0.75 21.4274H23.25C23.6642 21.4274 24 21.0916 24 20.6774C24 20.2632 23.6642 19.9274 23.25 19.9274ZM18.1939 4.07812H21.2686V19.9274H18.1939V4.07812ZM10.4628 19.9274V7.578H13.5375V19.9274H10.4628ZM2.73157 11.0781H5.80627V19.9274H2.73157V11.0781Z"/>
            </svg>
            <span class="label-count rs-compare-items-count">{count($list)}</span>
        </span>
        <div {if !$param.custom_class}class="mt-2"{/if}>{t}Сравнение{/t}</div>
    </a>
{/if}