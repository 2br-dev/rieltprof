{include file="head.tpl"}

<div class="viewport">
    <div class="progress-block" style="display:none">
        <p class="title">{t}Идет обновление...{/t}</p>
        <div class="progress-container">
            <div class="progress-bar" style="width:0%"></div>
            <div class="percent">0%</div>
        </div>
        <div class="module">{t}Подготовка к установке обновлений{/t}</div>
    </div>

{if count($data.updateData)}
        <h3>{t d=count($data.updateData)}Доступно %d [plural:%d:обновление|обновления|обновлений]{/t}</h3>
        <p>{t}Отметье модули, которые необходимо обновить{/t}</p>
    </div> <!-- viewport -->
    <form method="POST" class="update-item-form">
        {$table->getView()}
    </form>
{else}
    <div class="system-updated">
        <h2><i class="zmdi zmdi-check-all m-r-10"></i>{t}Cистема не нуждается в обновлении{/t}</h2>
        <p>{t}Все модули обновлены до последних версий{/t}</p>
    </div>
</div> <!-- viewport -->
{/if}