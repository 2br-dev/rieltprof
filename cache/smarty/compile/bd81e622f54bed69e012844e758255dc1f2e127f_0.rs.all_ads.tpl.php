<?php
/* Smarty version 3.1.30, created on 2022-03-02 20:55:05
  from "C:\OpenServer\domains\rieltprof.local\templates\rieltprof_flatlines\moduleview\rieltprof\block\all_ads.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_621faf7972cfd8_46604793',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bd81e622f54bed69e012844e758255dc1f2e127f' => 
    array (
      0 => 'C:\\OpenServer\\domains\\rieltprof.local\\templates\\rieltprof_flatlines\\moduleview\\rieltprof\\block\\all_ads.tpl',
      1 => 1620847801,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%catalog%/fitures-table.tpl' => 1,
    'rs:%catalog%/features-card.tpl' => 1,
  ),
),false)) {
function content_621faf7972cfd8_46604793 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('config', \RS\Config\Loader::byModule('rieltprof'));
$_smarty_tpl->_assignInScope('current_user', \RS\Application\Auth::getCurrentUser());
?>
<table class="table-view" data-mode="list">
    <thead>
    <tr>


        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th class="features"> </th>
        <th class="district">Район</th>
        <th class="type">Тип объекта</th>
        <th class="price">Стоимость</th>
        <th class="rooms">Комнат</th>
        <th class="square">Площадь</th>
        <th class="date">Дата</th>

        <th class="phone">Телефон</th>
    </tr>
    </thead>
    <tbody id="last-released-data">
        <?php if ($_smarty_tpl->tpl_vars['ads']->value) {?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['ads']->value, 'ad');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['ad']->value) {
?>
                <?php $_smarty_tpl->_assignInScope('properties', $_smarty_tpl->tpl_vars['ad']->value->fillProperty());
?>
                <tr
                    data-id="<?php echo $_smarty_tpl->tpl_vars['ad']->value['id'];?>
"
                    class="object-link"
                    data-user="<?php echo $_smarty_tpl->tpl_vars['ad']->value['owner'];?>
"
                    data-url="<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl('getOwnerPhone',array(),'rieltprof-tools');?>
"
                    data-product="<?php echo $_smarty_tpl->tpl_vars['ad']->value['id'];?>
"
                >
                    <td class=""><p class="expand-row"></p></td>
                    <td class="photo-holder">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['ad']->value->getUrl();?>
" class="photo lazy-image" data-src="<?php echo $_smarty_tpl->tpl_vars['ad']->value->getMainImage()->getUrl('550','330','xy');?>
"></a>
                    </td>
                    <td class="features">
                        <div class="features">
                            <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/fitures-table.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('product'=>$_smarty_tpl->tpl_vars['ad']->value), 0, true);
?>

                        </div>
                    </td>
                    <td class="district"><?php echo $_smarty_tpl->tpl_vars['ad']->value->getProductPropValue($_smarty_tpl->tpl_vars['config']->value['prop_district'],'district');?>
</td>
                    <td class="type"><?php echo $_smarty_tpl->tpl_vars['ad']->value['object'];?>
</td>
                    <td class="price">
                        <?php if ($_smarty_tpl->tpl_vars['ad']->value['cost_product']) {?>
                            <?php echo $_smarty_tpl->tpl_vars['config']->value->formatCost($_smarty_tpl->tpl_vars['ad']->value['cost_product'],' ');?>
 ₽
                        <?php } else { ?>
                            <?php echo $_smarty_tpl->tpl_vars['config']->value->formatCost($_smarty_tpl->tpl_vars['ad']->value['cost_rent'],' ');?>
 ₽/мес.
                        <?php }?>
                    </td>
                    <td class="rooms">
                        <?php if ($_smarty_tpl->tpl_vars['ad']->value['rooms']) {?>
                            <?php echo $_smarty_tpl->tpl_vars['ad']->value['rooms'];?>

                        <?php } else { ?>
                            <?php if ($_smarty_tpl->tpl_vars['ad']->value->getProductPropValue($_smarty_tpl->tpl_vars['config']->value['prop_rooms_list'],'rooms_list') !== NULL) {?>
                                <?php if ($_smarty_tpl->tpl_vars['ad']->value->getProductPropValue($_smarty_tpl->tpl_vars['config']->value['prop_rooms_list'],'rooms_list') == 'Студия') {?>
                                    Студия
                                <?php } else { ?>
                                    <?php echo $_smarty_tpl->tpl_vars['ad']->value->getProductPropValue($_smarty_tpl->tpl_vars['config']->value['prop_rooms_list'],'rooms_list');?>

                                <?php }?>
                            <?php } else { ?>
                                -
                            <?php }?>
                        <?php }?>
                    </td>
                    <td class="square">
                        <?php if ($_smarty_tpl->tpl_vars['ad']->value['object'] != "Участок") {?>
                            <?php if ($_smarty_tpl->tpl_vars['ad']->value['object'] == 'Дом' || $_smarty_tpl->tpl_vars['ad']->value['object'] == 'Дача') {?>
                                <?php echo $_smarty_tpl->tpl_vars['ad']->value['square'];?>
м²/<?php echo $_smarty_tpl->tpl_vars['ad']->value['land_area'];?>
сот.
                            <?php } else { ?>
                                <?php if ($_smarty_tpl->tpl_vars['ad']->value['object'] == 'Гараж' || $_smarty_tpl->tpl_vars['ad']->value['object'] == 'Комната' || $_smarty_tpl->tpl_vars['ad']->value['object'] == 'Коммерция') {?>
                                    <?php echo $_smarty_tpl->tpl_vars['ad']->value['square'];?>
м²
                                <?php } else { ?>
                                    <?php echo $_smarty_tpl->tpl_vars['ad']->value['square'];?>
м²
                                    <?php if ($_smarty_tpl->tpl_vars['ad']->value['square_living'] && $_smarty_tpl->tpl_vars['ad']->value['square_living'] != '0') {?>
                                        /<?php echo $_smarty_tpl->tpl_vars['ad']->value['square_living'];?>
м²
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['ad']->value['square_kitchen'] && $_smarty_tpl->tpl_vars['ad']->value['square_kitchen'] != '0') {?>
                                        /<?php echo $_smarty_tpl->tpl_vars['ad']->value['square_kitchen'];?>
м²
                                    <?php }?>
                                <?php }?>
                            <?php }?>
                        <?php } else { ?>
                            <?php echo $_smarty_tpl->tpl_vars['ad']->value['land_area'];?>
сот.
                        <?php }?>
                    </td>
                    <td class="date">
                        <?php echo $_smarty_tpl->tpl_vars['ad']->value->dateFormat('d.m.Y','dateof');?>

                    </td>







                    <td class="phone" id="phone-<?php echo $_smarty_tpl->tpl_vars['ad']->value['id'];?>
">
                        <a href="javascript:void(0);" class="phone">Телефон<span class="bubble"></span></a>
                        <a class="ticket-favorite rs-favorite <?php if ($_smarty_tpl->tpl_vars['ad']->value->inFavorite()) {?>rs-in-favorite<?php }?>" data-title="В избранное" data-already-title="В избранном"></a>
                    </td>
                </tr>
                <tr class="object-data">
                    <td colspan="8">
                        <div class="object-card">
                            <div class="left">
                                <div class="photo lazy-image" data-src="<?php echo $_smarty_tpl->tpl_vars['ad']->value->getMainImage()->getUrl('320','360','axy');?>
"></div>
                            </div>
                            <div class="right">
                                <div class="labels">
                                    <div class="features">
                                        <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/features-card.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('product'=>$_smarty_tpl->tpl_vars['ad']->value), 0, true);
?>


                                    </div>
                                </div>
                                <div class="location">
                                    <div class="area">
                                        <?php echo $_smarty_tpl->tpl_vars['ad']->value['city'];?>
,
                                        <?php if ($_smarty_tpl->tpl_vars['ad']->value['county'] != NULL) {?>
                                            <?php echo $_smarty_tpl->tpl_vars['ad']->value->getProductPropValue($_smarty_tpl->tpl_vars['config']->value['prop_county'],'county');?>
 округ,
                                        <?php }?>
                                        <?php echo $_smarty_tpl->tpl_vars['ad']->value->getProductPropValue($_smarty_tpl->tpl_vars['config']->value['prop_district'],'district');?>
</div>
                                    <div class="address">
                                        <?php if (!empty($_smarty_tpl->tpl_vars['ad']->value['street'])) {?>
                                            ул. <?php echo $_smarty_tpl->tpl_vars['ad']->value['street'];?>
,
                                        <?php }?>
                                        <?php ob_start();
echo $_smarty_tpl->tpl_vars['ad']->value['house'];
$_prefixVariable1=ob_get_clean();
if (!empty($_prefixVariable1)) {?>
                                            д. <?php echo $_smarty_tpl->tpl_vars['ad']->value['house'];?>
,
                                        <?php }?>
                                        <?php ob_start();
echo $_smarty_tpl->tpl_vars['ad']->value['liter'];
$_prefixVariable2=ob_get_clean();
if (!empty($_prefixVariable2)) {?>
                                            литер <?php echo $_smarty_tpl->tpl_vars['ad']->value['liter'];?>

                                        <?php }?>
                                     </div>
                                </div>
                                <div class="features">
                                    <?php if ($_smarty_tpl->tpl_vars['ad']->value['square']) {?>
                                        <div class="feature">
                                            <div class="value"><?php echo $_smarty_tpl->tpl_vars['ad']->value['square'];?>
м²</div>
                                            <div class="key">Общая</div>
                                        </div>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['ad']->value['square_living']) {?>
                                        <div class="feature">
                                            <div class="value"><?php echo $_smarty_tpl->tpl_vars['ad']->value['square_living'];?>
м²</div>
                                            <div class="key">Жилая</div>
                                        </div>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['ad']->value['square_kitchen']) {?>
                                        <div class="feature">
                                            <div class="value"><?php echo $_smarty_tpl->tpl_vars['ad']->value['square_kitchen'];?>
м²</div>
                                            <div class="key">Кухня</div>
                                        </div>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['ad']->value['rooms']) {?>
                                        <div class="feature">
                                            <div class="value"><?php echo $_smarty_tpl->tpl_vars['ad']->value['rooms'];?>
</div>
                                            <div class="key">Комнат</div>
                                        </div>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['ad']->value['flat']) {?>
                                        <div class="feature">
                                            <div class="value"><?php echo $_smarty_tpl->tpl_vars['ad']->value['flat'];?>
</div>
                                            <div class="key">Этаж</div>
                                        </div>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['ad']->value['flat_house']) {?>
                                        <div class="feature">
                                            <div class="value"><?php echo $_smarty_tpl->tpl_vars['ad']->value['flat_house'];?>
</div>
                                            <div class="key">Этажность</div>
                                        </div>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['ad']->value['land_area']) {?>
                                        <div class="feature">
                                            <div class="value"><?php echo $_smarty_tpl->tpl_vars['ad']->value['land_area'];?>
сот.</div>
                                            <div class="key">Участок</div>
                                        </div>
                                    <?php }?>
                                </div>
                                <div class="obj-footer">
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['ad']->value->getUrl();?>
">Подробнее</a>
                                    <div class="author">
                                        <span>Автор: </span><a
                                                <?php if ($_smarty_tpl->tpl_vars['current_user']->value['id'] != $_smarty_tpl->tpl_vars['ad']->value['owner']) {?>href="/owner-profile/<?php echo $_smarty_tpl->tpl_vars['ad']->value->getOwner()->id;?>
/" <?php } else { ?>href="/my/"<?php }?>
                                        >
                                            <?php echo $_smarty_tpl->tpl_vars['ad']->value->getOwner()->surname;?>
 <?php echo $_smarty_tpl->tpl_vars['ad']->value->getOwner()->name;?>

                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

        <?php }?>
    </tbody>
</table>

<?php }
}
