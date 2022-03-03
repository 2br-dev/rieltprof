<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; Charset=utf-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >
</head>
<body>
    <style>
        #table-wrapper {
            line-height:150%;
        }

        h1,h2,h3,h4,h5,h6 {
            line-height:normal;
        }

        @media (max-width:500px) {
            #table-wrapper {
                font-size: 12px;
            }
            table {
                font-size:inherit;
            }
        }
    </style>
    <table border="0" cellspacing="0" cellpadding="0" bgcolor="#eeeeee" style="font-family:Arial, sans-serif; border-collapse: collapse; width: 100%; height: 100%; line-height:150%; font-size:14px;" id="table-wrapper">
        <tbody>
        <tr>
            <td>
                <div style="padding:0px 15px 15px;">
                    <table align="center" border="0" cellspacing="0" cellpadding="0" style="max-width: 640px; padding:40px 0;">
                    <tbody>
                    <tr>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%" style="min-width:400px; border-collapse:collapse;margin-bottom: 15px;">
                                <tr>
                                    <td width="40"></td>
                                    <td>
                                        <a style="display: inline-block;" href="{$SITE->getRootUrl(true)}" target="_blank">
                                            <img src="{$CONFIG->__logo->getUrl(400, 50, 'xy', true)}" alt="" style="border: none;">
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%" style="min-width:400px; padding:40px; background: #fff;">
                                <tbody>
                                <tr>
                                    <td>
                                        {block name="content"}{/block}
                                        <p>
                                            {t}С наилучшими пожеланиями{/t},<br />
                                            <a href="{$SITE->getRootUrl(true)}" target="_blank">
                                                {if $CONFIG.firm_name_for_notice}{$CONFIG.firm_name_for_notice}{else}{$SITE->getMainDomain()}{/if}
                                            </a>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="display: block; text-align: center; padding-bottom: 15px">
                                        <img src="//spacergif.org/spacer.gif" width="100%" height="1" style="background: #eeeeee;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="display: block; text-align: center; line-height: 10px; padding: 15px 0">
                                        {if $CONFIG.facebook_group}
                                            <a style="display: inline-block; padding: 0 5px;" href="{$CONFIG.facebook_group}" target="_blank">
                                                <img src="{$SITE->getRootUrl(true)}/modules/alerts/view/img/facebook.png" width="32" style="border: none;"/>
                                            </a>
                                        {/if}
                                        {if $CONFIG.twitter_group}
                                            <a style="display: inline-block; padding: 0 5px;" href="{$CONFIG.twitter_group}" target="_blank">
                                                <img src="{$SITE->getRootUrl(true)}/modules/alerts/view/img/twitter.png" width="32" style="border: none;"/>
                                            </a>
                                        {/if}
                                        {if $CONFIG.instagram_group}
                                            <a style="display: inline-block; padding: 0 5px;" href="{$CONFIG.instagram_group}" target="_blank">
                                                <img src="{$SITE->getRootUrl(true)}/modules/alerts/view/img/instagram.png" width="32" style="border: none;"/>
                                            </a>
                                        {/if}
                                        {if $CONFIG.vkontakte_group}
                                            <a style="display: inline-block; padding: 0 5px;" href="{$CONFIG.vkontakte_group}" target="_blank">
                                                <img src="{$SITE->getRootUrl(true)}/modules/alerts/view/img/vk.png" width="32" style="border: none;"/>
                                            </a>
                                        {/if}
                                        {if $CONFIG.youtube_group}
                                            <a style="display: inline-block; padding: 0 5px;" href="{$CONFIG.youtube_group}" target="_blank">
                                                <img src="{$SITE->getRootUrl(true)}/modules/alerts/view/img/youtube.png" width="32" style="border: none;"/>
                                            </a>
                                        {/if}
                                        {if $CONFIG.viber_group}
                                            <a style="display: inline-block; padding: 0 5px;" href="{$CONFIG.viber_group}" target="_blank">
                                                <img src="{$SITE->getRootUrl(true)}/modules/alerts/view/img/viber.png" width="32" style="border: none;"/>
                                            </a>
                                        {/if}
                                        {if $CONFIG.telegram_group}
                                            <a style="display: inline-block; padding: 0 5px;" href="{$CONFIG.telegram_group}" target="_blank">
                                                <img src="{$SITE->getRootUrl(true)}/modules/alerts/view/img/telegram.png" width="32" style="border: none;"/>
                                            </a>
                                        {/if}
                                        {if $CONFIG.whatsapp_group}
                                            <a style="display: inline-block; padding: 0 5px;" href="{$CONFIG.whatsapp_group}" target="_blank">
                                                <img src="{$SITE->getRootUrl(true)}/modules/alerts/view/img/whatsapp.png" width="32" style="border: none;"/>
                                            </a>
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="display: block; text-align: center;">
                                        <p style="font-size: 70%; color: #B3B3B3; margin: 0; font-family: Tahoma;">{t}Это автоматическая рассылка, на это письмо отвечать нет необходимости.{/t}</p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</body>
</html>