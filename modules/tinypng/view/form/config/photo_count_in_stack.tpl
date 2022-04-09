{static_call var=right_version callback=['\TinyPNG\Model\Api', 'isPHPRightVersion']}
{if !$right_version}
    <span style="color: red;">{t}Внимание Ваша версия PHP ниже 5.5. Это минимально необходимая версия для работы данного модуля. Пожалуйста, обратитесь с хостинг.{/t}</span><br/><br/>
{/if}

{static_call var=count callback=['\TinyPNG\Model\Api', 'getCountInStack']}
{$count}