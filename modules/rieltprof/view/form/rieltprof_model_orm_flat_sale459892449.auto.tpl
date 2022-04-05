
<div class="formbox" >
            
    <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                    <li class=" active"><a data-target="#rieltprof-flat-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                    <li class=""><a data-target="#rieltprof-flat-tab6" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(6)}</a></li>
                    <li class=""><a data-target="#rieltprof-flat-tab7" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(7)}</a></li>
                    <li class=""><a data-target="#rieltprof-flat-tab8" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(8)}</a></li>
                    <li class=""><a data-target="#rieltprof-flat-tab9" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(9)}</a></li>
        
        </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none"/>
                        <div class="tab-pane active" id="rieltprof-flat-tab0" role="tabpanel">
                                                                                                                                    {include file=$elem.__id->getRenderTemplate() field=$elem.__id}
                                                                                                                        {include file=$elem.___tmpid->getRenderTemplate() field=$elem.___tmpid}
                                                                                                                                                                                                                                                        
                                            <table class="otable">
                                                                                                                                                                                                                    
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
                        <div class="tab-pane" id="rieltprof-flat-tab6" role="tabpanel">
                                                                                                            {include file=$elem.___photo_->getRenderTemplate() field=$elem.___photo_}
                                                                                                
                                                </div>
                        <div class="tab-pane" id="rieltprof-flat-tab7" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                            
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
                        <div class="tab-pane" id="rieltprof-flat-tab8" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__rooms_list->getTitle()}&nbsp;&nbsp;{if $elem.__rooms_list->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__rooms_list->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__rooms_list->getRenderTemplate() field=$elem.__rooms_list}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__rooms_isolated->getTitle()}&nbsp;&nbsp;{if $elem.__rooms_isolated->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__rooms_isolated->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__rooms_isolated->getRenderTemplate() field=$elem.__rooms_isolated}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__split_wc->getTitle()}&nbsp;&nbsp;{if $elem.__split_wc->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__split_wc->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__split_wc->getRenderTemplate() field=$elem.__split_wc}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__material->getTitle()}&nbsp;&nbsp;{if $elem.__material->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__material->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__material->getRenderTemplate() field=$elem.__material}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__year->getTitle()}&nbsp;&nbsp;{if $elem.__year->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__year->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__year->getRenderTemplate() field=$elem.__year}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__state->getTitle()}&nbsp;&nbsp;{if $elem.__state->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__state->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__state->getRenderTemplate() field=$elem.__state}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__square->getTitle()}&nbsp;&nbsp;{if $elem.__square->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__square->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__square->getRenderTemplate() field=$elem.__square}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__square_kitchen->getTitle()}&nbsp;&nbsp;{if $elem.__square_kitchen->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__square_kitchen->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__square_kitchen->getRenderTemplate() field=$elem.__square_kitchen}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__square_living->getTitle()}&nbsp;&nbsp;{if $elem.__square_living->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__square_living->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__square_living->getRenderTemplate() field=$elem.__square_living}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__flat->getTitle()}&nbsp;&nbsp;{if $elem.__flat->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__flat->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__flat->getRenderTemplate() field=$elem.__flat}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__flat_house->getTitle()}&nbsp;&nbsp;{if $elem.__flat_house->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__flat_house->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__flat_house->getRenderTemplate() field=$elem.__flat_house}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
                        <div class="tab-pane" id="rieltprof-flat-tab9" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
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
                                    <td class="otitle">{$elem.__only_cash->getTitle()}&nbsp;&nbsp;{if $elem.__only_cash->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__only_cash->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__only_cash->getRenderTemplate() field=$elem.__only_cash}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__breakdown->getTitle()}&nbsp;&nbsp;{if $elem.__breakdown->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__breakdown->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__breakdown->getRenderTemplate() field=$elem.__breakdown}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__encumbrance->getTitle()}&nbsp;&nbsp;{if $elem.__encumbrance->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__encumbrance->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__encumbrance->getRenderTemplate() field=$elem.__encumbrance}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__child->getTitle()}&nbsp;&nbsp;{if $elem.__child->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__child->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__child->getRenderTemplate() field=$elem.__child}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__remodeling->getTitle()}&nbsp;&nbsp;{if $elem.__remodeling->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__remodeling->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__remodeling->getRenderTemplate() field=$elem.__remodeling}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__exclusive->getTitle()}&nbsp;&nbsp;{if $elem.__exclusive->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__exclusive->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__exclusive->getRenderTemplate() field=$elem.__exclusive}</td>
                                </tr>
                                
                                                            
                        </table>
                                                </div>
            
        </form>
    </div>
    </div>