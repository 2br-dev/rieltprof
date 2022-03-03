{strip}
{addjs file="jquery.min.js" basepath="common"}
{addjs file="jquery.ui/jquery-ui.min.js" basepath="common"}
{addjs file="dialog-options/jquery.dialogoptions.js" basepath="common"}

{addjs file="{$mod_js}install.js" basepath="root"}
{addcss file="{$mod_css}install.css" basepath="root"}
{addmeta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"}
{/strip}
<div class="install action-step{$step}" data-current-step="{$step}">
    <div class="topline">
        <div class="viewport">
            <a href="" class="brand"></a>
            <span class="wizard hidden-mobile">{t}Мастер установки{/t}</span>
            <span class="version"><span class="hidden-mobile">{t}Версия{/t}</span> {$Setup.VERSION}</span>
        </div> <!-- .viewport -->
    </div> <!-- .topline -->
    <div class="viewport">
        <table class="steps">
            <tr>
                {$step=intval($step)}
                <td width="188" class="step1{if $step==1} current{/if}{if $step==2} pre{/if}{if $step>1} ready{/if}"><span class="pos">1/5</span> <span class="text">{t}Лицензионное соглашение{/t}</span></td>
                <td width="188" class="step2{if $step==2} current{/if}{if $step==3} pre{/if}{if $step>2} ready{/if}"><span class="pos">2/5</span> <span class="text">{t}Проверка параметров сервера{/t}</span></td>
                <td width="188" class="step3{if $step==3} current{/if}{if $step==4} pre{/if}{if $step>3} ready{/if}"><span class="pos">3/5</span> <span class="text">{t}Конфигурирование системы{/t}</span></td>
                <td width="188" class="step4{if $step==4} current{/if}{if $step==5} pre{/if}{if $step>4} ready{/if}"><span class="pos">4/5</span> <span class="text">{t}Установка лицензии{/t}</span></td>
                <td class="step5 last{if $step==5} current{/if}"><span class="pos">5/5</span> <span class="text">{t}Завершение{/t}</span></td>
            </tr>
        </table>
    </div>
    
    <div class="viewport">
        <div class="workzone">
            <i class="corner ps{$step}"></i>
            {block name="content"}{/block}
        </div>
    </div>
</div>
{block name="root"}{/block}
<div class="footline">
    <div class="viewport">
        <span class="copy">Copyright &copy; 2012-{"NOW"|dateformat:"Y"} ReadyScript</span>
    </div>
</div>