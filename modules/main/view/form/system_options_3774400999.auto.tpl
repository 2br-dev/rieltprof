
<div class="formbox" >
            
    <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                    <li class=" active"><a data-target="#rs-config-cms-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                    <li class=""><a data-target="#rs-config-cms-tab1" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(1)}</a></li>
                    <li class=""><a data-target="#rs-config-cms-tab2" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(2)}</a></li>
                    <li class=""><a data-target="#rs-config-cms-tab3" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(3)}</a></li>
                    <li class=""><a data-target="#rs-config-cms-tab4" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(4)}</a></li>
                    <li class=""><a data-target="#rs-config-cms-tab5" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(5)}</a></li>
                    <li class=""><a data-target="#rs-config-cms-tab6" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(6)}</a></li>
        
        </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none"/>
                        <div class="tab-pane active" id="rs-config-cms-tab0" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__ADMIN_SECTION->getTitle()}&nbsp;&nbsp;{if $elem.__ADMIN_SECTION->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__ADMIN_SECTION->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__ADMIN_SECTION->getRenderTemplate() field=$elem.__ADMIN_SECTION}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__SM_COMPILE_CHECK->getTitle()}&nbsp;&nbsp;{if $elem.__SM_COMPILE_CHECK->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__SM_COMPILE_CHECK->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__SM_COMPILE_CHECK->getRenderTemplate() field=$elem.__SM_COMPILE_CHECK}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__DETAILED_EXCEPTION->getTitle()}&nbsp;&nbsp;{if $elem.__DETAILED_EXCEPTION->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__DETAILED_EXCEPTION->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__DETAILED_EXCEPTION->getRenderTemplate() field=$elem.__DETAILED_EXCEPTION}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__CACHE_ENABLED->getTitle()}&nbsp;&nbsp;{if $elem.__CACHE_ENABLED->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__CACHE_ENABLED->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__CACHE_ENABLED->getRenderTemplate() field=$elem.__CACHE_ENABLED}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__CACHE_BLOCK_ENABLED->getTitle()}&nbsp;&nbsp;{if $elem.__CACHE_BLOCK_ENABLED->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__CACHE_BLOCK_ENABLED->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__CACHE_BLOCK_ENABLED->getRenderTemplate() field=$elem.__CACHE_BLOCK_ENABLED}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__CACHE_TIME->getTitle()}&nbsp;&nbsp;{if $elem.__CACHE_TIME->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__CACHE_TIME->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__CACHE_TIME->getRenderTemplate() field=$elem.__CACHE_TIME}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__COMPRESS_CSS->getTitle()}&nbsp;&nbsp;{if $elem.__COMPRESS_CSS->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__COMPRESS_CSS->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__COMPRESS_CSS->getRenderTemplate() field=$elem.__COMPRESS_CSS}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__COMPRESS_JS->getTitle()}&nbsp;&nbsp;{if $elem.__COMPRESS_JS->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__COMPRESS_JS->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__COMPRESS_JS->getRenderTemplate() field=$elem.__COMPRESS_JS}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__JS_POSITION_FOOTER->getTitle()}&nbsp;&nbsp;{if $elem.__JS_POSITION_FOOTER->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__JS_POSITION_FOOTER->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__JS_POSITION_FOOTER->getRenderTemplate() field=$elem.__JS_POSITION_FOOTER}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__CSS_POSITION_FOOTER->getTitle()}&nbsp;&nbsp;{if $elem.__CSS_POSITION_FOOTER->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__CSS_POSITION_FOOTER->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__CSS_POSITION_FOOTER->getRenderTemplate() field=$elem.__CSS_POSITION_FOOTER}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__show_debug_header->getTitle()}&nbsp;&nbsp;{if $elem.__show_debug_header->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__show_debug_header->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__show_debug_header->getRenderTemplate() field=$elem.__show_debug_header}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__CRON_ENABLE->getTitle()}&nbsp;&nbsp;{if $elem.__CRON_ENABLE->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__CRON_ENABLE->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__CRON_ENABLE->getRenderTemplate() field=$elem.__CRON_ENABLE}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__TIMEZONE->getTitle()}&nbsp;&nbsp;{if $elem.__TIMEZONE->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__TIMEZONE->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__TIMEZONE->getRenderTemplate() field=$elem.__TIMEZONE}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__COMPRESS_ADMIN_ENABLE->getTitle()}&nbsp;&nbsp;{if $elem.__COMPRESS_ADMIN_ENABLE->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__COMPRESS_ADMIN_ENABLE->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__COMPRESS_ADMIN_ENABLE->getRenderTemplate() field=$elem.__COMPRESS_ADMIN_ENABLE}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__METER_RECALCULATE_INTERVAL->getTitle()}&nbsp;&nbsp;{if $elem.__METER_RECALCULATE_INTERVAL->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__METER_RECALCULATE_INTERVAL->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__METER_RECALCULATE_INTERVAL->getRenderTemplate() field=$elem.__METER_RECALCULATE_INTERVAL}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__ENABLE_DEBUG_PROFILING->getTitle()}&nbsp;&nbsp;{if $elem.__ENABLE_DEBUG_PROFILING->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__ENABLE_DEBUG_PROFILING->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__ENABLE_DEBUG_PROFILING->getRenderTemplate() field=$elem.__ENABLE_DEBUG_PROFILING}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__LOG_QUERY_STACK_TRACE_LEVEL->getTitle()}&nbsp;&nbsp;{if $elem.__LOG_QUERY_STACK_TRACE_LEVEL->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__LOG_QUERY_STACK_TRACE_LEVEL->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__LOG_QUERY_STACK_TRACE_LEVEL->getRenderTemplate() field=$elem.__LOG_QUERY_STACK_TRACE_LEVEL}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="rs-config-cms-tab1" role="tabpanel">
                                                                                                            {include file=$elem.____cache__->getRenderTemplate() field=$elem.____cache__}
                                                                                                
                                                </div>
                        <div class="tab-pane" id="rs-config-cms-tab2" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
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
                                    <td class="otitle">{$elem.__hostname->getTitle()}&nbsp;&nbsp;{if $elem.__hostname->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__hostname->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__hostname->getRenderTemplate() field=$elem.__hostname}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="rs-config-cms-tab3" role="tabpanel">
                                                                                                                            
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__captcha_class->getTitle()}&nbsp;&nbsp;{if $elem.__captcha_class->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__captcha_class->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__captcha_class->getRenderTemplate() field=$elem.__captcha_class}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="rs-config-cms-tab4" role="tabpanel">
                                                                                                                            
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__access_priority->getTitle()}&nbsp;&nbsp;{if $elem.__access_priority->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__access_priority->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__access_priority->getRenderTemplate() field=$elem.__access_priority}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="rs-config-cms-tab5" role="tabpanel">
                                                                                                                            
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__robot_user_agents->getTitle()}&nbsp;&nbsp;{if $elem.__robot_user_agents->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__robot_user_agents->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__robot_user_agents->getRenderTemplate() field=$elem.__robot_user_agents}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="rs-config-cms-tab6" role="tabpanel">
                                                                                                            {include file=$elem.___log_settings_->getRenderTemplate() field=$elem.___log_settings_}
                                                                                                
                                                </div>
            
        </form>
    </div>
    </div>