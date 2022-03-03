{$config=ConfigLoader::byModule('pushsender')}
<p>{t}Пункт меню{/t}</p>
{html_options name="mobile_menu_id" options=$config->getMenusList() selected=$elem.mobile_menu_id}