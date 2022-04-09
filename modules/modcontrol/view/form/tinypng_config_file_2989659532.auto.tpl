<div class="formbox" >
    {if $elem._before_form_template}{include file=$elem._before_form_template}{/if}

                        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
            <input type="submit" value="" style="display:none">
            <div class="notabs">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                                    <table class="otable">
                                                                                    
                                    <tr>
                                    <td class="otitle">{$elem.__name->getTitle()}&nbsp;&nbsp;{if $elem.__name->getHint() != ''}<a class="help-icon" title="{$elem.__name->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__name->getRenderTemplate() field=$elem.__name}</td>
                                    </tr>
                                                            
                                    <tr>
                                    <td class="otitle">{$elem.__description->getTitle()}&nbsp;&nbsp;{if $elem.__description->getHint() != ''}<a class="help-icon" title="{$elem.__description->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__description->getRenderTemplate() field=$elem.__description}</td>
                                    </tr>
                                                            
                                    <tr>
                                    <td class="otitle">{$elem.__version->getTitle()}&nbsp;&nbsp;{if $elem.__version->getHint() != ''}<a class="help-icon" title="{$elem.__version->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__version->getRenderTemplate() field=$elem.__version}</td>
                                    </tr>
                                                            
                                    <tr>
                                    <td class="otitle">{$elem.__core_version->getTitle()}&nbsp;&nbsp;{if $elem.__core_version->getHint() != ''}<a class="help-icon" title="{$elem.__core_version->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__core_version->getRenderTemplate() field=$elem.__core_version}</td>
                                    </tr>
                                                            
                                    <tr>
                                    <td class="otitle">{$elem.__author->getTitle()}&nbsp;&nbsp;{if $elem.__author->getHint() != ''}<a class="help-icon" title="{$elem.__author->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__author->getRenderTemplate() field=$elem.__author}</td>
                                    </tr>
                                                            
                                    <tr>
                                    <td class="otitle">{$elem.__enabled->getTitle()}&nbsp;&nbsp;{if $elem.__enabled->getHint() != ''}<a class="help-icon" title="{$elem.__enabled->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__enabled->getRenderTemplate() field=$elem.__enabled}</td>
                                    </tr>
                                                            
                                    <tr>
                                    <td class="otitle">{$elem.__portion_count->getTitle()}&nbsp;&nbsp;{if $elem.__portion_count->getHint() != ''}<a class="help-icon" title="{$elem.__portion_count->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__portion_count->getRenderTemplate() field=$elem.__portion_count}</td>
                                    </tr>
                                                            
                                    <tr>
                                    <td class="otitle">{$elem.__photo_count_in_stack->getTitle()}&nbsp;&nbsp;{if $elem.__photo_count_in_stack->getHint() != ''}<a class="help-icon" title="{$elem.__photo_count_in_stack->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__photo_count_in_stack->getRenderTemplate() field=$elem.__photo_count_in_stack}</td>
                                    </tr>
                                                            
                                    <tr>
                                    <td class="otitle">{$elem.__quality->getTitle()}&nbsp;&nbsp;{if $elem.__quality->getHint() != ''}<a class="help-icon" title="{$elem.__quality->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__quality->getRenderTemplate() field=$elem.__quality}</td>
                                    </tr>
                                                                        </table>
                            </div>
        </form>
    </div>