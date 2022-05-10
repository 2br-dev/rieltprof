<div class="formbox" >
    {if $elem._before_form_template}{include file=$elem._before_form_template}{/if}

                <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                            <li class=" active"><a data-target="#main-config-file-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                            <li class=""><a data-target="#main-config-file-tab1" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(1)}</a></li>
                            <li class=""><a data-target="#main-config-file-tab2" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(2)}</a></li>
                            <li class=""><a data-target="#main-config-file-tab3" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(3)}</a></li>
                            <li class=""><a data-target="#main-config-file-tab4" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(4)}</a></li>
                            <li class=""><a data-target="#main-config-file-tab5" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(5)}</a></li>
                            <li class=""><a data-target="#main-config-file-tab6" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(6)}</a></li>
                            <li class=""><a data-target="#main-config-file-tab7" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(7)}</a></li>
                            <li class=""><a data-target="#main-config-file-tab8" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(8)}</a></li>
                            <li class=""><a data-target="#main-config-file-tab9" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(9)}</a></li>
                    </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none">
                            <div class="tab-pane active" id="main-config-file-tab0" role="tabpanel">
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
                                        <td class="otitle">{$elem.__map_type->getTitle()}&nbsp;&nbsp;{if $elem.__map_type->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__map_type->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__map_type->getRenderTemplate() field=$elem.__map_type}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="main-config-file-tab1" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__image_quality->getTitle()}&nbsp;&nbsp;{if $elem.__image_quality->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__image_quality->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__image_quality->getRenderTemplate() field=$elem.__image_quality}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__watermark->getTitle()}&nbsp;&nbsp;{if $elem.__watermark->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__watermark->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__watermark->getRenderTemplate() field=$elem.__watermark}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__wmark_min_width->getTitle()}&nbsp;&nbsp;{if $elem.__wmark_min_width->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__wmark_min_width->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__wmark_min_width->getRenderTemplate() field=$elem.__wmark_min_width}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__wmark_min_height->getTitle()}&nbsp;&nbsp;{if $elem.__wmark_min_height->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__wmark_min_height->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__wmark_min_height->getRenderTemplate() field=$elem.__wmark_min_height}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__wmark_pos_x->getTitle()}&nbsp;&nbsp;{if $elem.__wmark_pos_x->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__wmark_pos_x->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__wmark_pos_x->getRenderTemplate() field=$elem.__wmark_pos_x}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__wmark_pos_y->getTitle()}&nbsp;&nbsp;{if $elem.__wmark_pos_y->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__wmark_pos_y->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__wmark_pos_y->getRenderTemplate() field=$elem.__wmark_pos_y}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__wmark_opacity->getTitle()}&nbsp;&nbsp;{if $elem.__wmark_opacity->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__wmark_opacity->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__wmark_opacity->getRenderTemplate() field=$elem.__wmark_opacity}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__webp_generate_only->getTitle()}&nbsp;&nbsp;{if $elem.__webp_generate_only->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__webp_generate_only->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__webp_generate_only->getRenderTemplate() field=$elem.__webp_generate_only}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__webp_disable_on_apple->getTitle()}&nbsp;&nbsp;{if $elem.__webp_disable_on_apple->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__webp_disable_on_apple->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__webp_disable_on_apple->getRenderTemplate() field=$elem.__webp_disable_on_apple}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="main-config-file-tab2" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                        <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_charset->getTitle()}&nbsp;&nbsp;{if $elem.__csv_charset->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_charset->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_charset->getRenderTemplate() field=$elem.__csv_charset}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_delimiter->getTitle()}&nbsp;&nbsp;{if $elem.__csv_delimiter->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_delimiter->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_delimiter->getRenderTemplate() field=$elem.__csv_delimiter}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_check_timeout->getTitle()}&nbsp;&nbsp;{if $elem.__csv_check_timeout->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_check_timeout->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_check_timeout->getRenderTemplate() field=$elem.__csv_check_timeout}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_timeout->getTitle()}&nbsp;&nbsp;{if $elem.__csv_timeout->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_timeout->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_timeout->getRenderTemplate() field=$elem.__csv_timeout}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="main-config-file-tab3" role="tabpanel">
                                                                                                                                                                                                                                <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__geo_ip_service->getTitle()}&nbsp;&nbsp;{if $elem.__geo_ip_service->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__geo_ip_service->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__geo_ip_service->getRenderTemplate() field=$elem.__geo_ip_service}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__dadata_token->getTitle()}&nbsp;&nbsp;{if $elem.__dadata_token->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dadata_token->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__dadata_token->getRenderTemplate() field=$elem.__dadata_token}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="main-config-file-tab4" role="tabpanel">
                                                                                                                                                                                                                                                                                    <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__long_polling_can_enable->getTitle()}&nbsp;&nbsp;{if $elem.__long_polling_can_enable->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__long_polling_can_enable->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__long_polling_can_enable->getRenderTemplate() field=$elem.__long_polling_can_enable}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__long_polling_timeout_sec->getTitle()}&nbsp;&nbsp;{if $elem.__long_polling_timeout_sec->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__long_polling_timeout_sec->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__long_polling_timeout_sec->getRenderTemplate() field=$elem.__long_polling_timeout_sec}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__long_polling_event_listen_interval_sec->getTitle()}&nbsp;&nbsp;{if $elem.__long_polling_event_listen_interval_sec->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__long_polling_event_listen_interval_sec->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__long_polling_event_listen_interval_sec->getRenderTemplate() field=$elem.__long_polling_event_listen_interval_sec}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="main-config-file-tab5" role="tabpanel">
                                                                                                                                                                                                                                <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__yandex_services_hint->getTitle()}&nbsp;&nbsp;{if $elem.__yandex_services_hint->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__yandex_services_hint->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__yandex_services_hint->getRenderTemplate() field=$elem.__yandex_services_hint}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__yandex_js_api_geocoder->getTitle()}&nbsp;&nbsp;{if $elem.__yandex_js_api_geocoder->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__yandex_js_api_geocoder->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__yandex_js_api_geocoder->getRenderTemplate() field=$elem.__yandex_js_api_geocoder}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="main-config-file-tab6" role="tabpanel">
                                                                                                                                                                            <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__google_api_key_map->getTitle()}&nbsp;&nbsp;{if $elem.__google_api_key_map->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__google_api_key_map->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__google_api_key_map->getRenderTemplate() field=$elem.__google_api_key_map}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="main-config-file-tab7" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                        <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__dadata_service_hint->getTitle()}&nbsp;&nbsp;{if $elem.__dadata_service_hint->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dadata_service_hint->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__dadata_service_hint->getRenderTemplate() field=$elem.__dadata_service_hint}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__dadata_api_key->getTitle()}&nbsp;&nbsp;{if $elem.__dadata_api_key->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dadata_api_key->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__dadata_api_key->getRenderTemplate() field=$elem.__dadata_api_key}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__dadata_secret_key->getTitle()}&nbsp;&nbsp;{if $elem.__dadata_secret_key->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dadata_secret_key->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__dadata_secret_key->getRenderTemplate() field=$elem.__dadata_secret_key}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__dadata_enable_log->getTitle()}&nbsp;&nbsp;{if $elem.__dadata_enable_log->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dadata_enable_log->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__dadata_enable_log->getRenderTemplate() field=$elem.__dadata_enable_log}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="main-config-file-tab8" role="tabpanel">
                                                                                                                                                                            <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__enable_remote_support->getTitle()}&nbsp;&nbsp;{if $elem.__enable_remote_support->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__enable_remote_support->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__enable_remote_support->getRenderTemplate() field=$elem.__enable_remote_support}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="main-config-file-tab9" role="tabpanel">
                                                                                                                                                                            <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__rel_canonical_class->getTitle()}&nbsp;&nbsp;{if $elem.__rel_canonical_class->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__rel_canonical_class->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__rel_canonical_class->getRenderTemplate() field=$elem.__rel_canonical_class}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                    </form>
    </div>
    </div>