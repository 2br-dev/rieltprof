<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:55:05
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\moduleview\rieltprof\sidebar.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf790073a0_60853136',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2c2e46d3e86aa72d71d18c84fc54d410e6d702bb' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\moduleview\\rieltprof\\sidebar.tpl',
      1 => 1615486751,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%rieltprof%/rating_info.tpl' => 1,
  ),
),false)) {
function content_621faf790073a0_60853136 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="sidebar" id="profile-sidebar">
    <?php $_smarty_tpl->_assignInScope('user', \RS\Application\Auth::getCurrentUser());
?>
    <div class="profile-block">
        <div class="img">
            <img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/logo_dark_mode.svg" alt="">
        </div>
        <a href="/my/"><div class="avatar lazy-image" data-src="<?php echo $_smarty_tpl->tpl_vars['user']->value['__photo']->getUrl('320','320','xy');?>
"></div></a>
        <div class="text-data">
            <div class="name"><a href="/my/"><?php echo $_smarty_tpl->tpl_vars['user']->value['surname'];?>
 <?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
 <?php if (!empty($_smarty_tpl->tpl_vars['user']->value['midname'])) {
echo $_smarty_tpl->tpl_vars['user']->value['midname'];
}?></a></div>
            <div class="email"><?php echo $_smarty_tpl->tpl_vars['user']->value['e_mail'];?>
</div>
            <?php $_smarty_tpl->_subTemplateRender("rs:%rieltprof%/rating_info.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('user'=>$_smarty_tpl->tpl_vars['user']->value,'config'=>$_smarty_tpl->tpl_vars['rieltprof_config']->value), 0, false);
?>

        </div>
    </div>
    <div class="navigation-block">
        <a href="/my/" class="waves-effect waves-light">
            <span class="link-text">Мои объявления</span>
            <span class="link-chip"><?php echo $_smarty_tpl->tpl_vars['user']->value->getCountAds($_smarty_tpl->tpl_vars['user']->value['id']);?>
</span>
        </a>
        <div class="favorite-data" data-favorite-url="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('catalog-front-favorite');?>
">
            <?php $_smarty_tpl->_assignInScope('countFavorite', \Catalog\Model\FavoriteApi::getInstance()->getFavoriteCount());
?>
            <a href="/favorite/" class="waves-effect waves-light">
                <span class="link-text">Избранное</span>
                <span class="link-chip rs-favorite-items-count"><?php echo $_smarty_tpl->tpl_vars['countFavorite']->value;?>
</span>
            </a>
        </div>
        
        
        
        
        <a href="/my-review/" class="waves-effect waves-light">
            <span class="link-text">Отзывы</span>
            <span class="link-chip"><?php echo $_smarty_tpl->tpl_vars['user']->value->getCountReviews();?>
</span>
        </a>
    </div>
</div>
<?php }
}
