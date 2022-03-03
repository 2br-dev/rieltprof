{if $partners}
    <div class="partners-block-wrapper">
        <div class="partners-block-title">
            <h3>Наши партеры</h3>
        </div>
        <div class="partners-wrapper">
            {foreach $partners as $partner}
                <div class="partner-item">
                    <div class="partner-img">
                        <a href="{$partner['link']}"><img src="{$partner.__image->getLink()}"></a>
                    </div>
                    <div class="partner-title">
                        {$partner['title']}
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
{/if}
