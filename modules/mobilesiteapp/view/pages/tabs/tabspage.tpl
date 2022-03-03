{$mobile_config=ConfigLoader::byModule('mobilesiteapp')}
{$catalog_config=ConfigLoader::byModule('catalog')}
{$external_config=ConfigLoader::byModule('externalapi')}
{$page_urls=[
'product' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'product.get']),
'offers' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'product.getofferslist']),
'recommended'  => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'product.getrecommendedlist']),
'reserve'  => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'product.reserve']),

'brands_list' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'brand.getlist']),
'brand' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'brand.get']),

'slideshow'  => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'banner.getlist']),

'category'  => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'category.getlist']),
'category_products'  => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'product.getlist']),

'favorite'  => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'favorite.getList']),
'favorite_add' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'favorite.add']),
'favorite_remove' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'favorite.remove']),

'cart' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'cart.getcartdata']),
'cart_add' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'cart.add']),
'cart_remove' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'cart.remove']),
'cart_update' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'cart.update']),
'cart_clear' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'cart.clear']),
'order_repeat' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'cart.repeatorder']),

'oneclick_url_send_cart' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'cart.oneclickcartsend']),
'oneclick_url_send_product' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'product.oneclicksend']),
'oneclick_url_fields' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'cart.oneclickcartfields']),

'user_get_addresses' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'user.getaddresses']),
'user_update_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'user.update']),
'user_get_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'user.get']),
'user_email_recovery' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'user.emailrecovery']),

'order_pickup_points' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'checkout.getorderpickuppoints']),
'checkout_address' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'checkout.address']),
'checkout_delivery_payment' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'checkout.deliverypayment']),
'checkout_confirm' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'checkout.confirm']),
'checkout_init' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'checkout.init']),
'checkout_cartdata' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'checkout.getcartdata']),
'checkout_address_lists_info' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'checkout.getaddresslistsinfo']),
'checkout_online_pay_url' => $router->getUrl('shop-front-onlinepay', ['Act'=>'doPay']),

'delivery_getlist' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'delivery.getlist']),
'payment_getlist' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'payment.getlist']),

'auth_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'oauth.token']),

'extends_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'mobilesiteapp.getextendsjson']),

'menu_list_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'menu.getlist']),
'menu_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'menu.get']),

'article_category_list_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'article.getcategorylist']),
'article_list_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'article.getlist']),
'article_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'article.get']),

'push_change_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'push.change']),
'push_list_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'push.getlist']),
'push_get_token_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'push.registerToken']),

'order_get_list' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'order.getlist']),
'order_get' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'order.get']),

'mobilesiteapp_config' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'mobilesiteapp.config']),

'set_affiliate_url' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'affiliate.set']),

'multi_request' => $this_controller->getUrl('externalapi-front-apigate', ['method'=>'multirequest.run'])
]}
{hook name="mobilesiteapp-tabs:tabs" title="{t}Мобильное приложение - нижние вкладки: нижние вкладки{/t}"}
<tabs-blocks-tabs [favorite]="{ favorite_url: '{$page_urls.favorite}',
                  favorite_url_add: '{$page_urls.favorite_add}',
                  favorite_url_remove: '{$page_urls.favorite_remove}'}"
                  [affiliate]="{ set_affiliate_url: '{$page_urls.set_affiliate_url}'}"
                  [banners]="{ slideshow_url: '{$page_urls.slideshow}'}"
                  [brands]="{ list_url: '{$page_urls.brands_list}',
                  url: '{$page_urls.brand}'}"
                  [category]="{ products_url: '{$page_urls.category_products}',
                  category_url: '{$page_urls.category}',
                  category_list_default_order: '{$catalog_config.list_default_order}',
                  category_list_default_order_direction: '{$catalog_config.list_default_order_direction}'}"
                  [product]="{ offers_url: '{$page_urls.offers}',
                  recommended_url: '{$page_urls.recommended}',
                  product_url: '{$page_urls.product}',
                  reserve_url: '{$page_urls.reserve}'}"
                  [cart]="{ cart_url: '{$page_urls.cart}',
                  cart_url_add: '{$page_urls.cart_add}',
                  cart_url_remove: '{$page_urls.cart_remove}',
                  cart_url_update: '{$page_urls.cart_update}',
                  cart_url_clear: '{$page_urls.cart_clear}',
                  oneclick_url_send_cart: '{$page_urls.oneclick_url_send_cart}',
                  oneclick_url_send_product: '{$page_urls.oneclick_url_send_product}',
                  oneclick_url_fields: '{$page_urls.oneclick_url_fields}'}"
                  [checkout]="{ order_pickup_points: '{$page_urls.order_pickup_points}',
                  checkout_init: '{$page_urls.checkout_init}',
                  checkout_address: '{$page_urls.checkout_address}',
                  checkout_delivery_payment: '{$page_urls.checkout_delivery_payment}',
                  checkout_address_lists_info: '{$page_urls.checkout_address_lists_info}',
                  checkout_cartdata: '{$page_urls.checkout_cartdata}',
                  checkout_confirm: '{$page_urls.checkout_confirm}',
                  checkout_online_pay_url: '{$page_urls.checkout_online_pay_url}'}"
                  [delivery]="{ delivery_getlist: '{$page_urls.delivery_getlist}'}"
                  [payment]="{ payment_getlist: '{$page_urls.payment_getlist}'}"
                  [mobilesiteapp]="{ url: '{$page_urls.mobilesiteapp_config}',
                  products_pagesize: '{$mobile_config.products_pagesize}',
                  mobile_phone: '{$mobile_config.mobile_phone}'}"
                  [auth]="{ auth_url: '{$page_urls.auth_url}'}"
                  [menu]="{ list_url: '{$page_urls.menu_list_url}',
                  url: '{$page_urls.menu_url}'
                  }"
                  {if $client_version >=1.1}
                  [article]="{ category_list_url: '{$page_urls.article_category_list_url}',
                  list_url: '{$page_urls.article_list_url}',
                  url: '{$page_urls.article_url}'
                  }"
                  {/if}

                  {if $client_version >1.2}
                  [is_shop_enabled]="{$external_config->isShopModuleEnabled()}"
                  {/if}

                  [settings]="{ push_change_url: '{$page_urls.push_change_url}',
                  push_get_token_url: '{$page_urls.push_get_token_url}',
                  push_list_url: '{$page_urls.push_list_url}'}"
                  [extend]="{ extends_url: '{$page_urls.extends_url}'}"
                  [order]="{ order_get_list_url: '{$page_urls.order_get_list}',
                  order_get_url: '{$page_urls.order_get}'}"
                  [multi_request]="{ multi_request: '{$page_urls.multi_request}'}"
                  [user]="{ get_addresses_url: '{$page_urls.user_get_addresses}',
                  repeat_order: '{$page_urls.order_repeat}',
                  user_get_url: '{$page_urls.user_get_url}',
                  user_email_recovery: '{$page_urls.user_email_recovery}',
                  user_update_url: '{$page_urls.user_update_url}'}"></tabs-blocks-tabs>
{/hook}
