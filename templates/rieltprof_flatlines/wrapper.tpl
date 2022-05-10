<div class="bodyWrap">
    <div class="global-wrapper{if $query == ""} sidebar-shown{/if}" id="search">
        <div class="categories-sidebar collapsed">
            {include file="%catalog%/sidebar-catalog.tpl"}
        </div>
        {block name="content"}{/block}
        {include file='%rieltprof%/statusbar.tpl'}
    </div>
</div> <!-- .bodyWrap -->
