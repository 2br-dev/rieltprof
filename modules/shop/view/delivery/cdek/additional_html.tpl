{$address=$order->getAddress()}
<input type="hidden" name="delivery_extra[value]" value='{ "tariffId":"{$extra_info.tariffId}", "zipcode":"{$address.zipcode}"}' disabled="disabled"/>
