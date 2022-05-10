<div class="formbox" >
    {if $elem._before_form_template}{include file=$elem._before_form_template}{/if}

                <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                            <li class=" active"><a data-target="#alerts-config-file-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                            <li class=""><a data-target="#alerts-config-file-tab1" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(1)}</a></li>
                    </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none">
                            <div class="tab-pane active" id="alerts-config-file-tab0" role="tabpanel">
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
                                        <td class="otitle">{$elem.__sms_sender_class->getTitle()}&nbsp;&nbsp;{if $elem.__sms_sender_class->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__sms_sender_class->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__sms_sender_class->getRenderTemplate() field=$elem.__sms_sender_class}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__sms_sender_login->getTitle()}&nbsp;&nbsp;{if $elem.__sms_sender_login->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__sms_sender_login->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__sms_sender_login->getRenderTemplate() field=$elem.__sms_sender_login}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__sms_sender_pass->getTitle()}&nbsp;&nbsp;{if $elem.__sms_sender_pass->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__sms_sender_pass->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__sms_sender_pass->getRenderTemplate() field=$elem.__sms_sender_pass}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                            <div class="tab-pane" id="alerts-config-file-tab1" role="tabpanel">
                                                                                                                                                                                                                                <table class="otable">
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__notice_items_delete_hours->getTitle()}&nbsp;&nbsp;{if $elem.__notice_items_delete_hours->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__notice_items_delete_hours->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__notice_items_delete_hours->getRenderTemplate() field=$elem.__notice_items_delete_hours}</td>
                                        </tr>
                                    
                                                                    
                                        <tr>
                                        <td class="otitle">{$elem.__allow_user_groups->getTitle()}&nbsp;&nbsp;{if $elem.__allow_user_groups->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__allow_user_groups->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__allow_user_groups->getRenderTemplate() field=$elem.__allow_user_groups}</td>
                                        </tr>
                                    
                                                            </table>
                                                            </div>
                    </form>
    </div>
    </div>