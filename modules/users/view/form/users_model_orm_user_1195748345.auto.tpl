<div class="formbox" >
                <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                    <li class=" active"><a data-target="#users-user-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                    <li class=""><a data-target="#users-user-tab1" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(1)}</a></li>
                    <li class=""><a data-target="#users-user-tab2" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(2)}</a></li>
                    <li class=""><a data-target="#users-user-tab3" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(3)}</a></li>
                    <li class=""><a data-target="#users-user-tab4" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(4)}</a></li>
                    <li class=""><a data-target="#users-user-tab5" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(5)}</a></li>
                    <li class=""><a data-target="#users-user-tab6" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(6)}</a></li>
                    <li class=""><a data-target="#users-user-tab7" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(7)}</a></li>
                    <li class=""><a data-target="#users-user-tab8" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(8)}</a></li>
                    <li class=""><a data-target="#users-user-tab9" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(9)}</a></li>
                    <li class=""><a data-target="#users-user-tab10" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(10)}</a></li>
                    <li class=""><a data-target="#users-user-tab11" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(11)}</a></li>
                    <li class=""><a data-target="#users-user-tab12" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(12)}</a></li>
                    <li class=""><a data-target="#users-user-tab13" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(13)}</a></li>
                </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none"/>
                        <div class="tab-pane active" id="users-user-tab0" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__name->getTitle()}&nbsp;&nbsp;{if $elem.__name->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__name->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__name->getRenderTemplate() field=$elem.__name}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__surname->getTitle()}&nbsp;&nbsp;{if $elem.__surname->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__surname->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__surname->getRenderTemplate() field=$elem.__surname}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__midname->getTitle()}&nbsp;&nbsp;{if $elem.__midname->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__midname->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__midname->getRenderTemplate() field=$elem.__midname}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__e_mail->getTitle()}&nbsp;&nbsp;{if $elem.__e_mail->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__e_mail->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__e_mail->getRenderTemplate() field=$elem.__e_mail}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__login->getTitle()}&nbsp;&nbsp;{if $elem.__login->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__login->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__login->getRenderTemplate() field=$elem.__login}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__openpass->getTitle()}&nbsp;&nbsp;{if $elem.__openpass->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__openpass->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__openpass->getRenderTemplate() field=$elem.__openpass}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__phone->getTitle()}&nbsp;&nbsp;{if $elem.__phone->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__phone->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__phone->getRenderTemplate() field=$elem.__phone}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__sex->getTitle()}&nbsp;&nbsp;{if $elem.__sex->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__sex->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__sex->getRenderTemplate() field=$elem.__sex}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__subscribe_on->getTitle()}&nbsp;&nbsp;{if $elem.__subscribe_on->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__subscribe_on->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__subscribe_on->getRenderTemplate() field=$elem.__subscribe_on}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__dateofreg->getTitle()}&nbsp;&nbsp;{if $elem.__dateofreg->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dateofreg->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__dateofreg->getRenderTemplate() field=$elem.__dateofreg}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__balance->getTitle()}&nbsp;&nbsp;{if $elem.__balance->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__balance->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__balance->getRenderTemplate() field=$elem.__balance}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__ban_expire->getTitle()}&nbsp;&nbsp;{if $elem.__ban_expire->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__ban_expire->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__ban_expire->getRenderTemplate() field=$elem.__ban_expire}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__last_visit->getTitle()}&nbsp;&nbsp;{if $elem.__last_visit->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__last_visit->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__last_visit->getRenderTemplate() field=$elem.__last_visit}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__last_ip->getTitle()}&nbsp;&nbsp;{if $elem.__last_ip->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__last_ip->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__last_ip->getRenderTemplate() field=$elem.__last_ip}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__registration_ip->getTitle()}&nbsp;&nbsp;{if $elem.__registration_ip->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__registration_ip->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__registration_ip->getRenderTemplate() field=$elem.__registration_ip}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__is_enable_two_factor->getTitle()}&nbsp;&nbsp;{if $elem.__is_enable_two_factor->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__is_enable_two_factor->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__is_enable_two_factor->getRenderTemplate() field=$elem.__is_enable_two_factor}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__manager_user_id->getTitle()}&nbsp;&nbsp;{if $elem.__manager_user_id->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__manager_user_id->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__manager_user_id->getRenderTemplate() field=$elem.__manager_user_id}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab1" role="tabpanel">
                                                                                                                                                                                                                                                                                                                <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__is_company->getTitle()}&nbsp;&nbsp;{if $elem.__is_company->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__is_company->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__is_company->getRenderTemplate() field=$elem.__is_company}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__company->getTitle()}&nbsp;&nbsp;{if $elem.__company->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__company->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__company->getRenderTemplate() field=$elem.__company}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__company_inn->getTitle()}&nbsp;&nbsp;{if $elem.__company_inn->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__company_inn->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__company_inn->getRenderTemplate() field=$elem.__company_inn}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab2" role="tabpanel">
                                                                                                            {include file=$elem.____groups__->getRenderTemplate() field=$elem.____groups__}
                                                                                                                                                                                                                    </div>
                        <div class="tab-pane" id="users-user-tab3" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__data->getTitle()}&nbsp;&nbsp;{if $elem.__data->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__data->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__data->getRenderTemplate() field=$elem.__data}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab4" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__desktop_notice_locks->getTitle()}&nbsp;&nbsp;{if $elem.__desktop_notice_locks->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__desktop_notice_locks->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__desktop_notice_locks->getRenderTemplate() field=$elem.__desktop_notice_locks}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab5" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__user_cost->getTitle()}&nbsp;&nbsp;{if $elem.__user_cost->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__user_cost->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__user_cost->getRenderTemplate() field=$elem.__user_cost}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab6" role="tabpanel">
                                                                                                            {include file=$elem.____interaction__->getRenderTemplate() field=$elem.____interaction__}
                                                                                                                                                </div>
                        <div class="tab-pane" id="users-user-tab7" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__allow_api_methods->getTitle()}&nbsp;&nbsp;{if $elem.__allow_api_methods->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__allow_api_methods->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__allow_api_methods->getRenderTemplate() field=$elem.__allow_api_methods}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab8" role="tabpanel">
                                                                                                                                                                                                                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__push_lock->getTitle()}&nbsp;&nbsp;{if $elem.__push_lock->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__push_lock->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__push_lock->getRenderTemplate() field=$elem.__push_lock}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__access->getTitle()}&nbsp;&nbsp;{if $elem.__access->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__access->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__access->getRenderTemplate() field=$elem.__access}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab9" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__photo->getTitle()}&nbsp;&nbsp;{if $elem.__photo->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__photo->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__photo->getRenderTemplate() field=$elem.__photo}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab10" role="tabpanel">
                                                                                                                                                                                                                                                                                                                <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__rating->getTitle()}&nbsp;&nbsp;{if $elem.__rating->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__rating->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__rating->getRenderTemplate() field=$elem.__rating}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__review_num->getTitle()}&nbsp;&nbsp;{if $elem.__review_num->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__review_num->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__review_num->getRenderTemplate() field=$elem.__review_num}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__balls->getTitle()}&nbsp;&nbsp;{if $elem.__balls->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__balls->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__balls->getRenderTemplate() field=$elem.__balls}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab11" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__admin_comment->getTitle()}&nbsp;&nbsp;{if $elem.__admin_comment->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__admin_comment->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__admin_comment->getRenderTemplate() field=$elem.__admin_comment}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab12" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__count_ads->getTitle()}&nbsp;&nbsp;{if $elem.__count_ads->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__count_ads->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__count_ads->getRenderTemplate() field=$elem.__count_ads}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="users-user-tab13" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__basket_min_limit->getTitle()}&nbsp;&nbsp;{if $elem.__basket_min_limit->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__basket_min_limit->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__basket_min_limit->getRenderTemplate() field=$elem.__basket_min_limit}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                    </form>
    </div>
    </div>