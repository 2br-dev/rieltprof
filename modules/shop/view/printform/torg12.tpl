{$user = $order->getUser()}
{$address = $order->getAddress()}
{$cart = $order->getCart()}
{$order_data = $cart->getOrderData(false, false)}

<!DOCTYPE HTML>
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <TITLE>ТОВАРНАЯ НАКЛАДНАЯ</TITLE>
    <STYLE type="text/css">
        .header td{
            border: #000000 1px;
            text-align: center;
            padding: 5px;
            border-style: solid solid none none;
        }
        .products{
            border-bottom: 1px solid black;
        }
        .border-right{
            border-right: #000000 1px solid
        }
        .border-bottom{
            border-bottom: #000000 1px solid
        }
        .border-bottom td {
            border-bottom: #000000 1px solid !important;
        }
        .center{
            text-align: center!important;
        }
        .right{
            text-align: right!important;
        }
        @media print {
            .page-break { page-break-after: always;}
        }
        {literal}
        body {margin-top: 0px;margin-left: 0px;}

        #page_1 {position:relative; overflow: hidden;margin: 5px 0px 20px 38px;padding: 0px;border: none;width: 1049px;}
        #page_1 #id_1 {border:none;margin: 0px 0px 0px 0px;padding: 0px;border:none;width: 1049px;overflow: hidden;}
        #page_1 #id_2 {border:none;margin: 1px 0px 0px 489px;padding: 0px;border:none;width: 366px;overflow: hidden;}

        #page_1 #p1dimg1 {position:absolute;top:451px;left:529px;z-index:-1;width:519px;height:242px;}
        #page_1 #p1dimg1 #p1img1 {width:519px;height:242px;}

        #page_2 {position:relative; overflow: hidden;margin: 20px 0px 189px 38px;padding: 0px;border: none;width: 1048px;}

        #page_2 #p2dimg1 {position:absolute;top:96px;left:529px;z-index:-1;width:519px;height:19px;}
        #page_2 #p2dimg1 #p2img1 {width:519px;height:19px;}

        .ft0{font: 8px 'Verdana';line-height: 10px;}
        .ft1{font: 1px 'Verdana';line-height: 1px;}
        .ft2{font: 11px 'Verdana';line-height: 13px;}
        .ft3{font: 1px 'Verdana';line-height: 4px;}
        .ft4{font: 1px 'Verdana';line-height: 3px;}
        .ft5{font: 9px 'Verdana';line-height: 12px;}
        .ft6{font: 1px 'Verdana';line-height: 10px;}
        .ft7{font: 7px 'Verdana';line-height: 8px;}
        .ft8{font: 10px 'Verdana';line-height: 12px;}
        .ft9{font: 1px 'Verdana';line-height: 5px;}
        .ft10{font: 1px 'Verdana';line-height: 11px;}
        .ft11{font: 1px 'Verdana';line-height: 9px;}
        .ft12{font: 1px 'Verdana';line-height: 6px;}
        .ft13{font: bold 16px 'Arial';line-height: 19px;}
        .ft17{font: 1px 'Verdana';line-height: 7px;}
        .ft19{font: 1px 'Verdana';line-height: 8px;}
        .ft20{font: 1px 'Verdana';line-height: 2px;}

        .p0{text-align: left;padding-left: 755px;padding-right: 3px;margin-top: 0px;margin-bottom: 0px;text-indent: 131px;}
        .p1{text-align: left;margin-top: 0px;margin-bottom: 0px;}
        .p2{text-align: left;padding-left: 27px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p3{text-align: right;padding-right: 10px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p4{text-align: right;padding-right: 19px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p5{text-align: left;padding-left: 6px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p6{text-align: center;padding-right: 2px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p7{text-align: center;padding-right: 22px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p8{text-align: right;padding-right: 11px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p9{text-align: left;padding-left: 4px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p10{text-align: center;padding-right: 30px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p11{text-align: right;padding-right: 9px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p12{text-align: center;padding-left: 3px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p13{text-align: left;padding-left: 17px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p14{text-align: left;padding-left: 8px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p15{text-align: left;padding-left: 7px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p45{text-align: left;padding-left: 14px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p46{text-align: left;padding-left: 16px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p47{text-align: left;padding-left: 18px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p48{text-align: left;padding-left: 15px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p49{text-align: left;padding-left: 5px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p50{text-align: right;padding-right: 3px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p51{text-align: left;padding-left: 47px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p52{text-align: right;padding-right: 43px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p53{text-align: left;padding-left: 25px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p54{text-align: left;padding-left: 39px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p55{text-align: left;padding-left: 33px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p56{text-align: right;padding-right: 44px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p57{text-align: left;padding-left: 65px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p58{text-align: right;padding-right: 22px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p59{text-align: left;padding-left: 3px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p60{text-align: left;padding-left: 46px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}

        .td0{padding: 0px;margin: 0px;width: 38px;vertical-align: bottom;}
        .td1{padding: 0px;margin: 0px;width: 61px;vertical-align: bottom;}
        .td2{padding: 0px;margin: 0px;width: 171px;vertical-align: bottom;}
        .td3{padding: 0px;margin: 0px;width: 99px;vertical-align: bottom;}
        .td4{padding: 0px;margin: 0px;width: 27px;vertical-align: bottom;}
        .td5{padding: 0px;margin: 0px;width: 35px;vertical-align: bottom;}
        .td6{padding: 0px;margin: 0px;width: 33px;vertical-align: bottom;}
        .td7{padding: 0px;margin: 0px;width: 30px;vertical-align: bottom;}
        .td8{padding: 0px;margin: 0px;width: 4px;vertical-align: bottom;}
        .td9{padding: 0px;margin: 0px;width: 32px;vertical-align: bottom;}
        .td10{padding: 0px;margin: 0px;width: 34px;vertical-align: bottom;}
        .td11{padding: 0px;margin: 0px;width: 10px;vertical-align: bottom;}
        .td12{padding: 0px;margin: 0px;width: 50px;vertical-align: bottom;}
        .td13{padding: 0px;margin: 0px;width: 70px;vertical-align: bottom;}
        .td14{padding: 0px;margin: 0px;width: 15px;vertical-align: bottom;}
        .td15{padding: 0px;margin: 0px;width: 85px;vertical-align: bottom;}
        .td16{padding: 0px;margin: 0px;width: 43px;vertical-align: bottom;}
        .td17{padding: 0px;margin: 0px;width: 7px;vertical-align: bottom;}
        .td18{padding: 0px;margin: 0px;width: 24px;vertical-align: bottom;}
        .td19{padding: 0px;margin: 0px;width: 5px;vertical-align: bottom;}
        .td20{padding: 0px;margin: 0px;width: 56px;vertical-align: bottom;}
        .td21{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 5px;vertical-align: bottom;}
        .td22{border-right: #000000 1px solid;border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 80px;vertical-align: bottom;}
        .td23{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 80px;vertical-align: bottom;}
        .td24{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 97px;vertical-align: bottom;}
        .td25{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 80px;vertical-align: bottom;}
        .td26{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 61px;vertical-align: bottom;}
        .td27{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 38px;vertical-align: bottom;}
        .td28{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 61px;vertical-align: bottom;}
        .td29{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 171px;vertical-align: bottom;}
        .td30{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 99px;vertical-align: bottom;}
        .td31{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 27px;vertical-align: bottom;}
        .td32{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 35px;vertical-align: bottom;}
        .td33{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 33px;vertical-align: bottom;}
        .td34{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 30px;vertical-align: bottom;}
        .td35{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 4px;vertical-align: bottom;}
        .td36{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 32px;vertical-align: bottom;}
        .td37{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 34px;vertical-align: bottom;}
        .td38{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 10px;vertical-align: bottom;}
        .td39{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 50px;vertical-align: bottom;}
        .td40{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 70px;vertical-align: bottom;}
        .td41{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 15px;vertical-align: bottom;}
        .td42{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 85px;vertical-align: bottom;}
        .td43{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 43px;vertical-align: bottom;}
        .td44{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 7px;vertical-align: bottom;}
        .td45{padding: 0px;margin: 0px;width: 338px;vertical-align: bottom;}
        .td47{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 5px;vertical-align: bottom;}
        .td48{padding: 0px;margin: 0px;width: 161px;vertical-align: bottom;}
        .td49{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 225px;vertical-align: bottom;}
        .td50{padding: 0px;margin: 0px;width: 289px;vertical-align: bottom;}
        .td51{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 5px;vertical-align: bottom;}
        .td52{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 56px;vertical-align: bottom;}
        .td53{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 23px;vertical-align: bottom;}
        .td54{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 23px;vertical-align: bottom;}
        .td55{padding: 0px;margin: 0px;width: 133px;vertical-align: bottom;}
        .td56{padding: 0px;margin: 0px;width: 224px;vertical-align: bottom;}
        .td57{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 3px;vertical-align: bottom;}
        .td58{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 109px;vertical-align: bottom;}
        .td59{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 119px;vertical-align: bottom;}
        .td60{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 158px;vertical-align: bottom;}
        .td61{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 9px;vertical-align: bottom;}
        .td62{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 69px;vertical-align: bottom;}
        .td65{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 90px;vertical-align: bottom;}
        .td78{padding: 0px;margin: 0px;width: 29px;vertical-align: bottom;}
        .td106{border-right: #000000 1px solid;border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 26px;vertical-align: bottom;}
        .td107{border-right: #000000 1px solid;border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 34px;vertical-align: bottom;}
        .td108{border-right: #000000 1px solid;border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 32px;vertical-align: bottom;}
        .td109{border-right: #000000 1px solid;border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 29px;vertical-align: bottom;}
        .td112{border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 34px;vertical-align: bottom;}
        .td114{border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 50px;vertical-align: bottom;}
        .td117{border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 85px;vertical-align: bottom;}
        .td128{padding: 0px;margin: 0px;width: 0px;vertical-align: bottom;}
        .td138{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 50px;vertical-align: bottom;}
        .td146{border-left: #000000 1px solid;border-right: #000000 1px solid;border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 37px;vertical-align: bottom;}
        .td147{border-right: #000000 1px solid;border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 329px;vertical-align: bottom;}
        .td148{border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 36px;vertical-align: bottom;}
        .td149{border-bottom: #808080 1px solid;padding: 0px;margin: 0px;width: 60px;vertical-align: bottom;}
        .td152{padding: 0px;margin: 0px;width: 89px;vertical-align: bottom;}
        .td153{padding: 0px;margin: 0px;width: 48px;vertical-align: bottom;}
        .td154{padding: 0px;margin: 0px;width: 12px;vertical-align: bottom;}
        .td155{padding: 0px;margin: 0px;width: 37px;vertical-align: bottom;}
        .td156{padding: 0px;margin: 0px;width: 74px;vertical-align: bottom;}
        .td158{padding: 0px;margin: 0px;width: 57px;vertical-align: bottom;}
        .td159{padding: 0px;margin: 0px;width: 88px;vertical-align: bottom;}
        .td160{padding: 0px;margin: 0px;width: 39px;vertical-align: bottom;}
        .td162{padding: 0px;margin: 0px;width: 13px;vertical-align: bottom;}
        .td163{padding: 0px;margin: 0px;width: 40px;vertical-align: bottom;}
        .td164{padding: 0px;margin: 0px;width: 47px;vertical-align: bottom;}
        .td165{padding: 0px;margin: 0px;width: 28px;vertical-align: bottom;}
        .td170{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 57px;vertical-align: bottom;}
        .td171{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 48px;vertical-align: bottom;}
        .td172{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 40px;vertical-align: bottom;}
        .td173{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 39px;vertical-align: bottom;}
        .td175{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 98px;vertical-align: bottom;}
        .td182{padding: 0px;margin: 0px;width: 991px;vertical-align: bottom;}
        .td183{padding: 0px;margin: 0px;width: 129px;vertical-align: bottom;}
        .td184{padding: 0px;margin: 0px;width: 19px;vertical-align: bottom;}
        .td185{padding: 0px;margin: 0px;width: 98px;vertical-align: bottom;}
        .td186{padding: 0px;margin: 0px;width: 1px;vertical-align: bottom;}
        .td187{padding: 0px;margin: 0px;width: 2px;vertical-align: bottom;}
        .td188{padding: 0px;margin: 0px;width: 14px;vertical-align: bottom;}
        .td189{padding: 0px;margin: 0px;width: 172px;vertical-align: bottom;}
        .td190{padding: 0px;margin: 0px;width: 254px;vertical-align: bottom;}
        .td191{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 48px;vertical-align: bottom;}
        .td192{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 35px;vertical-align: bottom;}
        .td193{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 61px;vertical-align: bottom;}
        .td194{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 99px;vertical-align: bottom;}
        .td195{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 7px;vertical-align: bottom;}
        .td196{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 74px;vertical-align: bottom;}
        .td197{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 56px;vertical-align: bottom;}
        .td198{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 57px;vertical-align: bottom;}
        .td199{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 88px;vertical-align: bottom;}
        .td200{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 39px;vertical-align: bottom;}
        .td201{padding: 0px;margin: 0px;width: 295px;vertical-align: bottom;}
        .td202{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 37px;vertical-align: bottom;}
        .td203{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 74px;vertical-align: bottom;}
        .td204{padding: 0px;margin: 0px;width: 76px;vertical-align: bottom;}
        .td205{padding: 0px;margin: 0px;width: 125px;vertical-align: bottom;}
        .td206{padding: 0px;margin: 0px;width: 53px;vertical-align: bottom;}
        .td207{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 2px;vertical-align: bottom;background: #000000;}
        .td208{padding: 0px;margin: 0px;width: 111px;vertical-align: bottom;}
        .td209{padding: 0px;margin: 0px;width: 2px;vertical-align: bottom;background: #000000;}
        .td210{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 2px;vertical-align: bottom;background: #000000;}
        .td211{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 324px;vertical-align: bottom;}
        .td212{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 13px;vertical-align: bottom;}
        .td213{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 12px;vertical-align: bottom;}
        .td214{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 80px;vertical-align: bottom;}
        .td215{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 158px;vertical-align: bottom;}
        .td216{padding: 0px;margin: 0px;width: 233px;vertical-align: bottom;}
        .td217{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 40px;vertical-align: bottom;}
        .td218{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 19px;vertical-align: bottom;}
        .td219{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 98px;vertical-align: bottom;}
        .td220{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 30px;vertical-align: bottom;}
        .td221{padding: 0px;margin: 0px;width: 213px;vertical-align: bottom;}
        .td222{padding: 0px;margin: 0px;width: 281px;vertical-align: bottom;}
        .td223{padding: 0px;margin: 0px;width: 113px;vertical-align: bottom;}
        .td224{padding: 0px;margin: 0px;width: 236px;vertical-align: bottom;}

        .tr0{height: 17px;}
        .tr1{height: 16px;}
        .tr2{height: 4px;}
        .tr3{height: 3px;}
        .tr4{height: 15px;}
        .tr5{height: 19px;}
        .tr6{height: 10px;}
        .tr7{height: 14px;}
        .tr8{height: 25px;}
        .tr9{height: 5px;}
        .tr10{height: 20px;}
        .tr11{height: 11px;}
        .tr12{height: 18px;}
        .tr13{height: 9px;}
        .tr14{height: 6px;}
        .tr15{height: 34px;}
        .tr16{height: 27px;}
        .tr17{height: 13px;}
        .tr18{height: 7px;}
        .tr20{height: 8px;}
        .tr21{height: 29px;}
        .tr22{height: 43px;}
        .tr23{height: 42px;}
        .tr24{height: 30px;}
        .tr25{height: 41px;}
        .tr26{height: 40px;}
        .tr27{height: 38px;}
        .tr28{height: 2px;}
        .tr29{height: 1px;}
        .tr30{height: 28px;}

        .t0{width: 1049px;font: 11px 'Verdana';}
        .t2{width: 1047px;font: 11px 'Verdana';}
        .t3{width: 1044px;margin-left: 4px;font: 11px 'Verdana';}
        {/literal}

    </STYLE>

<BODY>
<DIV id="page_1">
    <DIV id="id_1">
        <P class="p0 ft0">Унифицированная форма № <NOBR>ТОРГ-12</NOBR> Утверждена постановлением Госкомстата России от 25.12.98 № 132</P>
        <TABLE cellpadding=0 cellspacing=0 class="t0">
            <TR>
                <TD class="tr0 td0"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td1"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td2"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td3"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td4"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td5"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td6"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td7"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td8"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td9"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td11"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td12"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td13"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td14"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td15"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td16"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td17"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td18"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td19"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td20"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr0 td21"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr1 td22"><P class="p2 ft2">Код</P></TD>
            </TR>
            <TR>
                <TD class="tr2 td0"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td1"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td2"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td3"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td4"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td5"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td6"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td7"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td8"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td9"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td11"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td12"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td13"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td14"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td15"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td16"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td17"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td18"><P class="p1 ft3">&nbsp;</P></TD>
                <TD colspan=2 class="tr2 td1"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td21"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr3 td23"><P class="p1 ft4">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr4 td0"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td1"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td2"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td3"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td4"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td5"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td6"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td7"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td8"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td9"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td11"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td12"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td13"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td14"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td15"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td16"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan=5 class="tr4 td24"><P class="p3 ft5">Форма по ОКУД</P></TD>
                <TD class="tr4 td25"><P class="p4 ft2">0310001</P></TD>
            </TR>
            <TR>
                <TD class="tr2 td0"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td1"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td2"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td3"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td4"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td5"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td6"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td7"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td8"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td9"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td11"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td12"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td13"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td14"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td15"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td16"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td17"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td18"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td19"><P class="p1 ft3">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr5 td26"><P class="p5 ft2">по ОКПО</P></TD>
                <TD class="tr3 td23"><P class="p1 ft4">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan="19" class="tr4 td0">
                    <P class="p1" contenteditable="true">
                        {$CONFIG.firm_name}, {$CONFIG.firm_legal_address}, {if !empty($CONFIG.admin_phone)}т. {$CONFIG.admin_phone}, {/if} {if !empty($CONFIG.firm_inn)}ИНН {$CONFIG.firm_inn}, {/if} {if !empty($CONFIG.firm_kpp)}КПП {$CONFIG.firm_kpp}, {/if} р/с {$CONFIG.firm_rs}, в {$CONFIG.firm_bank}, к/с {$CONFIG.firm_ks}, БИК {$CONFIG.firm_bik}
                    </P>
                </TD>
                <TD class="tr4 td18"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td19"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td25"><P class="p1 center"  contenteditable="true">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr3 td27"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td28"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td29"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td30"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td31"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td32"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td33"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td34"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td35"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td36"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td37"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td37"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td38"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td39"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td40"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td41"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td42"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td43"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td44"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr2 td18"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td19"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td20"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td21"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr3 td23"><P class="p1 ft4">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr6 td0"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td1"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td2"><P class="p1 ft6">&nbsp;</P></TD>
                <TD colspan=10 class="tr6 td45"><P class="p6 ft7"><NOBR>(организация-грузоотправитель,</NOBR> адрес, телефон, факс, банковские реквизиты)</P></TD>
                <TD class="tr6 td12"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td13"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td14"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td15"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td16"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td17"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td18"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td19"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td20"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td21"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td25"><P class="p1 ft6">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan="23" class="tr0 td27 border-right"><P class="p1" contenteditable="true">&nbsp;</P></TD>
                <TD class="tr0 td23"><P class="p1 ft1">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr7 td0"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr7 td1"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr7 td2"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr7 td3"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan=6 class="tr7 td48"><P class="p7 ft7">структурное подразделение</P></TD>
                <TD class="tr7 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr7 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr7 td11"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr7 td12"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr7 td13"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr7 td14"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan=7 class="tr7 td49"><P class="p8 ft2">Вид деятельности по ОКДП</P></TD>
                <TD class="tr7 td25"><P class="p1 center"  contenteditable="true">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan=2 rowspan=2 class="tr8 td3"><P class="p9 ft8">Грузополучатель</P></TD>
                <TD class="tr9 td2"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td3"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td4"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td5"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td6"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td7"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td8"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td9"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td10"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td10"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td11"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td12"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td13"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td14"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td15"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td16"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td17"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td18"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td19"><P class="p1 ft9">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr8 td26"><P class="p5 ft2">по ОКПО</P></TD>
                <TD class="tr2 td23"><P class="p1 ft3">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr10 td2"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td3"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td4"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td5"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td6"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td7"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td8"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td9"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td11"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td12"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td13"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td14"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td15"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td16"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td17"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td18"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr10 td19"><P class="p1 ft1">&nbsp;</P></TD>
                <TD rowspan="2" class="tr10 td25"><P class="p1 center border-bottom"  contenteditable="true">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr11 td0"><P class="p1 ft10">&nbsp;</P></TD>
                <TD class="tr11 td1"><P class="p1 ft10">&nbsp;</P></TD>
                <TD colspan="18" class="tr6 td29">
                    <P class="p1" contenteditable="true">
                        {if $user.is_company}
                            {$user.company}{if !empty($user.company_address)}, {$user.company_address}{/if}{if !empty($user.phone)}, т. {$user.phone}{/if}{if !empty($user.company_rs)}, р/с {$user.company_rs}, в {$user.company_bank}{/if}{if !empty($user.company_bank_ks)}, к/с {$user.company_bank_ks}{/if}{if !empty($user.company_bank_bik)}, БИК {$user.company_bank_bik}{/if}
                        {else}
                            {$user->getFio()}
                        {/if}
                    </P>
                </TD>

                <TD class="tr11 td19"><P class="p1 ft10">&nbsp;</P></TD>
                <TD class="tr11 td20"><P class="p1 ft10">&nbsp;</P></TD>
                <TD class="tr11 td21"><P class="p1 ft10">&nbsp;</P></TD>

            </TR>
            <TR>
                <TD class="tr6 td0"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td1"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td2"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td3"><P class="p1 ft6">&nbsp;</P></TD>
                <TD colspan=10 class="tr6 td50"><P class="p10 ft7">(организация, адрес, телефон, факс, банковские реквизиты)</P></TD>
                <TD class="tr6 td13"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td14"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td15"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td16"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td17"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td18"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td19"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td20"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td21"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td25"><P class="p1 ft6">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan=2 class="tr12 td3"><P class="p9 ft2">Поставщик</P></TD>
                <TD class="tr12 td2"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td3"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td4"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td5"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td6"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td7"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td8"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td9"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td11"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td12"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td13"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td14"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td15"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td16"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td17"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td18"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td19"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan=2 class="tr12 td26"><P class="p5 ft2">по ОКПО</P></TD>
                <TD class="tr12 td25"><P class="p1 center"  contenteditable="true">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr11 td0"><P class="p1 ft10">&nbsp;</P></TD>
                <TD class="tr11 td1"><P class="p1 ft10">&nbsp;</P></TD>
                <TD colspan="18" class="tr6 td29">
                    <P class="p1" contenteditable="true">
                        {$CONFIG.firm_name}, {$CONFIG.firm_legal_address}, {if !empty($CONFIG.admin_phone)}т. {$CONFIG.admin_phone}, {/if}р/с {$CONFIG.firm_rs}, в {$CONFIG.firm_bank}, к/с {$CONFIG.firm_ks}, БИК {$CONFIG.firm_bik}
                    </P>
                </TD>

                <TD class="tr11 td19"><P class="p1 ft10">&nbsp;</P></TD>
                <TD class="tr11 td20"><P class="p1 ft10">&nbsp;</P></TD>
                <TD class="tr11 td21"><P class="p1 ft10">&nbsp;</P></TD>
                <TD class="tr6 td23"><P class="p1 ft6">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr6 td0"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td1"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td2"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td3"><P class="p1 ft6">&nbsp;</P></TD>
                <TD colspan=10 class="tr6 td50"><P class="p10 ft7">(организация, адрес, телефон, факс, банковские реквизиты)</P></TD>
                <TD class="tr6 td13"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td14"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td15"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td16"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td17"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td18"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td19"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td20"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td21"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td25"><P class="p1 ft6">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan=2 class="tr12 td3"><P class="p9 ft2">Плательщик</P></TD>
                <TD class="tr12 td2"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td3"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td4"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td5"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td6"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td7"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td8"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td9"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td10"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td11"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td12"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td13"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td14"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td15"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td16"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td17"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td18"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr12 td19"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan=2 class="tr12 td26"><P class="p5 ft2">по ОКПО</P></TD>
                <TD rowspan="2" class="tr12 td25 border-bottom"><P class="p1 center"  contenteditable="true">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr11 td0"><P class="p1 ft10">&nbsp;</P></TD>
                <TD class="tr11 td1"><P class="p1 ft10">&nbsp;</P></TD>
                <TD colspan="18" class="tr6 td29">
                    <P class="p1" contenteditable="true">
                        {if $user.is_company}
                            {$user.company}{if !empty($user.company_address)}, {$user.company_address}{/if}{if !empty($user.phone)}, т. {$user.phone}{/if}{if !empty($user.company_rs)}, р/с {$user.company_rs}, в {$user.company_bank}{/if}{if !empty($user.company_bank_ks)}, к/с {$user.company_bank_ks}{/if}{if !empty($user.company_bank_bik)}, БИК {$user.company_bank_bik}{/if}
                        {else}
                            {$user->getFio()}
                        {/if}
                    </P>
                </TD>
                <TD class="tr6 td51"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td52"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td47"><P class="p1 ft6">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr13 td0"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td1"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td2"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td3"><P class="p1 ft11">&nbsp;</P></TD>
                <TD colspan=10 class="tr13 td50"><P class="p10 ft7">(организация, адрес, телефон, факс, банковские реквизиты)</P></TD>
                <TD class="tr13 td13"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td14"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td15"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td16"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td17"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td53"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td19"><P class="p1 ft11">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr4 td26"><P class="p3 ft2">номер</P></TD>
                <TD class="tr13 td25"><P class="p1 ft11">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan=2 rowspan=3 class="tr1 td3"><P class="p9 ft2">Основание</P></TD>
                <TD class="tr14 td2"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td3"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td4"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td5"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td6"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td7"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td8"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td9"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td10"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td10"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td11"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td12"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td13"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td14"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td15"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td16"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td17"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td53"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td19"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td25"><P class="p1 ft12">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr2 td2"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td3"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td4"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td5"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td6"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td7"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td8"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td9"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td11"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td12"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td13"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td14"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td15"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td16"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td17"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td53"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr3 td51"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td52"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td47"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td23"><P class="p1 ft4">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr14 td2"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td3"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td4"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td5"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td6"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td7"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td8"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td9"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td10"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td10"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td11"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td12"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td13"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td14"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td15"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td16"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td17"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td53"><P class="p1 ft12">&nbsp;</P></TD>
                <TD class="tr14 td19"><P class="p1 ft12">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr4 td26"><P class="p11 ft2">дата</P></TD>
                <TD class="tr14 td25"><P class="p1 ft12">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr13 td0"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td1"><P class="p1 ft11">&nbsp;</P></TD>
                <TD colspan="18" class="tr13 td2 border-right"><P class="p1" contenteditable="true">{t num=$order.order_num}Заказ №%num{/t}</P></TD>
                <TD class="tr13 td19"><P class="p1 ft11">&nbsp;</P></TD>
                <TD class="tr13 td25"><P class="p1 ft11">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr2 td0"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td1"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr3 td29"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td30"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td31"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td32"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td33"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td34"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td35"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td36"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td37"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td37"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td38"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td39"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td40"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td41"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td42"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td43"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td44"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td54"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td51"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td52"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td47"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td23"><P class="p1 ft4">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr6 td0"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td1"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td2"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td3"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td4"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td5"><P class="p1 ft6">&nbsp;</P></TD>
                <TD colspan=5 class="tr6 td55"><P class="p12 ft7">(договор, <NOBR>заказ-наряд)</NOBR></P></TD>
                <TD class="tr6 td10"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td11"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td12"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td13"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td14"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td15"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td16"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td17"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td53"><P class="p1 ft6">&nbsp;</P></TD>
                <TD class="tr6 td19"><P class="p1 ft6">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr4 td26"><P class="p3 ft2">номер</P></TD>
                <TD class="tr6 td25"><P class="p1 ft6">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr9 td0"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td1"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td2"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td3"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td4"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td5"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td6"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td7"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td8"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td9"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td10"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td10"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td11"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td12"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td13"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td14"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td15"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td16"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td17"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td53"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td19"><P class="p1 ft9">&nbsp;</P></TD>
                <TD class="tr9 td25"><P class="p1 ft9">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr2 td0"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td1"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td2"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td3"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td4"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td5"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td6"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td7"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td8"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr3 td36"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td37"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td37"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td38"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td39"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td40"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr2 td14"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td15"><P class="p1 ft3">&nbsp;</P></TD>
                <TD colspan=2 class="tr2 td12"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td53"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr3 td51"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td52"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td47"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td23"><P class="p1 ft4">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr4 td0"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td1"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td2"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan=5 rowspan=3 class="tr15 td56"><P class="p13 ft13">ТОВАРНАЯ НАКЛАДНАЯ</P></TD>
                <TD class="tr4 td57"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan=4 class="tr7 td58"><P class="p9 ft2">Номер документа</P></TD>
                <TD colspan=2 class="tr7 td59"><P class="p14 ft2">Дата составления</P></TD>
                <TD class="tr4 td14"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan=4 class="tr4 td60"><P class="p15 ft2">Транспортная накладная</P></TD>
                <TD class="tr4 td19"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan=2 class="tr4 td26"><P class="p11 ft2">дата</P></TD>
                <TD class="tr4 td25"><P class="p1 ft1">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr2 td0"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td1"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td2"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td57"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td9"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td61"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td12"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td62"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td14"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td15"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td16"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td17"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td53"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr3 td51"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td52"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td47"><P class="p1 ft4">&nbsp;</P></TD>
                <TD class="tr3 td23"><P class="p1 ft4">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr4 td0"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td1"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td2"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td57"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan="4" class="tr7 td36 border-right"><P class="p1 center" contenteditable="true">{$order.order_num}</P></TD>
                <TD colspan="2" class="tr7 td39 border-right"><P class="p1 center" contenteditable="true">{date("Y.m.d" , strtotime($order.dateof))}</P></TD>
                <TD class="tr4 td14"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td15"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td16"><P class="p1 ft1">&nbsp;</P></TD>
                <TD class="tr4 td17"><P class="p1 ft1">&nbsp;</P></TD>
                <TD colspan=4 class="tr4 td65"><P class="p3 ft8">Вид операции</P></TD>
                <TD class="tr4 td25"><P class="p1 ft1">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr2 td0"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td1"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td2"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td3"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td4"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td5"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td6"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td7"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td8"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td9"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td11"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td12"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td13"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td14"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td15"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td16"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td17"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td18"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td19"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td20"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr2 td21"><P class="p1 ft3">&nbsp;</P></TD>
                <TD class="tr3 td23"><P class="p1 ft4">&nbsp;</P></TD>
            </TR>
        </TABLE>
    </DIV>
</DIV>
<DIV id="page_2">
    {$page = 1}
    {$counter = 1}
    {foreach $products_array as $product_arr}
    <TABLE cellpadding=0 cellspacing=0 class="t2">
        <tr class="header">
            <td style="padding: 0"></td>
            <td rowspan="2">Номер по порядку</td>
            <td colspan="2">Товар</td>
            <td colspan="2">Единица измерения</td>
            <td rowspan="2">Вид упокавки</td>
            <td colspan="2">Количество</td>
            <td rowspan="2">Масса брутто</td>
            <td rowspan="2">Количество (масса нетто)</td>
            <td rowspan="2">Цена руб. коп.</td>
            <td rowspan="2">Сумма без НДС руб. коп.</td>
            <td colspan="2">НДС</td>
            <td rowspan="2">Сумма с учетом НДС руб. коп.</td>
        </tr>
        <tr class="header">
            <td style="padding: 0"></td>
            <td>Наименование, характеристика, сорт, артикул товара</td>
            <td>Код</td>
            <td>Наименование</td>
            <td>Код по ОКЕИ</td>
            <td>В одном месте</td>
            <td>Мест, штук</td>
            <td>Ставка, %</td>
            <td>Сумма руб. коп.</td>
        </tr>
        <TR class="header">
            <TD style="padding: 0"></TD><TD>1</TD><TD>2</TD><TD>3</TD><TD>4</TD><TD>5</TD><TD>6</TD><TD>7</TD><TD>8</TD><TD>9</TD><TD>10</TD><TD>11</TD><TD>12</TD><TD>13</TD><TD>14</TD><TD>15</TD>
        </TR>

        {foreach from=$product_arr key=n item=item name=foo}
        {assign var=product value=$products[$n].product}
        <TR class="header {if $smarty.foreach.foo.last} border-bottom {/if} " >
            <TD class="tr1 td128" style="padding: 0"></TD>
            <TD class="tr1 td146"><P class="p1 center">{$counter}</P></TD>
            <TD class="tr1 td147"><P class="p1">{$item.cartitem.title}</P></TD>
            {$unit = $product->getUnit()}
            <TD class="tr1 td106"><P class="p1 center" contenteditable="true"></P></TD>
            <TD class="tr1 td107"><P class="p1 center">{$unit.stitle}</P></TD>
            <TD class="tr1 td108"><P class="p1 center">{$unit.code}</P></TD>
            <TD class="tr1 td109"><P class="p1 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td148 border-right"><P class="p1 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td112 border-right"><P class="p1 center">{$item.cartitem.amount}</P></TD>
            <TD class="tr1 td112 border-right"><P class="p1 center" contenteditable="true"></P></TD>
            <TD class="tr1 td149 border-right"><P class="p1 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">{$item.single_cost_with_discount}</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">{$taxes.items[$n].subtotal}</P></TD>
            <TD class="tr1 td114 border-right"><P class="p1 center">
                    {if !empty($taxes.taxes)}
                        {round($taxes.items[$n].taxes.rate, 2)}
                    {else}
                        Без НДС
                    {/if}
                </P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">{$taxes.items[$n].taxes.value}</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">{$taxes.items[$n].taxes.cost}</P></TD>
        </TR>
            {$counter = $counter + 1}
        {/foreach}
        {if count($products_array) != $page}
            </TABLE>
            <div class="page-break" style="margin-bottom: 20px"></div>
        {/if}
        {$page = $page + 1}
    {/foreach}
        <TR>
            <TD colspan="8" class="tr1 td128 border-right right" style="padding-right: 10px">Итого</TD>
            <TD class="tr1 td112 border-right"><P class="p1 center" contenteditable="true">{$total_amount}</P></TD>
            <TD class="tr1 td112 border-right"><P class="p1 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td149 border-right"><P class="p1 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">X</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">{$subtotal}</P></TD>
            <TD class="tr1 td114 border-right"><P class="p1 center">X</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">{$all_taxes}</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">{$subtotal + $all_taxes}</P></TD>
        </TR>
        <TR>
            <TD colspan="8" class="tr1 td128 border-right right" style="padding-right: 10px">Всего по накладной</TD>
            <TD class="tr1 td112 border-right"><P class="p1 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td112 border-right"><P class="p1 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td149 border-right"><P class="p1 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">X</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">{$subtotal}</P></TD>
            <TD class="tr1 td114 border-right"><P class="p1 center">X</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">{$all_taxes}</P></TD>
            <TD class="tr1 td117 border-right"><P class="p1 center">{$subtotal + $all_taxes}</P></TD>
        </TR>
    </TABLE>
    <TABLE cellpadding=0 cellspacing=0 class="t3">
        <TR>
            <TD colspan=26 class="tr21 td182"><P class="p1 ft8">Товарная накладная имеет приложение на ______________________ листах и содержит ___________________________________________ порядковых номеров записей</P></TD>
            <TD class="tr21 td162"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr21 td163"><P class="p1 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr22 td152"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td153"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td5"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td1"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td154"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td17"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td16"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td155"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td17"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td156"><P class="p1 ft1">&nbsp;</P></TD>
            <TD colspan=6 class="tr22 td183"><P class="p13 ft8">Масса груза (нетто)</P></TD>
            <TD class="tr22 td154"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr23 td52"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr23 td170"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr23 td171"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr23 td172"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr23 td39"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr23 td173"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td184"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td185"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td7"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td162"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr22 td163"><P class="p1 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr20 td152"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td153"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td5"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td1"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td154"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td17"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td16"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td155"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td17"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td156"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td155"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td164"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td165"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td186"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td187"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td188"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td154"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td20"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td158"><P class="p1 ft19">&nbsp;</P></TD>
            <TD colspan=2 class="tr20 td159"><P class="p45 ft7">прописью</P></TD>
            <TD class="tr20 td12"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td160"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td184"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td185"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td7"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td162"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td163"><P class="p1 ft19">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=3 class="tr24 td189"><P class="p46 ft2">Всего мест</P></TD>
            <TD class="tr24 td1"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td154"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td17"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td16"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td155"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td17"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td156"><P class="p1 ft1">&nbsp;</P></TD>
            <TD colspan=9 class="tr24 td190"><P class="p47 ft2">Масса груза (брутто)</P></TD>
            <TD class="tr24 td153"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td163"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td12"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td160"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td184"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td185"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td7"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td162"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td163"><P class="p1 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr6 td152"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr13 td191"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td192"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td193"><P class="p1 ft11">&nbsp;</P></TD>
            <TD colspan=4 class="tr13 td194"><P class="p1 ft7">прописью</P></TD>
            <TD class="tr13 td195"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td196"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr6 td155"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td164"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td165"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td186"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td187"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td188"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td154"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr13 td197"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td198"><P class="p1 ft11">&nbsp;</P></TD>
            <TD colspan=2 class="tr13 td199"><P class="p48 ft7">прописью</P></TD>
            <TD class="tr13 td138"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td200"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr6 td184"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td185"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td7"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td162"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td163"><P class="p1 ft6">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=7 rowspan=2 class="tr25 td201"><P class="p46 ft8">Приложение (паспорта, сертификаты, и т. п.) на</P></TD>
            <TD rowspan=2 class="tr26 td202"><P class="p1 ft1">&nbsp;</P></TD>
            <TD rowspan=2 class="tr26 td44"><P class="p1 ft1">&nbsp;</P></TD>
            <TD rowspan=2 class="tr26 td203"><P class="p1 ft1">&nbsp;</P></TD>
            <TD rowspan=2 class="tr26 td202"><P class="p1 ft1">&nbsp;</P></TD>
            <TD colspan=3 rowspan=2 class="tr25 td204"><P class="p49 ft2">листах</P></TD>
            <TD class="tr16 td187"><P class="p1 ft1">&nbsp;</P></TD>
            <TD rowspan=2 class="tr25 td188"><P class="p1 ft1">&nbsp;</P></TD>
            <TD colspan=3 rowspan=2 class="tr25 td205"><P class="p1 ft2">По доверенности №</P></TD>
            <TD class="tr16 td153"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr16 td163"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr16 td12"><P class="p1 ft1">&nbsp;</P></TD>
            <TD rowspan=2 class="tr25 td160"><P class="p49 ft2">от "</P></TD>
            <TD rowspan=2 class="tr25 td184"><P class="p50 ft2">"</P></TD>
            <TD class="tr16 td185"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr16 td7"><P class="p1 ft1">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr25 td206"><P class="p1 ft2">г.</P></TD>
        </TR>
        <TR>
            <TD class="tr17 td207"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td153"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td163"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td12"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td185"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td7"><P class="p1 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=3 rowspan=3 class="tr27 td189"><P class="p48 ft2">Всего отпущено на сумму:</P></TD>
            <TD class="tr28 td1"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td154"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td17"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td16"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td155"><P class="p1 ft20">&nbsp;</P></TD>
            <TD rowspan=2 class="tr20 td17"><P class="p1 ft19">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr20 td208"><P class="p46 ft7">прописью</P></TD>
            <TD rowspan=2 class="tr20 td164"><P class="p1 ft19">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr20 td78"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr29 td207"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr28 td188"><P class="p1 ft20">&nbsp;</P></TD>
            <TD colspan=3 rowspan=3 class="tr27 td205"><P class="p1 ft2">Выданной</P></TD>
            <TD class="tr29 td171"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr29 td172"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr29 td39"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr28 td160"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td184"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr29 td175"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr28 td7"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td162"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td163"><P class="p1 ft20">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr14 td1"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td154"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td17"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td16"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td155"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td209"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td188"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td153"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td163"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td12"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td160"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td184"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td185"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td7"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td162"><P class="p1 ft12">&nbsp;</P></TD>
            <TD class="tr14 td163"><P class="p1 ft12">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr24 td1"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td154"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td17"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td16"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td155"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td17"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td156"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td155"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td164"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td165"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td186"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td209"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td188"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td153"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td163"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td12"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td160"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td184"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td185"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td7"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td162"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr24 td163"><P class="p1 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr6 td152"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td153"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td5"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td1"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td154"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td17"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td16"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td155"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td17"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td156"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td155"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td164"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td165"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td186"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr13 td210"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr6 td188"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td154"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td20"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr13 td198"><P class="p1 ft11">&nbsp;</P></TD>
            <TD colspan=7 class="tr13 td211"><P class="p51 ft7">кем, кому (организация, должность, фамилия, и. о.)</P></TD>
            <TD class="tr13 td212"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr6 td163"><P class="p1 ft6">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=3 class="tr0 td189"><P class="p52 ft2">Отпуск разрешил</P></TD>
            <TD class="tr0 td1"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td154"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td17"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td16"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td155"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td17"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td156"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td155"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td164"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td165"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td186"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td209"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td188"><P class="p1 ft1">&nbsp;</P></TD>
            <TD colspan=3 rowspan=2 class="tr30 td205"><P class="p1 ft2">Груз принял</P></TD>
            <TD class="tr0 td153"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td163"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td12"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td160"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td184"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td185"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td7"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td162"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr0 td163"><P class="p1 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr11 td152"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr11 td153"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr6 td192"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td193"><P class="p1 ft7">должность</P></TD>
            <TD class="tr6 td213"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr11 td17"><P class="p1 ft10">&nbsp;</P></TD>
            <TD colspan=2 class="tr6 td214"><P class="p53 ft7">подпись</P></TD>
            <TD class="tr11 td17"><P class="p1 ft10">&nbsp;</P></TD>
            <TD colspan=3 class="tr6 td215"><P class="p54 ft7">расшифровка подписи</P></TD>
            <TD colspan=2 class="tr11 td78"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr6 td210"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr11 td188"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr11 td153"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr11 td163"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr11 td12"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr11 td160"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr11 td184"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr11 td185"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr11 td7"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr11 td162"><P class="p1 ft10">&nbsp;</P></TD>
            <TD class="tr11 td163"><P class="p1 ft10">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=4 class="tr7 td216"><P class="p55 ft2" contenteditable="true">Главный (страший) бухгалтер</P></TD>
            <TD class="tr7 td154"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td17"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td16"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td155"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td17"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td156"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td155"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td164"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td165"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td186"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr17 td210"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td188"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td154"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td20"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr17 td198"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr17 td191"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr17 td217"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr17 td138"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr17 td200"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr17 td218"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr17 td219"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr17 td220"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr17 td212"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr7 td163"><P class="p1 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr6 td152"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td153"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr13 td192"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td193"><P class="p1 ft7">должность</P></TD>
            <TD class="tr13 td213"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr6 td17"><P class="p1 ft6">&nbsp;</P></TD>
            <TD colspan=2 class="tr13 td214"><P class="p53 ft7">подпись</P></TD>
            <TD class="tr6 td17"><P class="p1 ft6">&nbsp;</P></TD>
            <TD colspan=3 class="tr13 td215"><P class="p54 ft7">расшифровка подписи</P></TD>
            <TD colspan=2 class="tr6 td78"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr13 td210"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr6 td188"><P class="p1 ft6">&nbsp;</P></TD>
            <TD colspan=5 rowspan=2 class="tr0 td221"><P class="p1 ft2">Груз получил грузополучатель</P></TD>
            <TD class="tr6 td12"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td160"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td184"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td185"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td7"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td162"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td163"><P class="p1 ft6">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=3 rowspan=2 class="tr1 td189"><P class="p56 ft5">Отпуск груза произвел</P></TD>
            <TD class="tr18 td1"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td154"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td17"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td16"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td155"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td17"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td156"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td155"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td164"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td165"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td186"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td209"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td188"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td12"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td160"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td184"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td185"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td7"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td162"><P class="p1 ft17">&nbsp;</P></TD>
            <TD class="tr18 td163"><P class="p1 ft17">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr13 td1"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td154"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td17"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td16"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td155"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td17"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td156"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td155"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td164"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td165"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td186"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr20 td210"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr13 td188"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td154"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td20"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td158"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td153"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr20 td217"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td138"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td200"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td218"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td219"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td220"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr20 td212"><P class="p1 ft19">&nbsp;</P></TD>
            <TD class="tr13 td163"><P class="p1 ft11">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr6 td152"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td153"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr13 td192"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr13 td193"><P class="p1 ft7">должность</P></TD>
            <TD class="tr13 td213"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr6 td17"><P class="p1 ft6">&nbsp;</P></TD>
            <TD colspan=2 class="tr13 td214"><P class="p53 ft7">подпись</P></TD>
            <TD class="tr6 td17"><P class="p1 ft6">&nbsp;</P></TD>
            <TD colspan=3 class="tr13 td215"><P class="p54 ft7">расшифровка подписи</P></TD>
            <TD colspan=2 class="tr6 td78"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr13 td210"><P class="p1 ft11">&nbsp;</P></TD>
            <TD class="tr6 td188"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td154"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td20"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td158"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td153"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td163"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td12"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td160"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td184"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td185"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td7"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td162"><P class="p1 ft6">&nbsp;</P></TD>
            <TD class="tr6 td163"><P class="p1 ft6">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=3 class="tr10 td189"><P class="p57 ft2">М. П.</P></TD>
            <TD class="tr10 td1"><P class="p58 ft2">"</P></TD>
            <TD class="tr10 td154"><P class="p1 ft1">&nbsp;</P></TD>
            <TD colspan=9 class="tr10 td222"><P class="p59 ft2">" ___________________ 20____ года</P></TD>
            <TD class="tr10 td209"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr10 td188"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr10 td154"><P class="p1 ft1">&nbsp;</P></TD>
            <TD colspan=2 class="tr10 td223"><P class="p47 ft2">М. П.</P></TD>
            <TD colspan=2 class="tr10 td159"><P class="p60 ft2">"</P></TD>
            <TD colspan=5 class="tr10 td224"><P class="p1 ft2">" ___________________ 20____ года</P></TD>
            <TD class="tr10 td162"><P class="p1 ft1">&nbsp;</P></TD>
            <TD class="tr10 td163"><P class="p1 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr28 td152"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td153"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td5"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td1"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td154"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td17"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td16"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td155"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td17"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td156"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td155"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td164"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td165"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td186"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td209"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td188"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td154"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td20"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td158"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td153"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td163"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td12"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td160"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td184"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td185"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td7"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td162"><P class="p1 ft20">&nbsp;</P></TD>
            <TD class="tr28 td163"><P class="p1 ft20">&nbsp;</P></TD>
        </TR>
    </TABLE>
</DIV>
</BODY>
</HTML>
