
<div class="formbox" >
            
    <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                    <li class=" active"><a data-target="#site-config-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                    <li class=""><a data-target="#site-config-tab1" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(1)}</a></li>
                    <li class=""><a data-target="#site-config-tab2" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(2)}</a></li>
                    <li class=""><a data-target="#site-config-tab3" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(3)}</a></li>
                    <li class=""><a data-target="#site-config-tab4" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(4)}</a></li>
        
        </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none"/>
                        <div class="tab-pane active" id="site-config-tab0" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                            
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__admin_email->getTitle()}&nbsp;&nbsp;{if $elem.__admin_email->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__admin_email->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__admin_email->getRenderTemplate() field=$elem.__admin_email}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__admin_phone->getTitle()}&nbsp;&nbsp;{if $elem.__admin_phone->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__admin_phone->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__admin_phone->getRenderTemplate() field=$elem.__admin_phone}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__theme->getTitle()}&nbsp;&nbsp;{if $elem.__theme->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__theme->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__theme->getRenderTemplate() field=$elem.__theme}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__favicon->getTitle()}&nbsp;&nbsp;{if $elem.__favicon->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__favicon->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__favicon->getRenderTemplate() field=$elem.__favicon}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__favicon_svg->getTitle()}&nbsp;&nbsp;{if $elem.__favicon_svg->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__favicon_svg->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__favicon_svg->getRenderTemplate() field=$elem.__favicon_svg}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="site-config-tab1" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__logo->getTitle()}&nbsp;&nbsp;{if $elem.__logo->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__logo->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__logo->getRenderTemplate() field=$elem.__logo}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__slogan->getTitle()}&nbsp;&nbsp;{if $elem.__slogan->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__slogan->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__slogan->getRenderTemplate() field=$elem.__slogan}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_name->getTitle()}&nbsp;&nbsp;{if $elem.__firm_name->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_name->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_name->getRenderTemplate() field=$elem.__firm_name}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_inn->getTitle()}&nbsp;&nbsp;{if $elem.__firm_inn->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_inn->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_inn->getRenderTemplate() field=$elem.__firm_inn}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_kpp->getTitle()}&nbsp;&nbsp;{if $elem.__firm_kpp->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_kpp->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_kpp->getRenderTemplate() field=$elem.__firm_kpp}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_bank->getTitle()}&nbsp;&nbsp;{if $elem.__firm_bank->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_bank->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_bank->getRenderTemplate() field=$elem.__firm_bank}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_bik->getTitle()}&nbsp;&nbsp;{if $elem.__firm_bik->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_bik->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_bik->getRenderTemplate() field=$elem.__firm_bik}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_rs->getTitle()}&nbsp;&nbsp;{if $elem.__firm_rs->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_rs->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_rs->getRenderTemplate() field=$elem.__firm_rs}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_ks->getTitle()}&nbsp;&nbsp;{if $elem.__firm_ks->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_ks->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_ks->getRenderTemplate() field=$elem.__firm_ks}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_director->getTitle()}&nbsp;&nbsp;{if $elem.__firm_director->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_director->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_director->getRenderTemplate() field=$elem.__firm_director}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_accountant->getTitle()}&nbsp;&nbsp;{if $elem.__firm_accountant->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_accountant->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_accountant->getRenderTemplate() field=$elem.__firm_accountant}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_v_lice->getTitle()}&nbsp;&nbsp;{if $elem.__firm_v_lice->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_v_lice->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_v_lice->getRenderTemplate() field=$elem.__firm_v_lice}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_deistvuet->getTitle()}&nbsp;&nbsp;{if $elem.__firm_deistvuet->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_deistvuet->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_deistvuet->getRenderTemplate() field=$elem.__firm_deistvuet}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_address->getTitle()}&nbsp;&nbsp;{if $elem.__firm_address->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_address->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_address->getRenderTemplate() field=$elem.__firm_address}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_legal_address->getTitle()}&nbsp;&nbsp;{if $elem.__firm_legal_address->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_legal_address->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_legal_address->getRenderTemplate() field=$elem.__firm_legal_address}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_email->getTitle()}&nbsp;&nbsp;{if $elem.__firm_email->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_email->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_email->getRenderTemplate() field=$elem.__firm_email}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__firm_name_for_notice->getTitle()}&nbsp;&nbsp;{if $elem.__firm_name_for_notice->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__firm_name_for_notice->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__firm_name_for_notice->getRenderTemplate() field=$elem.__firm_name_for_notice}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="site-config-tab2" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__notice_from->getTitle()}&nbsp;&nbsp;{if $elem.__notice_from->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__notice_from->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__notice_from->getRenderTemplate() field=$elem.__notice_from}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__notice_reply->getTitle()}&nbsp;&nbsp;{if $elem.__notice_reply->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__notice_reply->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__notice_reply->getRenderTemplate() field=$elem.__notice_reply}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__smtp_is_use->getTitle()}&nbsp;&nbsp;{if $elem.__smtp_is_use->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__smtp_is_use->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__smtp_is_use->getRenderTemplate() field=$elem.__smtp_is_use}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__smtp_host->getTitle()}&nbsp;&nbsp;{if $elem.__smtp_host->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__smtp_host->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__smtp_host->getRenderTemplate() field=$elem.__smtp_host}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__smtp_port->getTitle()}&nbsp;&nbsp;{if $elem.__smtp_port->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__smtp_port->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__smtp_port->getRenderTemplate() field=$elem.__smtp_port}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__smtp_secure->getTitle()}&nbsp;&nbsp;{if $elem.__smtp_secure->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__smtp_secure->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__smtp_secure->getRenderTemplate() field=$elem.__smtp_secure}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__smtp_auth->getTitle()}&nbsp;&nbsp;{if $elem.__smtp_auth->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__smtp_auth->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__smtp_auth->getRenderTemplate() field=$elem.__smtp_auth}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__smtp_username->getTitle()}&nbsp;&nbsp;{if $elem.__smtp_username->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__smtp_username->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__smtp_username->getRenderTemplate() field=$elem.__smtp_username}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__smtp_password->getTitle()}&nbsp;&nbsp;{if $elem.__smtp_password->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__smtp_password->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__smtp_password->getRenderTemplate() field=$elem.__smtp_password}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__dkim_is_use->getTitle()}&nbsp;&nbsp;{if $elem.__dkim_is_use->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dkim_is_use->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__dkim_is_use->getRenderTemplate() field=$elem.__dkim_is_use}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__dkim_domain->getTitle()}&nbsp;&nbsp;{if $elem.__dkim_domain->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dkim_domain->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__dkim_domain->getRenderTemplate() field=$elem.__dkim_domain}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__dkim_private->getTitle()}&nbsp;&nbsp;{if $elem.__dkim_private->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dkim_private->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__dkim_private->getRenderTemplate() field=$elem.__dkim_private}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__dkim_selector->getTitle()}&nbsp;&nbsp;{if $elem.__dkim_selector->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dkim_selector->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__dkim_selector->getRenderTemplate() field=$elem.__dkim_selector}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__dkim_passphrase->getTitle()}&nbsp;&nbsp;{if $elem.__dkim_passphrase->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dkim_passphrase->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__dkim_passphrase->getRenderTemplate() field=$elem.__dkim_passphrase}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="site-config-tab3" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__facebook_group->getTitle()}&nbsp;&nbsp;{if $elem.__facebook_group->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__facebook_group->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__facebook_group->getRenderTemplate() field=$elem.__facebook_group}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__vkontakte_group->getTitle()}&nbsp;&nbsp;{if $elem.__vkontakte_group->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__vkontakte_group->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__vkontakte_group->getRenderTemplate() field=$elem.__vkontakte_group}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__twitter_group->getTitle()}&nbsp;&nbsp;{if $elem.__twitter_group->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__twitter_group->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__twitter_group->getRenderTemplate() field=$elem.__twitter_group}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__instagram_group->getTitle()}&nbsp;&nbsp;{if $elem.__instagram_group->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__instagram_group->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__instagram_group->getRenderTemplate() field=$elem.__instagram_group}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__youtube_group->getTitle()}&nbsp;&nbsp;{if $elem.__youtube_group->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__youtube_group->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__youtube_group->getRenderTemplate() field=$elem.__youtube_group}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__viber_group->getTitle()}&nbsp;&nbsp;{if $elem.__viber_group->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__viber_group->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__viber_group->getRenderTemplate() field=$elem.__viber_group}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__telegram_group->getTitle()}&nbsp;&nbsp;{if $elem.__telegram_group->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__telegram_group->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__telegram_group->getRenderTemplate() field=$elem.__telegram_group}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__whatsapp_group->getTitle()}&nbsp;&nbsp;{if $elem.__whatsapp_group->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__whatsapp_group->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__whatsapp_group->getRenderTemplate() field=$elem.__whatsapp_group}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="site-config-tab4" role="tabpanel">
                                                                                                                                                                                                                                                                    
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__policy_personal_data->getTitle()}&nbsp;&nbsp;{if $elem.__policy_personal_data->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__policy_personal_data->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__policy_personal_data->getRenderTemplate() field=$elem.__policy_personal_data}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__agreement_personal_data->getTitle()}&nbsp;&nbsp;{if $elem.__agreement_personal_data->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__agreement_personal_data->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__agreement_personal_data->getRenderTemplate() field=$elem.__agreement_personal_data}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__enable_agreement_personal_data->getTitle()}&nbsp;&nbsp;{if $elem.__enable_agreement_personal_data->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__enable_agreement_personal_data->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__enable_agreement_personal_data->getRenderTemplate() field=$elem.__enable_agreement_personal_data}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
            
        </form>
    </div>
    </div>