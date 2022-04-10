<div class="formbox" >
    {if $elem._before_form_template}{include file=$elem._before_form_template}{/if}

                <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                            <li class=" active"><a data-target="#catalog-config-file-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                            <li class=""><a data-target="#catalog-config-file-tab1" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(1)}</a></li>
                            <li class=""><a data-target="#catalog-config-file-tab2" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(2)}</a></li>
                            <li class=""><a data-target="#catalog-config-file-tab3" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(3)}</a></li>
                            <li class=""><a data-target="#catalog-config-file-tab4" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(4)}</a></li>
                            <li class=""><a data-target="#catalog-config-file-tab5" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(5)}</a></li>
                            <li class=""><a data-target="#catalog-config-file-tab6" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(6)}</a></li>
                            <li class=""><a data-target="#catalog-config-file-tab7" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(7)}</a></li>
                    </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none">
                            <div class="tab-pane active" id="catalog-config-file-tab0" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__name->getTitle()}&nbsp;&nbsp;{if $elem.__name->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__name->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__name->getRenderTemplate() field=$elem.__name}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__description->getTitle()}&nbsp;&nbsp;{if $elem.__description->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__description->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__description->getRenderTemplate() field=$elem.__description}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__version->getTitle()}&nbsp;&nbsp;{if $elem.__version->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__version->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__version->getRenderTemplate() field=$elem.__version}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__core_version->getTitle()}&nbsp;&nbsp;{if $elem.__core_version->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__core_version->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__core_version->getRenderTemplate() field=$elem.__core_version}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__author->getTitle()}&nbsp;&nbsp;{if $elem.__author->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__author->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__author->getRenderTemplate() field=$elem.__author}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__enabled->getTitle()}&nbsp;&nbsp;{if $elem.__enabled->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__enabled->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__enabled->getRenderTemplate() field=$elem.__enabled}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__default_cost->getTitle()}&nbsp;&nbsp;{if $elem.__default_cost->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_cost->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__default_cost->getRenderTemplate() field=$elem.__default_cost}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__old_cost->getTitle()}&nbsp;&nbsp;{if $elem.__old_cost->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__old_cost->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__old_cost->getRenderTemplate() field=$elem.__old_cost}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__hide_unobtainable_goods->getTitle()}&nbsp;&nbsp;{if $elem.__hide_unobtainable_goods->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__hide_unobtainable_goods->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__hide_unobtainable_goods->getRenderTemplate() field=$elem.__hide_unobtainable_goods}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__list_page_size->getTitle()}&nbsp;&nbsp;{if $elem.__list_page_size->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__list_page_size->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__list_page_size->getRenderTemplate() field=$elem.__list_page_size}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__items_on_page->getTitle()}&nbsp;&nbsp;{if $elem.__items_on_page->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__items_on_page->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__items_on_page->getRenderTemplate() field=$elem.__items_on_page}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__list_default_order->getTitle()}&nbsp;&nbsp;{if $elem.__list_default_order->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__list_default_order->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__list_default_order->getRenderTemplate() field=$elem.__list_default_order}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__list_default_order_direction->getTitle()}&nbsp;&nbsp;{if $elem.__list_default_order_direction->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__list_default_order_direction->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__list_default_order_direction->getRenderTemplate() field=$elem.__list_default_order_direction}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__list_order_instok_first->getTitle()}&nbsp;&nbsp;{if $elem.__list_order_instok_first->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__list_order_instok_first->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__list_order_instok_first->getRenderTemplate() field=$elem.__list_order_instok_first}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__list_default_view_as->getTitle()}&nbsp;&nbsp;{if $elem.__list_default_view_as->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__list_default_view_as->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__list_default_view_as->getRenderTemplate() field=$elem.__list_default_view_as}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__default_unit->getTitle()}&nbsp;&nbsp;{if $elem.__default_unit->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_unit->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__default_unit->getRenderTemplate() field=$elem.__default_unit}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__concat_dir_meta->getTitle()}&nbsp;&nbsp;{if $elem.__concat_dir_meta->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__concat_dir_meta->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__concat_dir_meta->getRenderTemplate() field=$elem.__concat_dir_meta}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__auto_barcode->getTitle()}&nbsp;&nbsp;{if $elem.__auto_barcode->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__auto_barcode->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__auto_barcode->getRenderTemplate() field=$elem.__auto_barcode}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__disable_search_index->getTitle()}&nbsp;&nbsp;{if $elem.__disable_search_index->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__disable_search_index->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__disable_search_index->getRenderTemplate() field=$elem.__disable_search_index}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__price_round->getTitle()}&nbsp;&nbsp;{if $elem.__price_round->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__price_round->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__price_round->getRenderTemplate() field=$elem.__price_round}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__cbr_link->getTitle()}&nbsp;&nbsp;{if $elem.__cbr_link->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__cbr_link->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__cbr_link->getRenderTemplate() field=$elem.__cbr_link}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__cbr_auto_update_interval->getTitle()}&nbsp;&nbsp;{if $elem.__cbr_auto_update_interval->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__cbr_auto_update_interval->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__cbr_auto_update_interval->getRenderTemplate() field=$elem.__cbr_auto_update_interval}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__cbr_percent_update->getTitle()}&nbsp;&nbsp;{if $elem.__cbr_percent_update->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__cbr_percent_update->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__cbr_percent_update->getRenderTemplate() field=$elem.__cbr_percent_update}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__use_offer_unit->getTitle()}&nbsp;&nbsp;{if $elem.__use_offer_unit->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__use_offer_unit->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__use_offer_unit->getRenderTemplate() field=$elem.__use_offer_unit}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__import_photos_timeout->getTitle()}&nbsp;&nbsp;{if $elem.__import_photos_timeout->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__import_photos_timeout->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__import_photos_timeout->getRenderTemplate() field=$elem.__import_photos_timeout}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__use_seo_filters->getTitle()}&nbsp;&nbsp;{if $elem.__use_seo_filters->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__use_seo_filters->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__use_seo_filters->getRenderTemplate() field=$elem.__use_seo_filters}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__show_all_products->getTitle()}&nbsp;&nbsp;{if $elem.__show_all_products->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__show_all_products->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__show_all_products->getRenderTemplate() field=$elem.__show_all_products}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__price_like_slider->getTitle()}&nbsp;&nbsp;{if $elem.__price_like_slider->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__price_like_slider->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__price_like_slider->getRenderTemplate() field=$elem.__price_like_slider}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__search_fields->getTitle()}&nbsp;&nbsp;{if $elem.__search_fields->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__search_fields->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__search_fields->getRenderTemplate() field=$elem.__search_fields}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__not_public_category_404->getTitle()}&nbsp;&nbsp;{if $elem.__not_public_category_404->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__not_public_category_404->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__not_public_category_404->getRenderTemplate() field=$elem.__not_public_category_404}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__not_public_product_404->getTitle()}&nbsp;&nbsp;{if $elem.__not_public_product_404->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__not_public_product_404->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__not_public_product_404->getRenderTemplate() field=$elem.__not_public_product_404}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__not_public_property_dir_404->getTitle()}&nbsp;&nbsp;{if $elem.__not_public_property_dir_404->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__not_public_property_dir_404->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__not_public_property_dir_404->getRenderTemplate() field=$elem.__not_public_property_dir_404}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__link_property_to_offer_amount->getTitle()}&nbsp;&nbsp;{if $elem.__link_property_to_offer_amount->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__link_property_to_offer_amount->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__link_property_to_offer_amount->getRenderTemplate() field=$elem.__link_property_to_offer_amount}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__dependent_filters->getTitle()}&nbsp;&nbsp;{if $elem.__dependent_filters->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dependent_filters->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__dependent_filters->getRenderTemplate() field=$elem.__dependent_filters}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__tabs_on_new_page->getTitle()}&nbsp;&nbsp;{if $elem.__tabs_on_new_page->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__tabs_on_new_page->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__tabs_on_new_page->getRenderTemplate() field=$elem.__tabs_on_new_page}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="catalog-config-file-tab1" role="tabpanel">
                                                                                                                            {include file=$elem.____clickfields__->getRenderTemplate() field=$elem.____clickfields__}
                                                                                                                                                                                                                                                                                                            </div>
                            <div class="tab-pane" id="catalog-config-file-tab2" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_id_fields->getTitle()}&nbsp;&nbsp;{if $elem.__csv_id_fields->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_id_fields->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_id_fields->getRenderTemplate() field=$elem.__csv_id_fields}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_offer_product_search_field->getTitle()}&nbsp;&nbsp;{if $elem.__csv_offer_product_search_field->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_offer_product_search_field->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_offer_product_search_field->getRenderTemplate() field=$elem.__csv_offer_product_search_field}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_offer_search_field->getTitle()}&nbsp;&nbsp;{if $elem.__csv_offer_search_field->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_offer_search_field->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_offer_search_field->getRenderTemplate() field=$elem.__csv_offer_search_field}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_dont_delete_stocks->getTitle()}&nbsp;&nbsp;{if $elem.__csv_dont_delete_stocks->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_dont_delete_stocks->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_dont_delete_stocks->getRenderTemplate() field=$elem.__csv_dont_delete_stocks}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_dont_delete_costs->getTitle()}&nbsp;&nbsp;{if $elem.__csv_dont_delete_costs->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_dont_delete_costs->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_dont_delete_costs->getRenderTemplate() field=$elem.__csv_dont_delete_costs}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_file_upload_type->getTitle()}&nbsp;&nbsp;{if $elem.__csv_file_upload_type->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_file_upload_type->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_file_upload_type->getRenderTemplate() field=$elem.__csv_file_upload_type}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_file_upload_access->getTitle()}&nbsp;&nbsp;{if $elem.__csv_file_upload_access->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_file_upload_access->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_file_upload_access->getRenderTemplate() field=$elem.__csv_file_upload_access}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="catalog-config-file-tab3" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                            <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__brand_products_type->getTitle()}&nbsp;&nbsp;{if $elem.__brand_products_type->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__brand_products_type->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__brand_products_type->getRenderTemplate() field=$elem.__brand_products_type}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__brand_products_specdir->getTitle()}&nbsp;&nbsp;{if $elem.__brand_products_specdir->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__brand_products_specdir->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__brand_products_specdir->getRenderTemplate() field=$elem.__brand_products_specdir}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__brand_products_cnt->getTitle()}&nbsp;&nbsp;{if $elem.__brand_products_cnt->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__brand_products_cnt->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__brand_products_cnt->getRenderTemplate() field=$elem.__brand_products_cnt}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__brand_products_hide_unobtainable->getTitle()}&nbsp;&nbsp;{if $elem.__brand_products_hide_unobtainable->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__brand_products_hide_unobtainable->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__brand_products_hide_unobtainable->getRenderTemplate() field=$elem.__brand_products_hide_unobtainable}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__brand_sort->getTitle()}&nbsp;&nbsp;{if $elem.__brand_sort->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__brand_sort->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__brand_sort->getRenderTemplate() field=$elem.__brand_sort}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="catalog-config-file-tab4" role="tabpanel">
                                                                                                                                                                            <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__warehouse_sticks->getTitle()}&nbsp;&nbsp;{if $elem.__warehouse_sticks->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__warehouse_sticks->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__warehouse_sticks->getRenderTemplate() field=$elem.__warehouse_sticks}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="catalog-config-file-tab5" role="tabpanel">
                                                                                                                                                                                                                                                                                    <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__ic_enable_button->getTitle()}&nbsp;&nbsp;{if $elem.__ic_enable_button->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__ic_enable_button->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__ic_enable_button->getRenderTemplate() field=$elem.__ic_enable_button}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__provider_user_group->getTitle()}&nbsp;&nbsp;{if $elem.__provider_user_group->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__provider_user_group->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__provider_user_group->getRenderTemplate() field=$elem.__provider_user_group}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_id_fields_ic->getTitle()}&nbsp;&nbsp;{if $elem.__csv_id_fields_ic->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_id_fields_ic->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_id_fields_ic->getRenderTemplate() field=$elem.__csv_id_fields_ic}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="catalog-config-file-tab6" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__yuml_import_setting->getTitle()}&nbsp;&nbsp;{if $elem.__yuml_import_setting->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__yuml_import_setting->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__yuml_import_setting->getRenderTemplate() field=$elem.__yuml_import_setting}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__import_yml_timeout->getTitle()}&nbsp;&nbsp;{if $elem.__import_yml_timeout->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__import_yml_timeout->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__import_yml_timeout->getRenderTemplate() field=$elem.__import_yml_timeout}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__import_yml_cost_id->getTitle()}&nbsp;&nbsp;{if $elem.__import_yml_cost_id->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__import_yml_cost_id->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__import_yml_cost_id->getRenderTemplate() field=$elem.__import_yml_cost_id}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__catalog_element_action->getTitle()}&nbsp;&nbsp;{if $elem.__catalog_element_action->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__catalog_element_action->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__catalog_element_action->getRenderTemplate() field=$elem.__catalog_element_action}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__catalog_section_action->getTitle()}&nbsp;&nbsp;{if $elem.__catalog_section_action->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__catalog_section_action->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__catalog_section_action->getRenderTemplate() field=$elem.__catalog_section_action}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__save_product_public->getTitle()}&nbsp;&nbsp;{if $elem.__save_product_public->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__save_product_public->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__save_product_public->getRenderTemplate() field=$elem.__save_product_public}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__save_product_dir->getTitle()}&nbsp;&nbsp;{if $elem.__save_product_dir->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__save_product_dir->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__save_product_dir->getRenderTemplate() field=$elem.__save_product_dir}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__dont_update_fields->getTitle()}&nbsp;&nbsp;{if $elem.__dont_update_fields->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dont_update_fields->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__dont_update_fields->getRenderTemplate() field=$elem.__dont_update_fields}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__use_htmlentity->getTitle()}&nbsp;&nbsp;{if $elem.__use_htmlentity->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__use_htmlentity->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__use_htmlentity->getRenderTemplate() field=$elem.__use_htmlentity}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__increase_cost->getTitle()}&nbsp;&nbsp;{if $elem.__increase_cost->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__increase_cost->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__increase_cost->getRenderTemplate() field=$elem.__increase_cost}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__use_vendorcode->getTitle()}&nbsp;&nbsp;{if $elem.__use_vendorcode->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__use_vendorcode->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__use_vendorcode->getRenderTemplate() field=$elem.__use_vendorcode}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__yml_product_group_identifier->getTitle()}&nbsp;&nbsp;{if $elem.__yml_product_group_identifier->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__yml_product_group_identifier->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__yml_product_group_identifier->getRenderTemplate() field=$elem.__yml_product_group_identifier}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__yml_offer_properties->getTitle()}&nbsp;&nbsp;{if $elem.__yml_offer_properties->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__yml_offer_properties->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__yml_offer_properties->getRenderTemplate() field=$elem.__yml_offer_properties}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__yml_import_multioffers->getTitle()}&nbsp;&nbsp;{if $elem.__yml_import_multioffers->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__yml_import_multioffers->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__yml_import_multioffers->getRenderTemplate() field=$elem.__yml_import_multioffers}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="catalog-config-file-tab7" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__default_product_meta_title->getTitle()}&nbsp;&nbsp;{if $elem.__default_product_meta_title->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_product_meta_title->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__default_product_meta_title->getRenderTemplate() field=$elem.__default_product_meta_title}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__default_product_meta_keywords->getTitle()}&nbsp;&nbsp;{if $elem.__default_product_meta_keywords->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_product_meta_keywords->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__default_product_meta_keywords->getRenderTemplate() field=$elem.__default_product_meta_keywords}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__default_product_meta_description->getTitle()}&nbsp;&nbsp;{if $elem.__default_product_meta_description->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_product_meta_description->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__default_product_meta_description->getRenderTemplate() field=$elem.__default_product_meta_description}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__default_weight->getTitle()}&nbsp;&nbsp;{if $elem.__default_weight->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_weight->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__default_weight->getRenderTemplate() field=$elem.__default_weight}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__weight_unit->getTitle()}&nbsp;&nbsp;{if $elem.__weight_unit->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__weight_unit->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__weight_unit->getRenderTemplate() field=$elem.__weight_unit}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__property_product_length->getTitle()}&nbsp;&nbsp;{if $elem.__property_product_length->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__property_product_length->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__property_product_length->getRenderTemplate() field=$elem.__property_product_length}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__default_product_length->getTitle()}&nbsp;&nbsp;{if $elem.__default_product_length->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_product_length->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__default_product_length->getRenderTemplate() field=$elem.__default_product_length}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__property_product_width->getTitle()}&nbsp;&nbsp;{if $elem.__property_product_width->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__property_product_width->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__property_product_width->getRenderTemplate() field=$elem.__property_product_width}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__default_product_width->getTitle()}&nbsp;&nbsp;{if $elem.__default_product_width->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_product_width->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__default_product_width->getRenderTemplate() field=$elem.__default_product_width}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__property_product_height->getTitle()}&nbsp;&nbsp;{if $elem.__property_product_height->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__property_product_height->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__property_product_height->getRenderTemplate() field=$elem.__property_product_height}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__default_product_height->getTitle()}&nbsp;&nbsp;{if $elem.__default_product_height->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_product_height->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__default_product_height->getRenderTemplate() field=$elem.__default_product_height}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__dimensions_unit->getTitle()}&nbsp;&nbsp;{if $elem.__dimensions_unit->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dimensions_unit->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__dimensions_unit->getRenderTemplate() field=$elem.__dimensions_unit}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                    </form>
    </div>
    </div>