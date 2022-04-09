<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Config;

use ReflectionClass;
use ReflectionMethod;
use RS\Config\Migration\ConvertTablesToAllowEmoji;
use RS\Config\Migration\OldTemplatePatcher;
use RS\Db\Adapter as DbAdapter;
use RS\HashStore\Api as HashStoreApi;

/**
 * Класс содержит патчи, которые будут выполняться после
 * установки обновлений для всех модулей. Данный класс нужно использовать
 * для выполнения долгих операций, трансформации файлов и данных после обновления.
 */
class Migration
{
    protected $update_data = [];
    protected $timeout = 20;
    protected $cloud_mode = false; //Если false, Значит миграция устанавливается коробочной версии

    /**
     * Включает режим установки миграции в облаке ReadyScript
     * @param $bool
     */
    function setCloudMode($bool = true)
    {
        $this->cloud_mode = $bool;
    }

    /**
     * Выполняет запуск всех необходимых патчей.
     * Каждый патч представлен в методе patch*, патч выполняется один раз,
     * после чего информация об этом заносится в БД
     *
     * @param array $update_data Массив со списком модулей, которые обновляются и их версий
     * @param $previous_state
     * @return mixed
     * true - если все патчи выполнились успешно
     * array - массив со state, если необходим повторный запуск
     * string - если произошла ошибка во время выполнения одного из патчей
     */
    public function run(array $update_data, $previous_state = [])
    {
        $this->update_data = $update_data;

        try {
            $reflection = new ReflectionClass($this);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PROTECTED);

            $start_time = microtime(true);

            foreach ($methods as $n => $method) {

                if (isset($previous_state['method'])
                    && $n < $previous_state['method']) {
                    continue;
                }

                $method_name = $method->getName();
                $patch_id = "MIGRATION_".$method_name;
                if (preg_match('/^patch/', $method_name) && !HashStoreApi::get($patch_id)) {

                    $state = (isset($previous_state['method']) && $previous_state['method'] == $n) ? $previous_state : [];
                    $timeout = $this->timeout - (microtime(true) - $start_time); //Остаток времени
                    $result = $this->$method_name($timeout, $state);

                    if (is_array($result)) {
                        //Нужен повторный запуск
                        return $result + [
                            'method' => $n
                        ];

                    } elseif (is_string($result)) {
                        return $result; //Ошибка
                    }
                }

                HashStoreApi::set($patch_id, true);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Устанавливает timeout, после патч прервется и будет необходим следующий шаг запуска
     *
     * @param $sec
     */
    public function setTimeout($sec)
    {
        $this->timeout = $sec;
    }

    /**
     * Удаляет старые файлы, которые более неактуальны в ReadyScript 6.
     * Файлы были перенесены в подпапку /admin/*
     *
     * @param integer $timeout
     * @param array $previous_state
     * @return bool
     */
    protected function patchMovingFilesToAdmin($timeout, $previous_state)
    {
        //Выполняем патч только если идет обновление с версии, младше 6.0
        if ($this->cloud_mode ||
            (isset($this->update_data['@core'])
                && version_compare($this->update_data['@core']['my_version'], '6.0.0') < 0))
        {
            $renamed = array(
                'modules/alerts/view/notice_list.tpl' => 'modules/alerts/view/admin/notice_list.tpl',
                'modules/alerts/view/test_sms.tpl' => 'modules/alerts/view/admin/test_sms.tpl',
                'modules/antivirus/view/event_col_details.tpl' => 'modules/antivirus/view/admin/event_col_details.tpl',
                'modules/antivirus/view/show_changed_files.tpl' => 'modules/antivirus/view/admin/show_changed_files.tpl',
                'modules/antivirus/view/show_diff.tpl' => 'modules/antivirus/view/admin/show_diff.tpl',
                'modules/atolonline/view/cms_settings_form.tpl' => 'modules/atolonline/view/admin/cms_settings_form.tpl',
                'modules/atolonline/view/cms_settings_form2.tpl' => 'modules/atolonline/view/admin/cms_settings_form2.tpl',
                'modules/atolonline/view/load_settings.tpl' => 'modules/atolonline/view/admin/load_settings.tpl',
                'modules/cartrules/view/rule_as_text.tpl' => 'modules/cartrules/view/admin/rule_as_text.tpl',
                'modules/catalog/view/tree_item_cell.tpl' => 'modules/catalog/view/admin/tree_item_cell.tpl',
                'modules/catalog/view/meproperty_form.tpl' => 'modules/catalog/view/form/product/meproperty_form.tpl',
                'modules/catalog/view/multioffers_form.tpl' => 'modules/catalog/view/form/product/multioffers_form.tpl',
                'modules/catalog/view/property_form.tpl' => 'modules/catalog/view/form/product/property_form.tpl',
                'modules/catalog/view/property_full_list.tpl' => 'modules/catalog/view/form/product/property_full_list.tpl',
                'modules/catalog/view/property_group_product.tpl' => 'modules/catalog/view/form/product/property_group_product.tpl',
                'modules/catalog/view/property_product.tpl' => 'modules/catalog/view/form/product/property_product.tpl',
                'modules/catalog/view/property_val.tpl' => 'modules/catalog/view/form/product/property_val.tpl',
                'modules/catalog/view/property_val_big_list.tpl' => 'modules/catalog/view/form/product/property_val_big_list.tpl',
                'modules/catalog/view/property_val_big_list_items.tpl' => 'modules/catalog/view/form/product/property_val_big_list_items.tpl',
                'modules/cdn/view/registration_form_info.tpl' => 'modules/cdn/view/admin/registration_form_info.tpl',
                'modules/cdn/view/registration_sent.tpl' => 'modules/cdn/view/admin/registration_sent.tpl',
                'modules/colors/view/color_column.tpl' => 'modules/colors/view/admin/color_column.tpl',
                'modules/exchange/view/exchange.tpl' => 'modules/exchange/view/admin/exchange.tpl',
                'modules/exchange/view/config_translit_checkbox.tpl' => 'modules/exchange/view/form/config/config_translit_checkbox.tpl',
                'modules/export/view/action_cell.tpl' => 'modules/export/view/admin/action_cell.tpl',
                'modules/export/view/exchangable_alert.tpl' => 'modules/export/view/admin/exchangable_alert.tpl',
                'modules/export/view/show_log.tpl' => 'modules/export/view/admin/show_log.tpl',
                'modules/export/view/url_cell.tpl' => 'modules/export/view/admin/url_cell.tpl',
                'modules/files/view/filesblock.tpl' => 'modules/files/view/adminblocks/files/filesblock.tpl',
                'modules/files/view/form_maker.tpl' => 'modules/files/view/adminblocks/files/form_maker.tpl',
                'modules/files/view/one_file.tpl' => 'modules/files/view/adminblocks/files/one_file.tpl',
                'modules/mailsender/view/col_status.tpl' => 'modules/mailsender/view/admin/col_status.tpl',
                'modules/mailsender/view/cron_check.tpl' => 'modules/mailsender/view/admin/cron_check.tpl',
                'modules/mailsender/view/form_generator.tpl' => 'modules/mailsender/view/admin/form_generator.tpl',
                'modules/mailsender/view/samples.tpl' => 'modules/mailsender/view/admin/samples.tpl',
                'modules/mailsender/view/dialog_field.tpl' => 'modules/mailsender/view/content/products/dialog_field.tpl',
                'modules/main/view/crud-options.tpl' => 'modules/main/view/admin/crud-options.tpl',
                'modules/main/view/license_col_number.tpl' => 'modules/main/view/admin/license_col_number.tpl',
                'modules/main/view/license_col_object.tpl' => 'modules/main/view/admin/license_col_object.tpl',
                'modules/main/view/license_notice.tpl' => 'modules/main/view/admin/license_notice.tpl',
                'modules/main/view/routes.tpl' => 'modules/main/view/admin/routes.tpl',
                'modules/main/view/show_changelog.tpl' => 'modules/main/view/admin/show_changelog.tpl',
                'modules/main/view/show_event_listeners.tpl' => 'modules/main/view/admin/show_event_listeners.tpl',
                'modules/main/view/systemcheck.tpl' => 'modules/main/view/admin/systemcheck.tpl',
                'modules/main/view/widget_list.tpl' => 'modules/main/view/admin/widget/widget_list.tpl',
                'modules/main/view/widget_wrapper.tpl' => 'modules/main/view/admin/widget/widget_wrapper.tpl',
                'modules/main/view/widgets.tpl' => 'modules/main/view/admin/widget/widgets.tpl',
                'modules/menu/view/adminmenu.tpl' => 'modules/menu/view/admin/adminmenu.tpl',
                'modules/menu/view/adminmenu_branch.tpl' => 'modules/menu/view/admin/adminmenu_branch.tpl',
                'modules/menu/view/tree_column.tpl' => 'modules/menu/view/admin/tree_column.tpl',
                'modules/migration/view/migrate.tpl' => 'modules/migration/view/admin/migrate.tpl',
                'modules/mobilesiteapp/view/loading.tpl' => 'modules/mobilesiteapp/view/admin/loading.tpl',
                'modules/mobilesiteapp/view/phonepreview.tpl' => 'modules/mobilesiteapp/view/admin/phonepreview.tpl',
                'modules/mobilesiteapp/view/promo.tpl' => 'modules/mobilesiteapp/view/admin/promo.tpl',
                'modules/mobilesiteapp/view/view_app.tpl' => 'modules/mobilesiteapp/view/admin/view_app.tpl',
                'modules/modcontrol/view/add.tpl' => 'modules/modcontrol/view/admin/add.tpl',
                'modules/modcontrol/view/add_ok.tpl' => 'modules/modcontrol/view/admin/add_ok.tpl',
                'modules/modcontrol/view/add_step2.tpl' => 'modules/modcontrol/view/admin/add_step2.tpl',
                'modules/modcontrol/view/col_description.tpl' => 'modules/modcontrol/view/admin/col_description.tpl',
                'modules/modcontrol/view/col_enabled.tpl' => 'modules/modcontrol/view/admin/col_enabled.tpl',
                'modules/modcontrol/view/crud_module.tpl' => 'modules/modcontrol/view/admin/crud_module.tpl',
                'modules/modcontrol/view/filter_by_options.tpl' => 'modules/modcontrol/view/admin/filter_by_options.tpl',
                'modules/modcontrol/view/module_list.tpl' => 'modules/modcontrol/view/admin/module_list.tpl',
                'modules/modcontrol/view/search_options.tpl' => 'modules/modcontrol/view/admin/search_options.tpl',
                'modules/modcontrol/view/search_options_result.tpl' => 'modules/modcontrol/view/admin/search_options_result.tpl',
                'modules/modcontrol/view/show_changelog.tpl' => 'modules/modcontrol/view/admin/show_changelog.tpl',
                'modules/ordersonmap/view/widget_sale_geography.tpl' => 'modules/ordersonmap/view/admin/widget_sale_geography.tpl',
                'modules/ormeditor/view/custom_property.tpl' => 'modules/ormeditor/view/admin/custom_property.tpl',
                'modules/ormeditor/view/field_list.tpl' => 'modules/ormeditor/view/admin/field_list.tpl',
                'modules/ormeditor/view/field_tab.tpl' => 'modules/ormeditor/view/admin/field_tab.tpl',
                'modules/ormeditor/view/fieldform.tpl' => 'modules/ormeditor/view/admin/fieldform.tpl',
                'modules/ormeditor/view/native_property.tpl' => 'modules/ormeditor/view/admin/native_property.tpl',
                'modules/pageseo/view/pageseo_column_meta.tpl' => 'modules/pageseo/view/admin/pageseo_column_meta.tpl',
                'modules/main/view/pageseo_column_route.tpl' => 'modules/pageseo/view/admin/pageseo_column_route.tpl',
                'modules/partnership/view/top_help.tpl' => 'modules/partnership/view/admin/top_help.tpl',
                'modules/photo/view/form.tpl' => 'modules/photo/view/admin/form.tpl',
                'modules/photo/view/form_onepic.tpl' => 'modules/photo/view/admin/form_onepic.tpl',
                'modules/pushsender/view/user_push_lock.tpl' => 'modules/pushsender/view/admin/user_push_lock.tpl',
                'modules/retailcrm/view/config_cron_check.tpl' => 'modules/retailcrm/view/admin/config_cron_check.tpl',
                'modules/shop/view/cdek_rebase.tpl' => 'modules/shop/view/admin/cdek_rebase.tpl',
                'modules/shop/view/orderview.tpl' => 'modules/shop/view/admin/orderview.tpl',
                'modules/shop/view/order_depend_maker.tpl' => 'modules/shop/view/admin/order_depend_maker.tpl',
                'modules/shop/view/order_footer_maker.tpl' => 'modules/shop/view/admin/order_footer_maker.tpl',
                'modules/shop/view/order_info_maker.tpl' => 'modules/shop/view/admin/order_info_maker.tpl',
                'modules/shop/view/order_status_cell.tpl' => 'modules/shop/view/admin/order_status_cell.tpl',
                'modules/shop/view/order_totalcost_cell.tpl' => 'modules/shop/view/admin/order_totalcost_cell.tpl',
                'modules/shop/view/order_tree_cell.tpl' => 'modules/shop/view/admin/order_tree_cell.tpl',
                'modules/shop/view/order_user_cell.tpl' => 'modules/shop/view/admin/order_user_cell.tpl',
                'modules/shop/view/orders_report.tpl' => 'modules/shop/view/admin/orders_report.tpl',
                'modules/shop/view/quick_show_orders.tpl' => 'modules/shop/view/admin/quick_show_orders.tpl',
                'modules/shop/view/receipt_actions_cell.tpl' => 'modules/shop/view/admin/receipt_actions_cell.tpl',
                'modules/shop/view/receipt_error.tpl' => 'modules/shop/view/admin/receipt_error.tpl',
                'modules/shop/view/receipt_info.tpl' => 'modules/shop/view/admin/receipt_info.tpl',
                'modules/shop/view/receipt_status_cell.tpl' => 'modules/shop/view/admin/receipt_status_cell.tpl',
                'modules/shop/view/reservation_cron_check.tpl' => 'modules/shop/view/admin/reservation_cron_check.tpl',
                'modules/shop/view/transaction_actions_cell.tpl' => 'modules/shop/view/admin/transaction_actions_cell.tpl',
                'modules/shop/view/transaction_cost_cell.tpl' => 'modules/shop/view/admin/transaction_cost_cell.tpl',
                'modules/shop/view/transaction_receipt.tpl' => 'modules/shop/view/admin/transaction_receipt.tpl',
                'modules/site/view/site_limit.tpl' => 'modules/site/view/admin/site_limit.tpl',
                'modules/site/view/top_help.tpl' => 'modules/site/view/admin/top_help.tpl',
                'modules/sitemap/view/sitemap_url.tpl' => 'modules/sitemap/view/admin/sitemap_url.tpl',
                'modules/siteupdate/view/changelog_col.tpl' => 'modules/siteupdate/view/admin/changelog_col.tpl',
                'modules/siteupdate/view/checkupdate.tpl' => 'modules/siteupdate/view/admin/checkupdate.tpl',
                'modules/siteupdate/view/head.tpl' => 'modules/siteupdate/view/admin/head.tpl',
                'modules/siteupdate/view/module_col.tpl' => 'modules/siteupdate/view/admin/module_col.tpl',
                'modules/siteupdate/view/post_redirector.tpl' => 'modules/siteupdate/view/admin/post_redirector.tpl',
                'modules/siteupdate/view/selectproduct.tpl' => 'modules/siteupdate/view/admin/selectproduct.tpl',
                'modules/siteupdate/view/update.tpl' => 'modules/siteupdate/view/admin/update.tpl',
                'modules/siteupdate/view/view_changelog.tpl' => 'modules/siteupdate/view/admin/view_changelog.tpl',
                'modules/statistic/view/dashboard.tpl' => 'modules/statistic/view/admin/dashboard.tpl',
                'modules/support/view/adminview.tpl' => 'modules/support/view/admin/adminview.tpl',
                'modules/support/view/table_user_cell.tpl' => 'modules/support/view/admin/table_user_cell.tpl',
                'modules/support/view/user_type_cell.tpl' => 'modules/support/view/admin/user_type_cell.tpl',
                'modules/tags/view/form.tpl' => 'modules/tags/view/admin/form.tpl',
                'modules/tags/view/tab_tags.tpl' => 'modules/tags/view/admin/tab_tags.tpl',
                'modules/tags/view/words.tpl' => 'modules/tags/view/admin/words.tpl',
                'modules/templates/view/block_manager.tpl' => 'modules/templates/view/admin/block_manager.tpl',
                'modules/templates/view/block_manager_add_module_form.tpl' => 'modules/templates/view/admin/block_manager_add_module_form.tpl',
                'modules/templates/view/copy_container.tpl' => 'modules/templates/view/admin/copy_container.tpl',
                'modules/templates/view/crud-block-form.tpl' => 'modules/templates/view/admin/crud-block-form.tpl',
                'modules/templates/view/file_manager.tpl' => 'modules/templates/view/admin/file_manager.tpl',
                'modules/templates/view/hook_sort.tpl' => 'modules/templates/view/admin/hook_sort.tpl',
                'modules/templates/view/select_template.tpl' => 'modules/templates/view/admin/select_template.tpl',
                'modules/templates/view/select_theme.tpl' => 'modules/templates/view/admin/select_theme.tpl',
                'modules/templates/view/select_theme_list.tpl' => 'modules/templates/view/admin/select_theme_list.tpl',
                'modules/templates/view/select_theme_mp_list.tpl' => 'modules/templates/view/admin/select_theme_mp_list.tpl',
                'modules/yandexmarketcpa/view/campaign.tpl' => 'modules/yandexmarketcpa/view/admin/campaign.tpl',
                'modules/yandexmarketcpa/view/config_auth_token.tpl' => 'modules/yandexmarketcpa/view/admin/config_auth_token.tpl',
                'modules/yandexmarketcpa/view/requesttest.tpl' => 'modules/yandexmarketcpa/view/admin/requesttest.tpl',
                'modules/yandexmarketcpa/view/secret_part_url.tpl' => 'modules/yandexmarketcpa/view/admin/secret_part_url.tpl',
                'modules/yandexmarketcpa/view/status.tpl' => 'modules/yandexmarketcpa/view/admin/status.tpl',
                'modules/yandexmarketcpa/view/substatus.tpl' => 'modules/yandexmarketcpa/view/admin/substatus.tpl',
            );

            foreach($renamed as $old_filename => $new_filename) {
                if (file_exists(\Setup::$PATH.'/'.$new_filename)
                    && file_exists(\Setup::$PATH.'/'.$old_filename)) {

                    @unlink(\Setup::$PATH.'/'.$old_filename);
                }
            }
        }

        return true;
    }

    /**
     * Распаковывает в старые темы оформления патч-архив,
     * который содержит все шаблоны, скрипты, css от темы default внутри себя.
     *
     * @param integer $timeout
     * @param array $previous_state
     * @return mixed
     * true - если все патчи выполнились успешно
     * array - массив со state, если необходим повторный запуск
     * @throws \RS\Exception
     */
    protected function patchOldTemplatesToRs6018($timeout, $previous_state)
    {
        //Выполняем патч только если идет обновление с версии, младше 6.0
        if ($this->cloud_mode ||
            (isset($this->update_data['@core'])
                && version_compare($this->update_data['@core']['my_version'], '6.0.18') < 0))
        {
            $patcher = new OldTemplatePatcher();
            return $patcher->patch($timeout, $previous_state);
        }

        return true;
    }


    /**
     * Конвертируем все таблицы в кодировку UTF8mb4, чтобы поддерживались emoji
     *
     * @param $timeout
     * @param array $previous_state
     * @return array|bool|string
     * @throws \RS\Db\Exception
     */
    protected function patchConvertTablesToCharsetUtf8mb4($timeout, $previous_state)
    {
        $patcher = new ConvertTablesToAllowEmoji();
        return $patcher->patch($timeout, $previous_state);
    }
}