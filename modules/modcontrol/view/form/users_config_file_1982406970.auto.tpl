<div class="formbox" >
    {if $elem._before_form_template}{include file=$elem._before_form_template}{/if}

                <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                            <li class=" active"><a data-target="#users-config-file-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                            <li class=""><a data-target="#users-config-file-tab1" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(1)}</a></li>
                            <li class=""><a data-target="#users-config-file-tab2" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(2)}</a></li>
                            <li class=""><a data-target="#users-config-file-tab3" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(3)}</a></li>
                            <li class=""><a data-target="#users-config-file-tab4" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(4)}</a></li>
                            <li class=""><a data-target="#users-config-file-tab5" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(5)}</a></li>
                    </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none">
                            <div class="tab-pane active" id="users-config-file-tab0" role="tabpanel">
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
                                        <td class="otitle">{$elem.__generate_password_length->getTitle()}&nbsp;&nbsp;{if $elem.__generate_password_length->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__generate_password_length->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__generate_password_length->getRenderTemplate() field=$elem.__generate_password_length}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__replace_country_phone_code->getTitle()}&nbsp;&nbsp;{if $elem.__replace_country_phone_code->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__replace_country_phone_code->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__replace_country_phone_code->getRenderTemplate() field=$elem.__replace_country_phone_code}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__country_phone_length->getTitle()}&nbsp;&nbsp;{if $elem.__country_phone_length->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__country_phone_length->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__country_phone_length->getRenderTemplate() field=$elem.__country_phone_length}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__default_country_phone_code->getTitle()}&nbsp;&nbsp;{if $elem.__default_country_phone_code->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_country_phone_code->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__default_country_phone_code->getRenderTemplate() field=$elem.__default_country_phone_code}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__generate_password_symbols->getTitle()}&nbsp;&nbsp;{if $elem.__generate_password_symbols->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__generate_password_symbols->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__generate_password_symbols->getRenderTemplate() field=$elem.__generate_password_symbols}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="users-config-file-tab1" role="tabpanel">
                                                                                                                            {include file=$elem.____userfields__->getRenderTemplate() field=$elem.____userfields__}
                                                                                                                                                </div>
                            <div class="tab-pane" id="users-config-file-tab2" role="tabpanel">
                                                                                                                                                                            <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__csv_id_fields->getTitle()}&nbsp;&nbsp;{if $elem.__csv_id_fields->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__csv_id_fields->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__csv_id_fields->getRenderTemplate() field=$elem.__csv_id_fields}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="users-config-file-tab3" role="tabpanel">
                                                                                                                                                                                                                                <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__clear_for_last_time->getTitle()}&nbsp;&nbsp;{if $elem.__clear_for_last_time->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__clear_for_last_time->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__clear_for_last_time->getRenderTemplate() field=$elem.__clear_for_last_time}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__clear_random->getTitle()}&nbsp;&nbsp;{if $elem.__clear_random->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__clear_random->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__clear_random->getRenderTemplate() field=$elem.__clear_random}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="users-config-file-tab4" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__type_auth->getTitle()}&nbsp;&nbsp;{if $elem.__type_auth->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__type_auth->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__type_auth->getRenderTemplate() field=$elem.__type_auth}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__type_code_provider->getTitle()}&nbsp;&nbsp;{if $elem.__type_code_provider->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__type_code_provider->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__type_code_provider->getRenderTemplate() field=$elem.__type_code_provider}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__two_factor_auth->getTitle()}&nbsp;&nbsp;{if $elem.__two_factor_auth->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__two_factor_auth->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__two_factor_auth->getRenderTemplate() field=$elem.__two_factor_auth}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__two_factor_register->getTitle()}&nbsp;&nbsp;{if $elem.__two_factor_register->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__two_factor_register->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__two_factor_register->getRenderTemplate() field=$elem.__two_factor_register}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__two_factor_recover->getTitle()}&nbsp;&nbsp;{if $elem.__two_factor_recover->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__two_factor_recover->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__two_factor_recover->getRenderTemplate() field=$elem.__two_factor_recover}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__lifetime_resolved_session_hours->getTitle()}&nbsp;&nbsp;{if $elem.__lifetime_resolved_session_hours->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__lifetime_resolved_session_hours->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__lifetime_resolved_session_hours->getRenderTemplate() field=$elem.__lifetime_resolved_session_hours}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__register_by_phone->getTitle()}&nbsp;&nbsp;{if $elem.__register_by_phone->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__register_by_phone->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__register_by_phone->getRenderTemplate() field=$elem.__register_by_phone}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__send_count_limit->getTitle()}&nbsp;&nbsp;{if $elem.__send_count_limit->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__send_count_limit->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__send_count_limit->getRenderTemplate() field=$elem.__send_count_limit}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__resend_delay_seconds->getTitle()}&nbsp;&nbsp;{if $elem.__resend_delay_seconds->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__resend_delay_seconds->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__resend_delay_seconds->getRenderTemplate() field=$elem.__resend_delay_seconds}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__block_delay_minutes->getTitle()}&nbsp;&nbsp;{if $elem.__block_delay_minutes->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__block_delay_minutes->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__block_delay_minutes->getRenderTemplate() field=$elem.__block_delay_minutes}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__lifetime_session_hours->getTitle()}&nbsp;&nbsp;{if $elem.__lifetime_session_hours->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__lifetime_session_hours->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__lifetime_session_hours->getRenderTemplate() field=$elem.__lifetime_session_hours}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__try_count_limit->getTitle()}&nbsp;&nbsp;{if $elem.__try_count_limit->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__try_count_limit->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__try_count_limit->getRenderTemplate() field=$elem.__try_count_limit}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__lifetime_code_minutes->getTitle()}&nbsp;&nbsp;{if $elem.__lifetime_code_minutes->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__lifetime_code_minutes->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__lifetime_code_minutes->getRenderTemplate() field=$elem.__lifetime_code_minutes}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__ip_limit_session_count->getTitle()}&nbsp;&nbsp;{if $elem.__ip_limit_session_count->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__ip_limit_session_count->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__ip_limit_session_count->getRenderTemplate() field=$elem.__ip_limit_session_count}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__code_length->getTitle()}&nbsp;&nbsp;{if $elem.__code_length->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__code_length->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__code_length->getRenderTemplate() field=$elem.__code_length}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__two_factor_demo_mode->getTitle()}&nbsp;&nbsp;{if $elem.__two_factor_demo_mode->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__two_factor_demo_mode->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__two_factor_demo_mode->getRenderTemplate() field=$elem.__two_factor_demo_mode}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="users-config-file-tab5" role="tabpanel">
                                                                                                                            {include file=$elem.____field_options__->getRenderTemplate() field=$elem.____field_options__}
                                                                                                                                                                                                                                                                                                                                                                </div>
                    </form>
    </div>
    </div>