{addcss file="%main%/systemcheck.css"}
<div class="system-tests">
    {foreach $tests as $test}
        {$test_result = $test->test()}
        <div class="item {if $test_result}success{else}fail{/if}">
            <div class="info">
                <p class="title">{$test->getTitle()}</p>
                <p class="description">{$test->getDescription()}</p>
                {if !$test_result}
                <div class="recommendation notice notice-warning">{$test->getRecommendation()}</div>
                {/if}
            </div>
            <div class="result">
                {if $test_result}
                    <i class="zmdi zmdi-check"></i>
                    <span class="hidden-xs">{t}Успешно{/t}</span>
                {else}
                    <i class="zmdi zmdi-close"></i>
                    <span class="hidden-xs">{t}Провалено{/t}</span>
                {/if}
            </div>
        </div>
    {/foreach}
</div>