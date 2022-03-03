<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:55:05
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\moduleview\catalog\fitures-table.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf79dd37b3_67973615',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '577e9bf86374a20279e317a11ff9d6abeb2e937e' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\moduleview\\catalog\\fitures-table.tpl',
      1 => 1620844688,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf79dd37b3_67973615 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="feature tooltipped <?php if ($_smarty_tpl->tpl_vars['product']->value['quickly']) {?>urgent<?php }?>" <?php if ($_smarty_tpl->tpl_vars['product']->value['quickly']) {?>title="Срочно" data-tooltip="Срочная продажа"<?php }?>>
    <?php if ($_smarty_tpl->tpl_vars['product']->value['quickly']) {?>
        С<span class="ext">рочно</span>
    <?php }?>
</div>
<div
    class="feature tooltipped <?php if ($_smarty_tpl->tpl_vars['product']->value['exclusive'] || $_smarty_tpl->tpl_vars['product']->value['advertise']) {?>exclusive<?php }?>"
    <?php if ($_smarty_tpl->tpl_vars['product']->value['exclusive'] || $_smarty_tpl->tpl_vars['product']->value['advertise']) {?>
        title="<?php if ($_smarty_tpl->tpl_vars['product']->value['exclusive']) {?>Эксклюзив<?php }
if ($_smarty_tpl->tpl_vars['product']->value['advertise']) {?>Рекламирую<?php }?>"
        data-tooltip="<?php if ($_smarty_tpl->tpl_vars['product']->value['exclusive']) {?>Эксклюзив чистый<?php }
if ($_smarty_tpl->tpl_vars['product']->value['advertise']) {?>Рекламирую в интернете<?php }?>"
    <?php }?>
>
    <?php if ($_smarty_tpl->tpl_vars['product']->value['exclusive'] || $_smarty_tpl->tpl_vars['product']->value['advertise']) {?>
        <?php if ($_smarty_tpl->tpl_vars['product']->value['exclusive']) {?>
            Э<span class="ext">ксклюзив</span>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['product']->value['advertise']) {?>
            С<span class="ext">ам рекламирую</span>
        <?php }?>
    <?php }?>
</div>
<div
        class="feature tooltipped <?php if ($_smarty_tpl->tpl_vars['product']->value['mortgage'] || $_smarty_tpl->tpl_vars['product']->value['only_cash']) {?>mortgage<?php }?>"
        <?php if ($_smarty_tpl->tpl_vars['product']->value['mortgage'] || $_smarty_tpl->tpl_vars['product']->value['only_cash']) {?>
            title="<?php if ($_smarty_tpl->tpl_vars['product']->value['mortgage']) {?>Ипотека<?php }
if ($_smarty_tpl->tpl_vars['product']->value['only_cash']) {?>Наличные<?php }?>"
            data-tooltip="<?php if ($_smarty_tpl->tpl_vars['product']->value['mortgage']) {?>Ипотеку можно<?php }
if ($_smarty_tpl->tpl_vars['product']->value['only_cash']) {?>Наличные<?php }?>"
        <?php }?>
>
    <?php if ($_smarty_tpl->tpl_vars['product']->value['mortgage']) {?>
        И<span class="ext">потеку можно</span>
    <?php } else { ?>
        <?php if ($_smarty_tpl->tpl_vars['product']->value['only_cash']) {?>
            Н<span class="ext">аличные</span>
        <?php }?>
    <?php }?>
</div>
<div class="feature tooltipped <?php if ($_smarty_tpl->tpl_vars['product']->value['mark']) {?>stowage<?php }?>" <?php if ($_smarty_tpl->tpl_vars['product']->value['mark']) {?>title="Закладка" data-tooltip="Закладку можно"<?php }?>>
    <?php if ($_smarty_tpl->tpl_vars['product']->value['mark']) {?>
        З<span class="ext">акладку можно</span>
    <?php }?>
</div>
<div class="feature tooltipped <?php if ($_smarty_tpl->tpl_vars['product']->value['breakdown']) {?>breakdown<?php }?>" <?php if ($_smarty_tpl->tpl_vars['product']->value['breakdown']) {?>title="Разбивка" data-tooltip="Разбивка по сумме"<?php }?>>
    <?php if ($_smarty_tpl->tpl_vars['product']->value['breakdown']) {?>
        Р<span class="ext">азбивка</span>
    <?php }?>
</div>
<div class="feature tooltipped <?php if ($_smarty_tpl->tpl_vars['product']->value['encumbrance']) {?>encumbrance<?php }?>" <?php if ($_smarty_tpl->tpl_vars['product']->value['encumbrance']) {?>title="Обременение" data-tooltip="Обременение: <?php echo $_smarty_tpl->tpl_vars['product']->value['encumbrance_notice'];?>
"<?php }?>>
    <?php if ($_smarty_tpl->tpl_vars['product']->value['encumbrance']) {?>
        О<span class="ext">бременение</span>
    <?php }?>
</div>
<div class="feature tooltipped <?php if ($_smarty_tpl->tpl_vars['product']->value['child']) {?>child<?php }?>" <?php if ($_smarty_tpl->tpl_vars['product']->value['child']) {?>title="Дети/Опека" data-tooltip="Дети/Опека"<?php }?>>
    <?php if ($_smarty_tpl->tpl_vars['product']->value['child']) {?>
        Д<span class="ext">ети/Опека</span>
    <?php }?>
</div>
<?php }
}
