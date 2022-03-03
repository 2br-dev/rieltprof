<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:55:09
  from "C:\OpenServer\domains\rieltprof.local\templates\system\debug\top.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf7dc32264_57275222',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6969b361505d82156c0c70b5df6c9b53b36c98ec' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\system\\debug\\top.tpl',
      1 => 1620406462,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_621faf7dc32264_57275222 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_function_addjs')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.addjs.php';
if (!is_callable('smarty_function_addcss')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.addcss.php';
if (!is_callable('smarty_block_t')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\block.t.php';
if (!is_callable('smarty_function_moduleinsert')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.moduleinsert.php';
if (!is_callable('smarty_function_adminUrl')) require_once 'C:\\OpenServer\\domains\\rieltprof.local\\core\\smarty\\rsplugins\\function.adminUrl.php';
echo smarty_function_addjs(array('file'=>"jquery.rs.admindebug.js",'basepath'=>"common",'unshift'=>true),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.ui/jquery-ui.min.js",'basepath'=>"common",'unshift'=>true),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.min.js",'name'=>"jquery",'basepath'=>"common",'header'=>true,'unshift'=>true),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"dialog-options/jquery.dialogoptions.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"bootstrap/bootstrap.min.js",'name'=>"bootstrap",'basepath'=>"common"),$_smarty_tpl);?>


<?php echo smarty_function_addjs(array('file'=>"lab/lab.min.js",'basepath'=>"common"),$_smarty_tpl);?>


<?php echo smarty_function_addjs(array('file'=>"jquery.datetimeaddon/jquery.datetimeaddon.min.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.rs.debug.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.rs.ormobject.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.cookie/jquery.cookie.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.form/jquery.form.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jstour/jquery.tour.engine.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jstour/jquery.tour.js",'basepath'=>"common"),$_smarty_tpl);?>


<?php echo smarty_function_addcss(array('file'=>"flatadmin/iconic-font/css/material-design-iconic-font.min.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"flatadmin/readyscript.ui/jquery-ui.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"flatadmin/app.css?v=2",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"common/animate.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"common/tour.css",'basepath'=>"common"),$_smarty_tpl);?>


<?php if ($_smarty_tpl->tpl_vars['this_controller']->value->getDebugGroup()) {?>
    <?php echo smarty_function_addcss(array('file'=>"flatadmin/debug.css",'basepath'=>"common"),$_smarty_tpl);?>

    <?php echo smarty_function_addcss(array('file'=>"%templates%/moduleblocks.css"),$_smarty_tpl);?>

<?php }
$_smarty_tpl->_assignInScope('has_debug_right', $_smarty_tpl->tpl_vars['current_user']->value->checkModuleRight('main',Main\Config\ModuleRights::RIGHT_DEBUG_MODE));
?>
<div id="debug-top-block" class="admin-style">
    <header id="header">
        <ul class="header-inner">
            <li class="rs-logo debug">
                <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getRootUrl();?>
"></a>
            </li>

            <li class="header-panel">
                <div class="viewport">
                    <div class="fixed-tools">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('main.admin');?>
" class="to-admin">
                            <i class="rs-icon rs-icon-admin"></i><br>
                            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
управление<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                        </a>

                        <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('main.admin',array("Act"=>"cleanCache"));?>
" class="rs-clean-cache">
                            <i class="rs-icon rs-icon-refresh"></i><br>
                            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
кэш<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                        </a>

                        <?php if ($_smarty_tpl->tpl_vars['has_debug_right']->value) {?>
                            <div class="debug-mode-switcher">
                                <div data-url="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('main.admin',array("Act"=>'ajaxToggleDebug'));?>
" class="toggle-switch rs-switch <?php if ($_smarty_tpl->tpl_vars['this_controller']->value->getDebugGroup()) {?>on<?php }?>">
                                    <label class="ts-helper"></label>
                                </div>
                                <div class="debugmode-text"><span class="hidden-xs"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
режим отладки<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span><span class="visible-xs"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
отладка<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span></div>
                            </div>
                        <?php }?>

                        <?php if ($_smarty_tpl->tpl_vars['this_controller']->value->getDebugGroup()) {?> 
                            <div class="rs-toggle-debug-modes toggle-debug-modes bt dropdown">
                                <i class="rs-icon rs-icon-debug-<?php echo $_smarty_tpl->tpl_vars['this_controller']->value->app->getDebugMode();?>
"></i><br>
                                <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
править<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
...</span>

                                <ul class="fxt-dropdown-menu">
                                    <li class="fxt-hover-node">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('main.admin',array("Act"=>"ajaxToggleDebugMode","mode"=>"blocks"));?>
" data-body-class="blocks">
                                            <i class="rs-icon rs-icon-modes rs-icon-mode-blocks"></i>
                                            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Блоки<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                                        </a>
                                    </li>
                                    <li class="fxt-hover-node">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('main.admin',array("Act"=>"ajaxToggleDebugMode","mode"=>"sectionsandrows"));?>
" data-body-class="sectionsandrows">
                                            <i class="rs-icon rs-icon-modes rs-icon-mode-sectionsandrows"></i>
                                            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Секции и строки<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                                        </a>
                                    </li>
                                    <li class="fxt-hover-node">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('main.admin',array("Act"=>"ajaxToggleDebugMode","mode"=>"containers"));?>
" data-body-class="containers">
                                            <i class="rs-icon rs-icon-modes rs-icon-mode-containers"></i>
                                            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Контейнеры<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php }?>
                    </div>

                    <div class="float-tools">
                        <div class="dropdown">
                            <a class="toggle visible-xs-inline-block" data-toggle="dropdown" id="floatTools" aria-haspopup="true"><i class="zmdi zmdi-more-vert"></i></a>

                            <ul class="ft-dropdown-menu" aria-labelledby="floatTools">
                                <?php echo smarty_function_moduleinsert(array('name'=>"\Main\Controller\Admin\Block\HeaderPanel",'public'=>true,'indexTemplate'=>"%main%/adminblocks/headerpanel/header_public_panel_items.tpl"),$_smarty_tpl,'C:\OpenServer\domains\rieltprof.local\templates\system\debug\top.tpl');?>

                                <?php if ($_smarty_tpl->tpl_vars['has_debug_right']->value && !$_smarty_tpl->tpl_vars['Smarty']->value['const']['CLOUD_UNIQ'] && $_smarty_tpl->tpl_vars['timing']->value->isEnable()) {?>
                                    <li>
                                        <a class="rs-open-performance-report" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Отчет<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
" href="<?php echo $_smarty_tpl->tpl_vars['timing']->value->getReportUrl();?>
" target="_blank">
                                            <i class="rs-icon rs-icon-performance"></i>
                                            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Отчет<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                                        </a>
                                    </li>
                                <?php }?>
                                <li>
                                    <a class="hidden-xs action start-tour" data-tour-id="welcome" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Обучение<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
">
                                        <i class="rs-icon rs-icon-tour"></i>
                                        <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Обучение<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                                    </a>
                                </li>
                                <li class="ft-hover-node">
                                    <a href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"users-ctrl",'do'=>"edit",'id'=>$_smarty_tpl->tpl_vars['current_user']->value['id']),$_smarty_tpl);?>
">
                                        <i class="rs-icon rs-icon-user"></i>
                                        <span><?php echo $_smarty_tpl->tpl_vars['current_user']->value->getFio();?>
</span>
                                    </a>

                                    <ul class="ft-sub">
                                        <li>
                                            <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('main.admin',array('Act'=>'logout'));?>
">
                                                <i class="rs-icon zmdi zmdi-power"></i>
                                                <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat1=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat1);
while ($_block_repeat1) {
ob_start();
?>
Выход<?php $_block_repeat1=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat1);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
</span>
                                            </a>
                                        </li>
                                    </ul>

                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </header>
</div>
<?php echo $_smarty_tpl->tpl_vars['result_html']->value;
}
}
