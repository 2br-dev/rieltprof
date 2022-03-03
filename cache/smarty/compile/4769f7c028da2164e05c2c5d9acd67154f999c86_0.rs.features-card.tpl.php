<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:55:06
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\moduleview\catalog\features-card.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf7a05f244_00483783',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4769f7c028da2164e05c2c5d9acd67154f999c86' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\moduleview\\catalog\\features-card.tpl',
      1 => 1620844817,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf7a05f244_00483783 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['product']->value['quickly']) {?>
    <div class="feature tooltipped urgent" title="Срочно" data-tooltip="Срочная продажа">С<span class="ext">рочно</span></div>
<?php }
if ($_smarty_tpl->tpl_vars['product']->value['exclusive']) {?>
    <div class="feature tooltipped exclusive" title="Эксклюзив" data-tooltip="Эксклюзив чистый">Э<span class="ext">ксклюзив</span></div>
<?php } else { ?>
    <?php if ($_smarty_tpl->tpl_vars['product']->value['advertise']) {?>
        <div class="feature tooltipped exclusive" title="Сам рекламирую" data-tooltip="Рекламиру в интернете">С<span class="ext">ам рекламирую</span></div>
    <?php }
}
if ($_smarty_tpl->tpl_vars['ad']->value['mortgage']) {?>
    <div class="feature tooltipped mortgage" title="Ипотека" data-tooltip="Ипотеку можно">И<span class="ext">потеку можно</span></div>
<?php } else { ?>
    <?php if ($_smarty_tpl->tpl_vars['ad']->value['only_cash']) {?>
        <div class="feature tooltipped mortgage" title="Наличные" data-tooltip="Наличные">Н<span class="ext">личные</span></div>
    <?php }
}
if ($_smarty_tpl->tpl_vars['product']->value['mark']) {?>
    <div class="feature tooltipped stowage" title="Закладка" data-tooltip="Закладку можно">З<span class="ext">акладку можно</span></div>
<?php }
if ($_smarty_tpl->tpl_vars['product']->value['breakdown']) {?>
    <div class="feature tooltipped breakdown" title="Разбивка" data-tooltip="Разбивка по сумме">Р<span class="ext">азбивка</span></div>
<?php }
if ($_smarty_tpl->tpl_vars['product']->value['encumbrance']) {?>
    <div class="feature tooltipped encumbrance" title="Обременение" data-tooltip="Обременение: <?php echo $_smarty_tpl->tpl_vars['product']->value['encumbrance_notice'];?>
">О<span class="ext">бременение</span></div>
<?php }
if ($_smarty_tpl->tpl_vars['product']->value['child']) {?>
    <div class="feature tooltipped child" title="Дети/Опека" data-tooltip="Дети/Опека">Д<span class="ext">ети/Опека</span></div>
<?php }
}
}
