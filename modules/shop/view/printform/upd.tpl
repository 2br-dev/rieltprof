{$user = $order->getUser()}
{$address = $order->getAddress()}
{$cart = $order->getCart()}
{$order_data = $cart->getOrderData(true, false)}
{$products = $cart->getProductItems()}

<!DOCTYPE HTML>
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <TITLE>Универсальный передаточный документ</TITLE>
    {literal}
    <STYLE type="text/css">
        @media print {
            .page-break { page-break-after: always;}
        }
        body {margin-top: 0px;margin-left: 0px;}
        .head td{
            border-top: 1px solid black;
        }
        .noborder{
            border: none !important;
        }
        #page_1 {position:relative; overflow: hidden;margin: 12px 0px 24px 17px;padding: 0px;border: none;width: 1106px;}

        #page_1 #p1dimg1 {position:absolute;top:404px;left:190px;z-index:-1;width:854px;height:157px;}
        #page_1 #p1dimg1 #p1img1 {width:854px;height:157px;}

        .border-right{
            border-right: #000000 1px solid;
        }
        .border-bottom{
            border-bottom: #000000 1px solid;
        }
        .center{
            text-align: center!important;
        }

        /*td{*/
            /*border: #000000 1px solid;*/
            /*box-sizing: border-box;*/
        /*}*/

        .ft0{font: 11px 'Times New Roman';line-height: 13px;}
        .ft1{font: 1px 'Times New Roman';line-height: 1px;}
        .ft2{font: 9px 'Times New Roman';line-height: 12px;}
        .ft3{font: 11px 'Times New Roman';line-height: 14px;}
        .ft4{font: 1px 'Times New Roman';line-height: 4px;}
        .ft5{font: 1px 'Times New Roman';line-height: 3px;}
        .ft6{font: 1px 'Times New Roman';line-height: 10px;}
        .ft7{font: 9px 'Times New Roman';line-height: 10px;}
        .ft8{font: 11px 'Times New Roman';line-height: 12px;}
        .ft9{font: 1px 'Times New Roman';line-height: 6px;}
        .ft10{font: 9px 'Times New Roman';line-height: 11px;}
        .ft11{font: 1px 'Times New Roman';line-height: 5px;}
        .ft12{font: bold 11px 'Times New Roman';line-height: 13px;}
        .ft13{font: 12px 'Times New Roman';line-height: 15px;}
        .ft14{font: 11px 'Times New Roman';line-height: 11px;}
        .ft15{font: 12px 'Times New Roman';line-height: 11px;}
        .ft16{font: 12px 'Times New Roman';line-height: 14px;}
        .ft17{font: 9px 'Times New Roman';line-height: 8px;}
        .ft18{font: 1px 'Times New Roman';line-height: 7px;}
        .ft19{font: 1px 'Times New Roman';line-height: 8px;}
        .ft20{font: 10px 'Times New Roman';line-height: 11px;}
        .ft21{font: 10px 'Times New Roman';line-height: 12px;}
        .ft22{font: 1px 'Times New Roman';line-height: 2px;}
        .ft23{font: bold 11px 'Times New Roman';line-height: 10px;}
        .ft24{font: 11px 'Times New Roman';line-height: 10px;}
        .ft25{font: 12px 'Times New Roman';line-height: 13px;}
        .ft26{font: 8px 'Times New Roman';line-height: 10px;}
        .ft27{font: 12px 'Times New Roman';line-height: 12px;}
        .ft28{font: 1px 'Times New Roman';line-height: 12px;}

        .p0{text-align: left;padding-left: 2px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p1{text-align: left;padding-left: 11px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p2{text-align: left;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p3{text-align: right;padding-right: 51px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p4{text-align: right;padding-right: 6px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p5{text-align: center;padding-right: 4px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p6{text-align: left;padding-left: 22px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p7{text-align: right;padding-right: 33px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p8{text-align: left;padding-left: 1px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p9{text-align: center;padding-right: 1px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p10{text-align: left;padding-left: 13px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p11{text-align: center;padding-right: 6px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p12{text-align: center;padding-right: 3px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p13{text-align: right;padding-right: 5px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p14{text-align: center;padding-right: 5px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p15{text-align: center;padding-right: 14px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p16{text-align: left;padding-left: 7px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p17{text-align: left;padding-left: 18px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p18{text-align: left;padding-left: 5px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p19{text-align: left;padding-left: 9px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p20{text-align: center;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p21{text-align: left;padding-left: 8px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p22{text-align: left;padding-left: 16px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p23{text-align: center;padding-right: 38px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p24{text-align: right;padding-right: 47px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p25{text-align: right;padding-right: 19px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p26{text-align: right;padding-right: 43px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p27{text-align: right;padding-right: 46px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p28{text-align: right;padding-right: 13px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p29{text-align: right;padding-right: 4px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p30{text-align: left;padding-left: 40px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p31{text-align: left;padding-left: 6px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p32{text-align: left;padding-left: 23px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p33{text-align: center;padding-right: 27px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p34{text-align: center;padding-right: 10px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p35{text-align: left;padding-left: 14px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p36{text-align: left;padding-left: 4px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p37{text-align: right;padding-right: 11px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p38{text-align: left;padding-left: 208px;margin-top: 0px;margin-bottom: 0px;}
        .p39{text-align: right;padding-right: 10px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p40{text-align: right;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p41{text-align: left;padding-left: 26px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p42{text-align: right;padding-right: 124px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p43{text-align: left;padding-left: 66px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p44{text-align: right;padding-right: 112px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p45{text-align: right;padding-right: 1px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p46{text-align: right;padding-right: 2px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p47{text-align: right;padding-right: 60px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p48{text-align: center;padding-right: 40px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p49{text-align: left;padding-left: 15px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p50{text-align: left;padding-left: 74px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}

        .td0{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 123px;vertical-align: bottom;}
        .td1{padding: 0px;margin: 0px;width: 117px;vertical-align: bottom;}
        .td2{padding: 0px;margin: 0px;width: 29px;vertical-align: bottom;}
        .td3{padding: 0px;margin: 0px;width: 9px;vertical-align: bottom;}
        .td4{padding: 0px;margin: 0px;width: 34px;vertical-align: bottom;}
        .td5{padding: 0px;margin: 0px;width: 84px;vertical-align: bottom;}
        .td6{padding: 0px;margin: 0px;width: 65px;vertical-align: bottom;}
        .td7{padding: 0px;margin: 0px;width: 44px;vertical-align: bottom;}
        .td8{padding: 0px;margin: 0px;width: 51px;vertical-align: bottom;}
        .td9{padding: 0px;margin: 0px;width: 18px;vertical-align: bottom;}
        .td10{padding: 0px;margin: 0px;width: 23px;vertical-align: bottom;}
        .td11{padding: 0px;margin: 0px;width: 57px;vertical-align: bottom;}
        .td12{padding: 0px;margin: 0px;width: 38px;vertical-align: bottom;}
        .td13{padding: 0px;margin: 0px;width: 49px;vertical-align: bottom;}
        .td14{padding: 0px;margin: 0px;width: 96px;vertical-align: bottom;}
        .td15{padding: 0px;margin: 0px;width: 36px;vertical-align: bottom;}
        .td16{padding: 0px;margin: 0px;width: 4px;vertical-align: bottom;}
        .td17{padding: 0px;margin: 0px;width: 72px;vertical-align: bottom;}
        .td18{padding: 0px;margin: 0px;width: 5px;vertical-align: bottom;}
        .td19{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 29px;vertical-align: bottom;}
        .td20{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 9px;vertical-align: bottom;}
        .td21{padding: 0px;margin: 0px;width: 41px;vertical-align: bottom;}
        .td22{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 43px;vertical-align: bottom;}
        .td23{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 34px;vertical-align: bottom;}
        .td24{padding: 0px;margin: 0px;width: 257px;vertical-align: bottom;}
        .td25{padding: 0px;margin: 0px;width: 208px;vertical-align: bottom;}
        .td26{padding: 0px;margin: 0px;width: 13px;vertical-align: bottom;}
        .td27{padding: 0px;margin: 0px;width: 25px;vertical-align: bottom;}
        .td28{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 21px;vertical-align: bottom;}
        .td29{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 29px;vertical-align: bottom;}
        .td30{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 9px;vertical-align: bottom;}
        .td31{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 43px;vertical-align: bottom;}
        .td32{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 34px;vertical-align: bottom;}
        .td33{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 41px;vertical-align: bottom;}
        .td34{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 65px;vertical-align: bottom;}
        .td35{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 44px;vertical-align: bottom;}
        .td36{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 51px;vertical-align: bottom;}
        .td37{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 18px;vertical-align: bottom;}
        .td38{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 23px;vertical-align: bottom;}
        .td39{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 57px;vertical-align: bottom;}
        .td40{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 38px;vertical-align: bottom;}
        .td41{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 49px;vertical-align: bottom;}
        .td42{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 96px;vertical-align: bottom;}
        .td43{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 36px;vertical-align: bottom;}
        .td44{padding: 0px;margin: 0px;width: 76px;vertical-align: bottom;}
        .td45{padding: 0px;margin: 0px;width: 54px;vertical-align: bottom;}
        .td46{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 24px;vertical-align: bottom;}
        .td47{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 22px;vertical-align: bottom;}
        .td48{padding: 0px;margin: 0px;width: 189px;vertical-align: bottom;}
        .td49{padding: 0px;margin: 0px;width: 155px;vertical-align: bottom;}
        .td50{padding: 0px;margin: 0px;width: 43px;vertical-align: bottom;}
        .td51{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 13px;vertical-align: bottom;}
        .td52{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 25px;vertical-align: bottom;}
        .td53{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 21px;vertical-align: bottom;}
        .td54{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 117px;vertical-align: bottom;}
        .td55{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 40px;vertical-align: bottom;}
        .td56{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 72px;vertical-align: bottom;}
        .td57{border-left: #000000 1px solid;border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 39px;vertical-align: bottom;}
        .td58{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 116px;vertical-align: bottom;}
        .td59{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 8px;vertical-align: bottom;}
        .td60{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 117px;vertical-align: bottom;}
        .td61{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 33px;vertical-align: bottom;}
        .td62{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 64px;vertical-align: bottom;}
        .td63{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 94px;vertical-align: bottom;}
        .td64{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 40px;vertical-align: bottom;}
        .td65{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 56px;vertical-align: bottom;}
        .td66{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 48px;vertical-align: bottom;}
        .td67{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 95px;vertical-align: bottom;}
        .td68{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 111px;vertical-align: bottom;}
        .td69{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 88px;vertical-align: bottom;}
        .td70{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 37px;vertical-align: bottom;}
        .td71{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 42px;vertical-align: bottom;}
        .td72{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 86px;vertical-align: bottom;}
        .td73{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 71px;vertical-align: bottom;}
        .td74{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 69px;vertical-align: bottom;}
        .td75{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 42px;vertical-align: bottom;}
        .td76{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 4px;vertical-align: bottom;}
        .td77{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 83px;vertical-align: bottom;}
        .td78{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 3px;vertical-align: bottom;}
        .td79{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 71px;vertical-align: bottom;}
        .td80{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 39px;vertical-align: bottom;}
        .td81{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 116px;vertical-align: bottom;}
        .td82{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 83px;vertical-align: bottom;}
        .td83{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 94px;vertical-align: bottom;}
        .td84{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 95px;vertical-align: bottom;}
        .td85{border-left: #000000 1px solid;border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 39px;vertical-align: bottom;}
        .td86{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 8px;vertical-align: bottom;}
        .td87{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 33px;vertical-align: bottom;}
        .td88{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 64px;vertical-align: bottom;}
        .td89{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 56px;vertical-align: bottom;}
        .td90{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 48px;vertical-align: bottom;}
        .td91{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 3px;vertical-align: bottom;}
        .td92{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 37px;vertical-align: bottom;}
        .td93{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 44px;vertical-align: bottom;}
        .td94{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 50px;vertical-align: bottom;}
        .td95{padding: 0px;margin: 0px;width: 118px;vertical-align: bottom;}
        .td96{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 44px;vertical-align: bottom;}
        .td97{padding: 0px;margin: 0px;width: 149px;vertical-align: bottom;}
        .td98{padding: 0px;margin: 0px;width: 236px;vertical-align: bottom;}
        .td99{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 84px;vertical-align: bottom;}
        .td100{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 65px;vertical-align: bottom;}
        .td101{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 44px;vertical-align: bottom;}
        .td102{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 49px;vertical-align: bottom;}
        .td103{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 36px;vertical-align: bottom;}
        .td104{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 4px;vertical-align: bottom;}
        .td105{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 72px;vertical-align: bottom;}
        .td106{padding: 0px;margin: 0px;width: 352px;vertical-align: bottom;}
        .td107{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 145px;vertical-align: bottom;}
        .td108{padding: 0px;margin: 0px;width: 22px;vertical-align: bottom;}
        .td109{padding: 0px;margin: 0px;width: 302px;vertical-align: bottom;}
        .td110{padding: 0px;margin: 0px;width: 279px;vertical-align: bottom;}
        .td111{padding: 0px;margin: 0px;width: 89px;vertical-align: bottom;}
        .td112{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 41px;vertical-align: bottom;}
        .td113{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 51px;vertical-align: bottom;}
        .td114{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 185px;vertical-align: bottom;}
        .td115{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 96px;vertical-align: bottom;}
        .td116{padding: 0px;margin: 0px;width: 241px;vertical-align: bottom;}
        .td117{padding: 0px;margin: 0px;width: 321px;vertical-align: bottom;}
        .td118{padding: 0px;margin: 0px;width: 17px;vertical-align: bottom;}
        .td119{padding: 0px;margin: 0px;width: 113px;vertical-align: bottom;}
        .td120{padding: 0px;margin: 0px;width: 382px;vertical-align: bottom;}
        .td121{padding: 0px;margin: 0px;width: 111px;vertical-align: bottom;}
        .td122{padding: 0px;margin: 0px;width: 30px;vertical-align: bottom;}
        .td123{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 182px;vertical-align: bottom;}
        .td124{padding: 0px;margin: 0px;width: 7px;vertical-align: bottom;}
        .td125{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 6px;vertical-align: bottom;}
        .td126{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 97px;vertical-align: bottom;}
        .td127{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 11px;vertical-align: bottom;}
        .td128{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 17px;vertical-align: bottom;}
        .td129{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 113px;vertical-align: bottom;}
        .td130{padding: 0px;margin: 0px;width: 6px;vertical-align: bottom;}
        .td131{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 153px;vertical-align: bottom;}
        .td132{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 20px;vertical-align: bottom;}
        .td133{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 7px;vertical-align: bottom;}
        .td134{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 1px;vertical-align: bottom;}
        .td135{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 32px;vertical-align: bottom;}
        .td136{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 54px;vertical-align: bottom;}
        .td137{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 70px;vertical-align: bottom;}
        .td138{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 111px;vertical-align: bottom;}
        .td139{padding: 0px;margin: 0px;width: 195px;vertical-align: bottom;}
        .td140{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 153px;vertical-align: bottom;}
        .td141{padding: 0px;margin: 0px;width: 153px;vertical-align: bottom;}
        .td142{padding: 0px;margin: 0px;width: 20px;vertical-align: bottom;}
        .td143{padding: 0px;margin: 0px;width: 1px;vertical-align: bottom;}
        .td144{padding: 0px;margin: 0px;width: 32px;vertical-align: bottom;}
        .td145{padding: 0px;margin: 0px;width: 71px;vertical-align: bottom;}
        .td146{padding: 0px;margin: 0px;width: 11px;vertical-align: bottom;}
        .td147{padding: 0px;margin: 0px;width: 70px;vertical-align: bottom;}
        .td148{padding: 0px;margin: 0px;width: 141px;vertical-align: bottom;}
        .td149{padding: 0px;margin: 0px;width: 182px;vertical-align: bottom;}
        .td150{padding: 0px;margin: 0px;width: 16px;vertical-align: bottom;}
        .td151{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 5px;vertical-align: bottom;}
        .td152{padding: 0px;margin: 0px;width: 97px;vertical-align: bottom;}
        .td153{padding: 0px;margin: 0px;width: 219px;vertical-align: bottom;}
        .td154{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 548px;vertical-align: bottom;}
        .td155{padding: 0px;margin: 0px;width: 517px;vertical-align: bottom;}
        .td156{padding: 0px;margin: 0px;width: 395px;vertical-align: bottom;}
        .td157{padding: 0px;margin: 0px;width: 376px;vertical-align: bottom;}

        .tr0{height: 14px;}
        .tr1{height: 16px;}
        .tr2{height: 4px;}
        .tr3{height: 3px;}
        .tr4{height: 10px;}
        .tr5{height: 12px;}
        .tr6{height: 6px;}
        .tr7{height: 11px;}
        .tr8{height: 5px;}
        .tr9{height: 20px;}
        .tr10{height: 19px;}
        .tr11{height: 15px;}
        .tr12{height: 17px;}
        .tr13{height: 8px;}
        .tr14{height: 7px;}
        .tr15{height: 13px;}
        .tr16{height: 26px;}
        .tr17{height: 2px;}
        .tr18{height: 18px;}

        .t0{width: 1087px;font: 10px 'Times New Roman';}
        .t1{width: 1072px;margin-left: 2px;font: 12px 'Times New Roman';}

    </STYLE>
    {/literal}
<BODY>
<DIV id="page_1">
    <TABLE cellpadding=0 cellspacing=0 class="t0">
        <TR>
            <TD colspan=5 class="tr0 td0"><P class="p0 ft0"><A>Универсальный</A></P></TD>
            <TD class="tr0 td1"><P class="p1 ft0"><NOBR>Счет-фактура</NOBR> N</P></TD>
            <TD colspan="2" class="tr0 td2"><P contenteditable="true" class="p2 center">&nbsp;</P></TD>
            <TD class="tr0 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td5"><P class="p2 ft0">от</P></TD>
            <TD colspan="3" class="tr0 td22" style="border: none"><P class="p2 center" contenteditable="true">{date("Y.m.d" , strtotime($order.dateof))}</P></TD>
            <TD class="tr0 td6"><P class="p3 ft0">(1)</P></TD>
            <TD class="tr0 td7"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td9"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td13"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td14"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td15"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td16"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td17"><P class="p2 ft2">Приложение N 1</P></TD>
            <TD class="tr0 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=5 rowspan=3 class="tr1 td0"><P class="p0 ft3"><A>передаточный документ</A></P></TD>
            <TD class="tr2 td1"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr3 td19"><P class="p2 ft5">&nbsp;</P></TD>
            <TD class="tr3 td20"><P class="p2 ft5">&nbsp;</P></TD>
            <TD class="tr2 td4"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td21"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr3 td22"><P class="p2 ft5">&nbsp;</P></TD>
            <TD class="tr3 td20"><P class="p2 ft5">&nbsp;</P></TD>
            <TD class="tr3 td23"><P class="p2 ft5">&nbsp;</P></TD>
            <TD class="tr2 td6"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td7"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td8"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td9"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td10"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td11"><P class="p2 ft4">&nbsp;</P></TD>
            <TD rowspan=2 class="tr4 td12"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=5 rowspan=2 class="tr4 td24"><P class="p4 ft7">к постановлению Правительства Российской Федерации</P></TD>
            <TD class="tr2 td8"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td12"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td18"><P class="p2 ft4">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD rowspan=2 class="tr5 td1"><P class="p1 ft8">Исправление N</P></TD>
            <TD rowspan="2" colspan="2" class="tr6 td2"><P class="p2 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr6 td4"><P class="p2 ft9">&nbsp;</P></TD>
            <TD rowspan=2 class="tr5 td5"><P class="p2 ft8">от</P></TD>
            <TD rowspan=2 colspan="3" class="tr6 td3"><P class="p2 center" contenteditable="true"></P></TD>
            <TD rowspan=2 class="tr5 td6"><P class="p2 ft8">(1а)</P></TD>
            <TD class="tr6 td7"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td8"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td9"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td10"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td11"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td8"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td12"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td18"><P class="p2 ft9">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr6 td4"><P class="p2 ft9">&nbsp;</P></TD>

            <TD class="tr6 td7"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td8"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td9"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td10"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td11"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td12"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td13"><P class="p2 ft9">&nbsp;</P></TD>
            <TD colspan=4 rowspan=2 class="tr7 td25"><P class="p4 ft10">от 26 декабря 2011 года N 1137</P></TD>
            <TD class="tr6 td8"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td12"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td18"><P class="p2 ft9">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr8 td21"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td26"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td27"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td10"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td28"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td1"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr2 td29"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td30"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr8 td4"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td21"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr2 td31"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td30"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td32"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr8 td6"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td7"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td8"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td9"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td10"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td11"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td12"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td13"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td8"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td12"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td18"><P class="p2 ft11">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr9 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr9 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr9 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr9 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr9 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr9 td1"><P class="p1 ft12">Продавец</P></TD>
            <TD class="tr9 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr9 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr9 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan="14" class="tr10 td33"><P class="p2">{$CONFIG.firm_name}</P></TD>
            <TD colspan=2 class="tr9 td44"><P class="p5 ft13">(2)</P></TD>
            <TD class="tr9 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr9 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr9 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr11 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td38"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td1"><P class="p1 ft3">Адрес</P></TD>
            <TD class="tr11 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan="15" class="tr0 td33"><P class="p2">{$CONFIG.firm_address}</P></TD>
            <TD class="tr11 td17"><P class="p6 ft13">(2а)</P></TD>
            <TD class="tr11 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=2 class="tr12 td45"><P class="p0 ft13">Статус:</P></TD>
            <TD class="tr12 td46"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td47"><P class="p2 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr12 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr12 td1"><P class="p1 ft3">ИНН/КПП продавца</P></TD>
            <TD class="tr12 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr12 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr12 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan="15" class="tr1 td33"><P class="p2">{$CONFIG.firm_inn} / {$CONFIG.firm_kpp}</P></TD>

            <TD class="tr12 td17"><P class="p6 ft13">(2б)</P></TD>
            <TD class="tr12 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr12 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr12 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr11 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=4 class="tr11 td48"><P class="p1 ft3">Грузоотправитель и его адрес</P></TD>
            <TD colspan="14" class="tr0 td33"><P class="p2" contenteditable="true">&nbsp;</P></TD>
            <TD colspan=2 class="tr11 td44"><P class="p7 ft13">(3)</P></TD>
            <TD class="tr11 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=5 class="tr7 td0"><P class="p0 ft10">1 - <NOBR>счет-фактура</NOBR> и</P></TD>
            <TD colspan=3 class="tr7 td49"><P class="p1 ft14">Грузополучатель и его адрес</P></TD>
            <TD class="tr7 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan="14" class="tr7 td21"><P class="p2" contenteditable="true">&nbsp;</P></TD>
            <TD colspan=2 class="tr7 td44"><P class="p7 ft15">(4)</P></TD>
            <TD class="tr7 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=5 rowspan=2 class="tr7 td0"><P class="p0 ft10">передаточный документ (акт)</P></TD>
            <TD colspan=4 rowspan=3 class="tr10 td48"><P class="p1 ft3">К <NOBR>платежно-расчетному</NOBR> документу</P></TD>
            <TD class="tr2 td33"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td22"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td20"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td23"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td34"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td35"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td36"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td37"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td38"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td39"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td40"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td41"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td42"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr2 td43"><P class="p2 ft4">&nbsp;</P></TD>
            <TD class="tr8 td16"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td17"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td8"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td12"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td18"><P class="p2 ft11">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD rowspan=2 class="tr0 td5"><P class="p0 ft16">N</P></TD>
            <TD colspan="2" rowspan=2 style="border-bottom: #000000 1px solid;"><P class="p0 center" contenteditable="true"></P></TD>
            <TD rowspan=2 class="tr0 td4"><P class="p8 ft16">от</P></TD>
            <TD rowspan="2" colspan="10" class="tr6 td6 border-bottom"><P class="p2" contenteditable="true">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr0 td44"><P class="p5 ft16">(5)</P></TD>
            <TD class="tr6 td8"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td12"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td18"><P class="p2 ft9">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=5 class="tr13 td0"><P class="p0 ft17">2 - передаточный документ</P></TD>
            {*<TD colspan="10" class="tr14 td34"><P class="p2 ft18">&nbsp;</P></TD>*}
            <TD class="tr13 td8"><P class="p2 ft19">&nbsp;</P></TD>
            <TD class="tr13 td12"><P class="p2 ft19">&nbsp;</P></TD>
            <TD class="tr13 td18"><P class="p2 ft19">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=2 class="tr11 td45"><P class="p0 ft2">(акт)</P></TD>
            <TD class="tr11 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td1"><P class="p1 ft12">Покупатель</P></TD>
            <TD class="tr11 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan="14" class="tr0 td33"><P class="p2" contenteditable="true">{$user.surname} {$user.name} {$user.midname}</P></TD>
            <TD colspan=2 class="tr11 td44"><P class="p7 ft13">(6)</P></TD>
            <TD class="tr11 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr11 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td1"><P class="p1 ft3">Адрес</P></TD>
            <TD class="tr11 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan="14" class="tr0 td33"><P class="p2">{$address->getLineView(true)}</P></TD>

            <TD class="tr11 td16"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td17"><P class="p6 ft13">(6а)</P></TD>
            <TD class="tr11 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr11 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=3 class="tr11 td49"><P class="p1 ft3">ИНН/КПП покупателя</P></TD>
            <TD class="tr11 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan="14" class="tr0 td33"><P class="p2" contenteditable="true"></P></TD>
            <TD class="tr11 td16"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td17"><P class="p6 ft13">(6б)</P></TD>
            <TD class="tr11 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr15 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=3 class="tr15 td49"><P class="p1 ft0">Валюта: наименование, код</P></TD>
            <TD class="tr15 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td50"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td6"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td7"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td9"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td13"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td14"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td15"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr16 td44"><P class="p7 ft13">(7)</P></TD>
            <TD class="tr15 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr15 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=4 class="tr15 td48"><P class="p1 ft0">Идентификатор государственного</P></TD>
            <TD class="tr15 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td50"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td6"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td7"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td9"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td13"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td14"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td15"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr15 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td1"><P class="p1 ft0">контракта, договора</P></TD>
            <TD class="tr15 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td50"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td6"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td7"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td9"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td13"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td14"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td15"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td16"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td17"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr0 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=3 class="tr0 td49"><P class="p1 ft3">(соглашения)(при наличии)</P></TD>
            <TD class="tr0 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan="14" class="tr15 td33"><P class="p2" contenteditable="true"></P></TD>
            <TD class="tr0 td16"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td17"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr14 td33"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td51"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td52"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td38"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td53"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td54"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td19"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td20"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td23"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td33"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td22"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td20"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td23"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td34"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td35"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td36"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td37"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td38"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td39"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td40"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td41"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td42"><P class="p2 ft18">&nbsp;</P></TD>
            <TD colspan=2 class="tr14 td55"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td56"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td36"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td40"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr13 td18"><P class="p2 ft19">&nbsp;</P></TD>
        </TR>
    </TABLE>
    {$page = 1}
    {$counter = 1}
    {foreach $products_array as $product_arr}
    <table cellpadding=0 cellspacing=0 class="t0">
        <TR class="head">
            <TD class="tr7 td57"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td58"><P class="p9 ft20">Наименование товара</P></TD>
            <TD class="tr7 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td59"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=3 rowspan=2 class="tr12 td60"><P class="p10 ft3">Единица измерения</P></TD>
            <TD class="tr7 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td61"><P class="p2 ft1">&nbsp;</P></TD>
            <TD rowspan=2 class="tr12 td62"><P class="p5 ft21">Цена</P></TD>
            <TD colspan=2 class="tr7 td63"><P class="p11 ft20">Стоимость товаров</P></TD>
            <TD colspan=2 rowspan=2 class="tr12 td64"><P class="p5 ft21">В том</P></TD>
            <TD class="tr7 td65"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td66"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td67"><P class="p12 ft20">Стоимость товаров</P></TD>
            <TD colspan=3 class="tr7 td68"><P class="p13 ft20">Страна происхождения</P></TD>
            <TD colspan=2 rowspan=2 class="tr12 td69"><P class="p14 ft21">Регистрационный</P></TD>
            <TD class="tr7 td18 noborder"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr6 td57"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td26"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td27"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td10"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td28"><P class="p2 ft9">&nbsp;</P></TD>
            <TD rowspan=2 class="tr0 td58"><P class="p9 ft21">(описание выполненных</P></TD>
            <TD colspan=2 rowspan=2 class="tr0 td70"><P class="p9 ft3">Код</P></TD>
            <TD colspan=2 rowspan=2 class="tr0 td71"><P class="p9 ft21">Коли-</P></TD>
            <TD colspan=2 rowspan=2 class="tr0 td63"><P class="p11 ft21">(работ, услуг),</P></TD>
            <TD class="tr6 td65"><P class="p2 ft9">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr0 td72"><P class="p14 ft21">Сумма налога,</P></TD>
            <TD rowspan=2 class="tr0 td67"><P class="p12 ft21">(работ, услуг),</P></TD>
            <TD class="tr6 td15"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td16"><P class="p2 ft9">&nbsp;</P></TD>
            <TD rowspan=2 class="tr15 td73"><P class="p2 ft0">товара</P></TD>
            <TD class="tr6 td18"><P class="p2 ft9">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr13 td57"><P class="p2 ft19">&nbsp;</P></TD>
            <TD class="tr13 td26"><P class="p2 ft19">&nbsp;</P></TD>
            <TD colspan=3 rowspan=2 class="tr15 td74"><P class="p15 ft21">Код товара/</P></TD>
            <TD class="tr14 td23"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td33"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td75"><P class="p2 ft18">&nbsp;</P></TD>
            <TD rowspan=2 class="tr15 td62"><P class="p5 ft21">(тариф) за</P></TD>
            <TD colspan=2 rowspan=2 class="tr15 td64"><P class="p5 ft21">числе</P></TD>
            <TD rowspan=2 class="tr15 td65"><P class="p14 ft21">Налоговая</P></TD>
            <TD class="tr14 td43"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td76"><P class="p2 ft18">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr15 td69"><P class="p11 ft21">номер</P></TD>
            <TD class="tr13 td18"><P class="p2 ft19">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD rowspan=2 class="tr7 td57"><P class="p16 ft14">N п/п</P></TD>
            <TD class="tr8 td26"><P class="p2 ft11">&nbsp;</P></TD>
            <TD rowspan=2 class="tr7 td58"><P class="p17 ft14">работ, оказанных</P></TD>
            <TD colspan=2 rowspan=2 class="tr7 td70"><P class="p16 ft14">вида</P></TD>
            <TD class="tr8 td61"><P class="p2 ft11">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr7 td77"><P class="p9 ft20">условное</P></TD>
            <TD colspan=2 rowspan=2 class="tr7 td71"><P class="p18 ft14">чество</P></TD>
            <TD colspan=2 rowspan=2 class="tr7 td63"><P class="p16 ft14">имущественных</P></TD>
            <TD colspan=2 rowspan=2 class="tr7 td72"><P class="p16 ft14">предъявляемая</P></TD>
            <TD rowspan=2 class="tr7 td67"><P class="p19 ft14">имущественных</P></TD>
            <TD class="tr8 td15"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td78"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td79"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td18"><P class="p2 ft11">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr6 td26"><P class="p2 ft9">&nbsp;</P></TD>
            <TD colspan=3 rowspan=2 class="tr5 td74"><P class="p15 ft21">работ, услуг</P></TD>
            <TD class="tr6 td61"><P class="p2 ft9">&nbsp;</P></TD>
            <TD rowspan=2 class="tr5 td62"><P class="p5 ft21">единицу</P></TD>
            <TD colspan=2 rowspan=2 class="tr5 td64"><P class="p5 ft21">сумма</P></TD>
            <TD rowspan=2 class="tr5 td65"><P class="p14 ft21">ставка</P></TD>
            <TD rowspan=2 class="tr5 td15"><P class="p9 ft21">Цифро-</P></TD>
            <TD class="tr6 td78"><P class="p2 ft9">&nbsp;</P></TD>
            <TD rowspan=2 class="tr5 td79"><P class="p14 ft21">Краткое</P></TD>
            <TD colspan=2 rowspan=2 class="tr5 td69"><P class="p11 ft21">таможенной</P></TD>
            <TD class="tr6 td18"><P class="p2 ft9">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr6 td57"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td26"><P class="p2 ft9">&nbsp;</P></TD>
            <TD rowspan=2 class="tr5 td58"><P class="p20 ft21">услуг), имущественного</P></TD>
            <TD colspan=2 rowspan=2 class="tr5 td70"><P class="p9 ft21">товара</P></TD>
            <TD rowspan=2 class="tr5 td61"><P class="p21 ft8">код</P></TD>
            <TD colspan=2 rowspan=2 class="tr5 td77"><P class="p20 ft21">обозначение</P></TD>
            <TD colspan=2 rowspan=2 class="tr5 td71"><P class="p9 ft21">(объем)</P></TD>
            <TD colspan=2 rowspan=2 class="tr5 td63"><P class="p11 ft21">прав без налога -</P></TD>
            <TD colspan=2 rowspan=2 class="tr5 td72"><P class="p14 ft21">покупателю</P></TD>
            <TD rowspan=2 class="tr5 td67"><P class="p12 ft21">прав с налогом -</P></TD>
            <TD class="tr6 td78"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td18"><P class="p2 ft9">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr6 td57"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td26"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td27"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td10"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td28"><P class="p2 ft9">&nbsp;</P></TD>
            <TD rowspan=2 class="tr5 td62"><P class="p5 ft21">измерения</P></TD>
            <TD colspan=2 rowspan=2 class="tr5 td64"><P class="p5 ft21">акциза</P></TD>
            <TD class="tr6 td65"><P class="p2 ft9">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr5 td80"><P class="p5 ft21">вой код</P></TD>
            <TD rowspan=2 class="tr5 td79"><P class="p14 ft21">наименование</P></TD>
            <TD colspan=2 rowspan=2 class="tr5 td69"><P class="p14 ft21">декларации</P></TD>
            <TD class="tr6 td18"><P class="p2 ft9">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr6 td57"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td26"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td27"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td10"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td28"><P class="p2 ft9">&nbsp;</P></TD>
            <TD rowspan=2 class="tr15 td81"><P class="p9 ft21">права</P></TD>
            <TD class="tr6 td2"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td59"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td61"><P class="p2 ft9">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr15 td82"><P class="p9 ft21">(национальное)</P></TD>
            <TD class="tr6 td3"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td61"><P class="p2 ft9">&nbsp;</P></TD>
            <TD colspan=2 rowspan=2 class="tr15 td83"><P class="p11 ft21">всего</P></TD>
            <TD class="tr6 td65"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td12"><P class="p2 ft9">&nbsp;</P></TD>
            <TD class="tr6 td66"><P class="p2 ft9">&nbsp;</P></TD>
            <TD rowspan=2 class="tr15 td84"><P class="p12 ft21">всего</P></TD>
            <TD class="tr6 td18"><P class="p2 ft9">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr14 td85"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td51"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td52"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td38"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td53"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td19"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td86"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td87"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td20"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td87"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td88"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td37"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td47"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td89"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td40"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td90"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td43"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td91"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td73"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td36"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr14 td92"><P class="p2 ft18">&nbsp;</P></TD>
            <TD class="tr13 td18"><P class="p2 ft19">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr5 td57"><P class="p22 ft8">А</P></TD>
            <TD class="tr5 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=2 class="tr5 td93"><P class="p23 ft21">Б</P></TD>
            <TD class="tr5 td58"><P class="p9 ft21">1</P></TD>
            <TD colspan=2 class="tr5 td70"><P class="p9 ft21">1а</P></TD>
            <TD class="tr5 td61"><P class="p10 ft8">2</P></TD>
            <TD colspan=2 class="tr5 td77"><P class="p9 ft21">2а</P></TD>
            <TD colspan=2 class="tr5 td71"><P class="p9 ft21">3</P></TD>
            <TD class="tr5 td62"><P class="p5 ft8">4</P></TD>
            <TD colspan=2 class="tr5 td63"><P class="p24 ft8">5</P></TD>
            <TD colspan=2 class="tr5 td64"><P class="p25 ft8">6</P></TD>
            <TD class="tr5 td65"><P class="p14 ft21">7</P></TD>
            <TD colspan=2 class="tr5 td72"><P class="p26 ft8">8</P></TD>
            <TD class="tr5 td67"><P class="p27 ft8">9</P></TD>
            <TD class="tr5 td15"><P class="p28 ft8">10</P></TD>
            <TD class="tr5 td78"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td79"><P class="p14 ft21">10а</P></TD>
            <TD class="tr5 td8"><P class="p29 ft8">11</P></TD>
            <TD class="tr5 td70"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr17 td85"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td51"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td52"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td38"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td53"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td81"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td19"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td86"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td87"><P class="p2 ft22">&nbsp;</P></TD>
            <TD colspan=2 class="tr17 td82"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td20"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td87"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td88"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td35"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td94"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td37"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td47"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td89"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td40"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td90"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td84"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td43"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td91"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td73"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td36"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr17 td92"><P class="p2 ft22">&nbsp;</P></TD>
            <TD class="tr3 td18"><P class="p2 ft5">&nbsp;</P></TD>
        </TR>
        {foreach from=$product_arr key=n item=item name=foo}
        {assign var=product value=$products[$n].product}
            <TR>
                <TD class="tr0 td85"><P class="p2 center">{$counter}</P></TD>
                <TD colspan="4" class="tr0 td51 border-right"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
                <TD class="tr0 td81" style="max-width: 114px;"><P class="p2" style="white-space: pre-wrap;">{$item.cartitem.title}</P></TD>
                {$unit = $product->getUnit()}
                <TD colspan="2" class="tr0 td19 border-right"><P class="p2 center" contenteditable="true">&nbsp;</P></TD>
                <TD class="tr0 td87"><P class="p2 center" contenteditable="true">{$unit.code}</P></TD>
                <TD colspan="2" class="tr0 td33 border-right"><P class="p2 center" contenteditable="true">{$unit.stitle}</P></TD>
                <TD colspan="2" class="tr0 td20 border-right"><P class="p2 center">{$item.cartitem.amount}</P></TD>
                <TD class="tr0 td88"><P class="p2 center">{$item.single_cost_noformat}</P></TD>
                <TD colspan="2" class="tr0 td35 border-right"><P class="p2 center">{$taxes.items[$n].subtotal}</P></TD>
                <TD colspan="2" class="tr0 td37 border-right"><P class="p2 center" contenteditable="true">&nbsp;</P></TD>
                <TD class="tr0 td89"><P class="p2 center">{round($taxes.items[$n].taxes.rate, 2)}</P></TD>
                <TD colspan="2" class="tr0 td40 border-right"><P class="p2 center">{$taxes.items[$n].taxes.value}</P></TD>
                <TD class="tr0 td84"><P class="p2 center">{$taxes.items[$n].taxes.cost}</P></TD>
                <TD colspan="2" class="tr0 td43 border-right"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
                <TD class="tr0 td73"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
                <TD colspan="2" class="tr0 td36 border-right"><P class="p2 center" contenteditable="true"></P></TD>
                <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
            </TR>
            {$counter = $counter + 1}
        {/foreach}
        {if count($products_array) != $page}
            </TABLE>
            <div class="page-break" style="margin-bottom: 20px"></div>
        {/if}
        {$page = $page + 1}
    {/foreach}
    {if $taxes.delivery}
        <TR>
            <TD class="tr0 td85"><P class="p2 center">{$counter }</P></TD>
            <TD colspan="4" class="tr0 td51 border-right"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
            <TD class="tr0 td81" style="max-width: 114px;"><P class="p2" style="white-space: pre-wrap;">{$taxes.delivery.title}</P></TD>
            <TD colspan="2" class="tr0 td19 border-right"><P class="p2 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr0 td87"><P class="p2 center" contenteditable="true"></P></TD>
            <TD colspan="2" class="tr0 td33 border-right"><P class="p2 center" contenteditable="true"></P></TD>
            <TD colspan="2" class="tr0 td20 border-right"><P class="p2 center" contenteditable="true">1</P></TD>
            <TD class="tr0 td88"><P class="p2 center">{$taxes.delivery.subtotal}</P></TD>
            <TD colspan="2" class="tr0 td35 border-right"><P class="p2 center">{$taxes.delivery.subtotal}</P></TD>
            <TD colspan="2" class="tr0 td37 border-right"><P class="p2 center" contenteditable="true">&nbsp;</P></TD>
            <TD class="tr0 td89"><P class="p2 center">{round($taxes.delivery.tax_rate, 2)}</P></TD>
            <TD colspan="2" class="tr0 td40 border-right"><P class="p2 center">{$taxes.delivery.tax}</P></TD>
            <TD class="tr0 td84"><P class="p2 center">{$taxes.delivery.cost}</P></TD>
            <TD colspan="2" class="tr0 td43 border-right"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
            <TD class="tr0 td73"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
            <TD colspan="2" class="tr0 td36 border-right"><P class="p2 center" contenteditable="true"></P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
    {/if}
        <TR>
            <TD class="tr4 td57"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td26"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td27"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td10"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td28"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td1"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td2"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td3"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=3 class="tr4 td95"><P class="p2 ft23">Всего к оплате</P></TD>
            <TD class="tr4 td3"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td4"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td62"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=2 class="tr4 td63"><P class="p30 ft24">X</P></TD>
            <TD class="tr4 td9"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td10"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td65"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan="2" class="tr4 td12"><P class="p2 center border-right">{$all_taxes}</P></TD>
            <TD class="tr4 td67"><P class="p2 center">{$order_data.total_cost}</P></TD>
            <TD class="tr4 td15"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td78"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td79"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td8"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td70"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td18"><P class="p2 ft6">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr8 td85"><P class="p2 ft11">&nbsp;</P></TD>
            <TD colspan=2 class="tr8 td40"><P class="p2 ft11">&nbsp;</P></TD>
            <TD colspan=2 class="tr8 td96"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td54"><P class="p2 ft11">&nbsp;</P></TD>
            <TD colspan=2 class="tr8 td40"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td23"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td33"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td22"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td20"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td23"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td88"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td35"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td94"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td37"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td38"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td89"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td40"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td90"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td84"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td43"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td91"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td73"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td36"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr8 td92"><P class="p2 ft11">&nbsp;</P></TD>
            <TD class="tr6 td18"><P class="p2 ft9">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=5 class="tr18 td0"><P class="p0 ft13">Документ составлен на</P></TD>
            <TD colspan=4 class="tr18 td48"><P class="p31 ft13">Руководитель организации или</P></TD>
            <TD class="tr18 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td50"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td6"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td7"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=4 class="tr18 td97"><P class="p16 ft13">Главный бухгалтер</P></TD>
            <TD class="tr18 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td13"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td14"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td15"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td16"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td17"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr0 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=3 class="tr0 td49"><P class="p31 ft16">иное уполномоченное лицо</P></TD>
            <TD class="tr0 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td50"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td6"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td7"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=6 class="tr0 td98"><P class="p16 ft16">или иное уполномоченное лицо</P></TD>
            <TD class="tr0 td14"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td15"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td16"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td17"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan="3" class="tr11 td33"><P class="p2 center" contenteditable="true">&nbsp;</P></TD>
            <TD colspan=2 class="tr1 td93"><P class="p2 ft13">листах</P></TD>
            <TD class="tr1 td1"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=2 class="tr11 td99"><P class="p9 ft2">(подпись)</P></TD>
            <TD class="tr1 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td32"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td100"><P class="p32 ft2">(ф.и.о.)</P></TD>
            <TD class="tr11 td101"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td9"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td102"><P class="p18 ft2">(подпись)</P></TD>
            <TD class="tr1 td14"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td103"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td104"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td105"><P class="p2 ft2">(ф.и.о.)</P></TD>
            <TD class="tr1 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr15 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td1"><P class="p31 ft25">Индивидуальный</P></TD>
            <TD class="tr15 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td50"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td6"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td7"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td9"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td13"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td14"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td15"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td16"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td17"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr11 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td1"><P class="p31 ft13">предприниматель</P></TD>
            <TD class="tr11 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td33"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td22"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td23"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td34"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td35"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td9"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td38"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td39"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td40"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td41"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td42"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td43"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td76"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td56"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr4 td21"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td26"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td27"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td10"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td28"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td1"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td2"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td3"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td4"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=2 class="tr4 td5"><P class="p9 ft7">(подпись)</P></TD>
            <TD class="tr4 td3"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td4"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td6"><P class="p32 ft7">(ф.и.о.)</P></TD>
            <TD class="tr4 td7"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td8"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td9"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td10"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=7 class="tr4 td106"><P class="p33 ft7">(реквизиты свидетельства о государственной регистрации индивидуального</P></TD>
            <TD class="tr4 td8"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td12"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td18"><P class="p2 ft6">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr15 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td28"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td54"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td19"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td20"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td23"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td33"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td22"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td20"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td23"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td34"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td35"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td36"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td37"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td38"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td39"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td40"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=2 class="tr5 td107"><P class="p34 ft26">предпринимателя)</P></TD>
            <TD class="tr5 td43"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td76"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr5 td56"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr15 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr7 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td108"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td1"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td50"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td6"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td7"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td9"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td13"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=7 class="tr7 td109"><P class="p35 ft26">Приложение N 1 к письму ФНС России от 21.10.2013 N <NOBR>ММВ-20-3/96@</NOBR></P></TD>
        </TR>
        <TR>
            <TD colspan=8 class="tr18 td110"><P class="p36 ft13">Основание передачи (сдачи) / получения (приемки)</P></TD>
            <TD class="tr18 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td50"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td6"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td7"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td9"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td13"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td14"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td15"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td16"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr18 td17"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=2 class="tr18 td111"><P class="p37 ft13">[8]</P></TD>
            <TD class="tr18 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr7 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td26"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td27"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td108"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td1"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr4 td30"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td32"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td112"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td31"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td30"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td32"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td100"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td101"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td113"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=5 class="tr4 td114"><P class="p2 ft7">(договор; доверенность и др.)</P></TD>
            <TD class="tr4 td115"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td103"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td104"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td105"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td113"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr7 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr7 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD colspan=6 class="tr11 td116"><P class="p36 ft13">Данные о транспортировке и грузе</P></TD>
            <TD class="tr11 td2"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td21"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td50"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td3"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td4"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td6"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td7"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td8"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td9"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td10"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td12"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td13"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td14"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td15"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td16"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td17"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=2 class="tr11 td111"><P class="p37 ft13">[9]</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
    </TABLE>
    <P class="p38 ft10">(транспортная накладная, поручение экспедитору, экспедиторская / складская расписка и др. / масса нетто/ брутто груза, если не приведены ссылки на транспортные документы, содержащие эти сведения)</P>
    <TABLE cellpadding=0 cellspacing=0 class="t1">
        <TR>
            <TD colspan=7 class="tr0 td117"><P class="p0 ft16">Товар (груз) передал / услуги, результаты работ, права сдал</P></TD>
            <TD class="tr0 td118"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td119"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td64"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=12 class="tr0 td120"><P class="p8 ft16">Товар (груз) получил / услуги, результаты работ, права принял</P></TD>
            <TD class="tr0 td121"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td122"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr0 td123"><P class="p2 center">&nbsp;</P></TD>
            <TD class="tr0 td51"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td124"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td125"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td126"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td127"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td128"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td39"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td129"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td64"><P class="p39 ft13">[10]</P></TD>
            <TD class="tr11 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td131"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td132"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td133"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td134"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td135"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td136"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td128"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td127"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td137"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td138"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td122"><P class="p40 ft13">[15]</P></TD>
        </TR>
        <TR>
            <TD colspan=2 class="tr4 td139"><P class="p20 ft26">(должность)</P></TD>
            <TD class="tr4 td124"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td130"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=3 class="tr4 td119"><P class="p41 ft7">(подпись)</P></TD>
            <TD class="tr4 td118"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td11"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=2 class="tr4 td140"><P class="p42 ft7">(ф.и.о.)</P></TD>
            <TD class="tr4 td130"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td141"><P class="p43 ft7">(должность)</P></TD>
            <TD class="tr4 td142"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td124"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td143"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td130"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td144"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=2 class="tr4 td145"><P class="p2 ft7">(подпись)</P></TD>
            <TD class="tr4 td18"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td146"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td147"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=2 class="tr4 td148"><P class="p44 ft7">(ф.и.о.)</P></TD>
        </TR>
        <TR>
            <TD class="tr1 td149"><P class="p0 ft13">Дата отгрузки, передачи (сдачи) "</P></TD>
            <TD colspan="2" class="tr11 td51"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td130"><P class="p40 ft13">"</P></TD>
            <TD class="tr11 td126"><P class="p2 center"  contenteditable="true"></P></TD>
            <TD colspan=2 class="tr1 td150"><P class="p45 ft13">20</P></TD>
            <TD class="tr11 td128"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td11"><P class="p0 ft13">г.</P></TD>
            <TD colspan=2 class="tr1 td140"><P class="p39 ft13">[11]</P></TD>
            <TD class="tr1 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td141"><P class="p2 ft13">Дата получения (приемки) "</P></TD>
            <TD class="tr11 td132"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td124"><P class="p40 ft13">"</P></TD>
            <TD class="tr11 td134"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan="3" class="tr11 td125"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
            <TD class="tr1 td118"><P class="p46 ft13">20</P></TD>
            <TD colspan="2" class="tr11 td151"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>

            <TD class="tr1 td147"><P class="p47 ft13">г.</P></TD>
            <TD colspan=2 class="tr1 td148"><P class="p40 ft13">[16]</P></TD>
        </TR>
        <TR>
            <TD colspan=2 class="tr5 td139"><P class="p0 ft27">Иные сведения об отгрузке, передаче</P></TD>
            <TD class="tr5 td124"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td130"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td152"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td18"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td146"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td118"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td11"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td119"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td64"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td130"><P class="p2 ft28">&nbsp;</P></TD>
            <TD colspan=6 class="tr5 td153"><P class="p2 ft27">Иные сведения о получении, приемке</P></TD>
            <TD class="tr5 td45"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td118"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td18"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td146"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td147"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td121"><P class="p2 ft28">&nbsp;</P></TD>
            <TD class="tr5 td122"><P class="p2 ft28">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr0 td123"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td51"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td133"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td125"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td126"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td151"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td127"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td128"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td39"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td129"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td64"><P class="p39 ft13">[12]</P></TD>
            <TD class="tr11 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td131"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td132"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td133"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td134"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td125"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td135"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td136"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td128"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td151"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td127"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td137"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td138"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td122"><P class="p40 ft13">[17]</P></TD>
        </TR>
        <TR>
            <TD colspan=11 class="tr4 td154"><P class="p48 ft7">(ссылки на неотъемлемые приложения, сопутствующие документы, иные документы и т.п.)</P></TD>
            <TD class="tr4 td130"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=13 class="tr4 td155"><P class="p49 ft7">(информация о наличии/отсутствии претензии; ссылки на неотъемлемые приложения, и другие документы и т.п.)</P></TD>
        </TR>
        <TR>
            <TD colspan=9 class="tr0 td156"><P class="p0 ft16">Ответственный за правильность оформления факта хозяйственной жизни</P></TD>
            <TD class="tr0 td119"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td64"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=13 class="tr0 td155"><P class="p2 ft16">Ответственный за правильность оформления факта хозяйственной жизни</P></TD>
        </TR>
        <TR>
            <TD class="tr11 td123"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td51"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td124"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td125"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td126"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td18"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td127"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td128"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td39"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td129"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td64"><P class="p39 ft13">[13]</P></TD>
            <TD class="tr1 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td131"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td132"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td133"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td134"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td135"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td136"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td128"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td18"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td127"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td137"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td138"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr1 td122"><P class="p40 ft13">[18]</P></TD>
        </TR>
        <TR>
            <TD colspan=2 class="tr4 td139"><P class="p20 ft26">(должность)</P></TD>
            <TD class="tr4 td124"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td130"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=3 class="tr4 td119"><P class="p41 ft7">(подпись)</P></TD>
            <TD class="tr4 td118"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td11"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=2 class="tr4 td140"><P class="p42 ft7">(ф.и.о.)</P></TD>
            <TD class="tr4 td130"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td141"><P class="p43 ft7">(должность)</P></TD>
            <TD class="tr4 td142"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td124"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td143"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td130"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td144"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=2 class="tr4 td145"><P class="p2 ft7">(подпись)</P></TD>
            <TD class="tr4 td18"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td146"><P class="p2 ft6">&nbsp;</P></TD>
            <TD class="tr4 td147"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=2 class="tr4 td148"><P class="p44 ft7">(ф.и.о.)</P></TD>
        </TR>
        <TR>
            <TD colspan=11 class="tr0 td154"><P class="p0 ft16">Наименование экономического субъекта – составителя документа (в т.ч. комиссионера / агента)</P></TD>
            <TD class="tr0 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD colspan=11 class="tr0 td157"><P class="p2 ft16">Наименование экономического субъекта - составителя документа</P></TD>
            <TD class="tr0 td121"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td122"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
        <TR>
            <TD class="tr0 td123"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td51"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td133"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td125"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td126"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td151"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td127"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td128"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td39"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td129"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td64"><P class="p39 ft13">[14]</P></TD>
            <TD class="tr11 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td131"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td132"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td133"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td134"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td125"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td135"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td136"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td128"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td151"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td127"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td137"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr0 td138"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td122"><P class="p40 ft13">[19]</P></TD>
        </TR>
        <TR>
            <TD colspan=11 class="tr4 td154"><P class="p48 ft7">(может не заполняться при проставлении печати в М.П., может быть указан ИНН / КПП)</P></TD>
            <TD class="tr4 td130"><P class="p2 ft6">&nbsp;</P></TD>
            <TD colspan=13 class="tr4 td155"><P class="p43 ft7">(может не заполняться при проставлении печати в М.П., может быть указан ИНН / КПП)</P></TD>
        </TR>
        <TR>
            <TD colspan=2 class="tr11 td139"><P class="p20 ft3">М.П.</P></TD>
            <TD class="tr11 td124"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td152"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td146"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td118"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td11"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td119"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td64"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td141"><P class="p50 ft13">М.П.</P></TD>
            <TD class="tr11 td142"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td124"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td143"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td130"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td144"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td45"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td118"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td18"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td146"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td147"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td121"><P class="p2 ft1">&nbsp;</P></TD>
            <TD class="tr11 td122"><P class="p2 ft1">&nbsp;</P></TD>
        </TR>
    </TABLE>
</DIV>
</BODY>
</HTML>
