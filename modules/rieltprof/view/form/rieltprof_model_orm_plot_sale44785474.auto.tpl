
<div class="formbox" >
            
    <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                    <li class=" active"><a data-target="#rieltprof-plot-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                    <li class=""><a data-target="#rieltprof-plot-tab6" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(6)}</a></li>
                    <li class=""><a data-target="#rieltprof-plot-tab7" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(7)}</a></li>
                    <li class=""><a data-target="#rieltprof-plot-tab8" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(8)}</a></li>
                    <li class=""><a data-target="#rieltprof-plot-tab9" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(9)}</a></li>
        
        </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none"/>
                        <div class="tab-pane active" id="rieltprof-plot-tab0" role="tabpanel">
                                                                                                                                    {include file=$elem.__id->getRenderTemplate() field=$elem.__id}
                                                                                                                                                                                            {include file=$elem.___tmpid->getRenderTemplate() field=$elem.___tmpid}
                                                                                                                                                                                                                                                                                                                            
                                            <table class="otable">
                                                                                                                                                        
                                <tr>
                                    <td class="otitle">{$elem.__dateof->getTitle()}&nbsp;&nbsp;{if $elem.__dateof->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dateof->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__dateof->getRenderTemplate() field=$elem.__dateof}</td>
                                </tr>
                                
                                                                                                                                                                                        
                                <tr>
                                    <td class="otitle">{$elem.__actual_on_date->getTitle()}&nbsp;&nbsp;{if $elem.__actual_on_date->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__actual_on_date->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__actual_on_date->getRenderTemplate() field=$elem.__actual_on_date}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__cost_product->getTitle()}&nbsp;&nbsp;{if $elem.__cost_product->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__cost_product->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__cost_product->getRenderTemplate() field=$elem.__cost_product}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__note->getTitle()}&nbsp;&nbsp;{if $elem.__note->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__note->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__note->getRenderTemplate() field=$elem.__note}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__personal_note->getTitle()}&nbsp;&nbsp;{if $elem.__personal_note->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__personal_note->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__personal_note->getRenderTemplate() field=$elem.__personal_note}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="rieltprof-plot-tab6" role="tabpanel">
                                                                                                            {include file=$elem.___photo_->getRenderTemplate() field=$elem.___photo_}
                                                                                                
                                                </div>
                        <div class="tab-pane" id="rieltprof-plot-tab7" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                            
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__county->getTitle()}&nbsp;&nbsp;{if $elem.__county->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__county->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__county->getRenderTemplate() field=$elem.__county}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__district->getTitle()}&nbsp;&nbsp;{if $elem.__district->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__district->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__district->getRenderTemplate() field=$elem.__district}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__street->getTitle()}&nbsp;&nbsp;{if $elem.__street->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__street->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__street->getRenderTemplate() field=$elem.__street}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__house->getTitle()}&nbsp;&nbsp;{if $elem.__house->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__house->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__house->getRenderTemplate() field=$elem.__house}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__liter->getTitle()}&nbsp;&nbsp;{if $elem.__liter->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__liter->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__liter->getRenderTemplate() field=$elem.__liter}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="rieltprof-plot-tab8" role="tabpanel">
                                                                                                                                                                                                                                                                    
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__quickly->getTitle()}&nbsp;&nbsp;{if $elem.__quickly->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__quickly->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__quickly->getRenderTemplate() field=$elem.__quickly}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__mark->getTitle()}&nbsp;&nbsp;{if $elem.__mark->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__mark->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__mark->getRenderTemplate() field=$elem.__mark}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__exclusive->getTitle()}&nbsp;&nbsp;{if $elem.__exclusive->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__exclusive->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__exclusive->getRenderTemplate() field=$elem.__exclusive}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="rieltprof-plot-tab9" role="tabpanel">
                                                                                                                            
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__land_area->getTitle()}&nbsp;&nbsp;{if $elem.__land_area->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__land_area->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__land_area->getRenderTemplate() field=$elem.__land_area}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
            
        </form>
    </div>
    </div>