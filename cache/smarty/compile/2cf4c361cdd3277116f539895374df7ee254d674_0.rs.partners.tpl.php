<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:54:41
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\moduleview\rieltprof\block\partners.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf61c296d5_45085469',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2cf4c361cdd3277116f539895374df7ee254d674' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\moduleview\\rieltprof\\block\\partners.tpl',
      1 => 1614951942,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf61c296d5_45085469 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['partners']->value) {?>
    <div class="partners-block-wrapper">
        <div class="partners-block-title">
            <h3>Наши партеры</h3>
        </div>
        <div class="partners-wrapper">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['partners']->value, 'partner');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['partner']->value) {
?>
                <div class="partner-item">
                    <div class="partner-img">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['partner']->value['link'];?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['partner']->value['__image']->getLink();?>
"></a>
                    </div>
                    <div class="partner-title">
                        <?php echo $_smarty_tpl->tpl_vars['partner']->value['title'];?>

                    </div>
                </div>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

        </div>
    </div>
<?php }
}
}
