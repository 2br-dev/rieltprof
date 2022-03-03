{$user = $order->getUser()}
{$address = $order->getAddress()}
{$cart = $order->getCart()}
{$order_data = $cart->getOrderData(true, false)}
{$products = $cart->getProductItems()}

<!DOCTYPE HTML>
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <META http-equiv="X-UA-Compatible" content="IE=8">
    <TITLE>Счет фактура</TITLE>
    <STYLE type="text/css">
        {literal}
        body {margin-top: 0px;margin-left: 0px;}
        .head td{
            border-top: 1px solid black;
        }
        #page_1 {position:relative; overflow: hidden;margin: 58px 0px 48px 19px;padding: 0px;border: none;width: 1103px;}
        #page_1 #id_1 {border:none;margin: 0px 0px 0px 0px;padding: 0px;border:none;width: 1103px;overflow: hidden;}
        #page_1 #id_2 {border:none;margin: 0px 0px 0px 244px;padding: 0px;border:none;width: 788px;overflow: hidden;}

        .border-right{
            border-right: #000000 1px solid;
        }
        .border-bottom{
            border-bottom: #000000 1px solid;
        }
        .center{
            text-align: center!important;
        }
        @media print {
            .page-break { page-break-after: always;}
        }
        .ft0{font: 13px 'Times New Roman';line-height: 15px;}
        .ft1{font: 1px 'Times New Roman';line-height: 1px;}
        .ft2{font: 16px 'Times New Roman';line-height: 19px;}
        .ft3{font: 12px 'Times New Roman';line-height: 14px;}
        .ft4{font: 12px 'Times New Roman';line-height: 15px;}
        .ft5{font: 1px 'Times New Roman';line-height: 8px;}
        .ft6{font: 11px 'Times New Roman';line-height: 14px;}
        .ft7{font: 1px 'Times New Roman';line-height: 10px;}
        .ft8{font: 1px 'Times New Roman';line-height: 11px;}
        .ft9{font: 12px 'Times New Roman';line-height: 12px;}
        .ft10{font: 1px 'Times New Roman';line-height: 4px;}
        .ft11{font: 1px 'Times New Roman';line-height: 9px;}
        .ft12{font: 1px 'Times New Roman';line-height: 7px;}
        .ft13{font: 1px 'Times New Roman';line-height: 2px;}
        .ft14{font: 15px 'Times New Roman';line-height: 17px;}

        .p0{text-align: left;padding-left: 842px;margin-top: 0px;margin-bottom: 0px;}
        .p1{text-align: left;padding-left: 842px;padding-right: 77px;margin-top: 0px;margin-bottom: 0px;}
        .p2{text-align: left;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p3{text-align: left;padding-left: 35px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p4{text-align: left;padding-left: 9px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p5{text-align: right;padding-right: 10px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p6{text-align: right;padding-right: 11px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p7{text-align: left;padding-left: 3px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p8{text-align: right;padding-right: 8px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p9{text-align: center;padding-right: 30px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p10{text-align: center;padding-right: 19px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p11{text-align: center;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p12{text-align: center;padding-left: 2px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p13{text-align: center;padding-right: 20px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p14{text-align: center;padding-left: 1px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p15{text-align: center;padding-right: 1px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p16{text-align: center;padding-right: 38px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p17{text-align: center;padding-right: 5px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p18{text-align: center;padding-right: 6px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p19{text-align: left;padding-left: 8px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p20{text-align: left;padding-left: 11px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p21{text-align: left;padding-left: 17px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p22{text-align: left;padding-left: 24px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p23{text-align: left;padding-left: 4px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p24{text-align: center;padding-right: 4px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p25{text-align: center;padding-right: 7px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p26{text-align: left;padding-left: 68px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p27{text-align: left;padding-left: 2px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p28{text-align: center;padding-right: 3px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p29{text-align: center;padding-left: 13px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p30{text-align: right;padding-right: 44px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p31{text-align: center;padding-left: 26px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p32{text-align: right;padding-right: 31px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p33{text-align: right;padding-right: 25px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p34{text-align: left;padding-left: 32px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p35{text-align: left;padding-left: 21px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p36{text-align: left;padding-left: 27px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p37{text-align: left;padding-left: 48px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p38{text-align: left;padding-left: 3px;margin-top: 20px;margin-bottom: 0px;}
        .p39{text-align: left;padding-left: 74px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
        .p40{text-align: left;padding-left: 90px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}

        .td0{padding: 0px;margin: 0px;width: 52px;vertical-align: bottom;}
        .td1{padding: 0px;margin: 0px;width: 25px;vertical-align: bottom;}
        .td2{padding: 0px;margin: 0px;width: 13px;vertical-align: bottom;}
        .td3{padding: 0px;margin: 0px;width: 192px;vertical-align: bottom;}
        .td4{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 33px;vertical-align: bottom;}
        .td5{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 12px;vertical-align: bottom;}
        .td6{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 1px;vertical-align: bottom;}
        .td7{padding: 0px;margin: 0px;width: 37px;vertical-align: bottom;}
        .td8{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 14px;vertical-align: bottom;}
        .td9{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 19px;vertical-align: bottom;}
        .td10{padding: 0px;margin: 0px;width: 19px;vertical-align: bottom;}
        .td11{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 58px;vertical-align: bottom;}
        .td12{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 38px;vertical-align: bottom;}
        .td13{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 26px;vertical-align: bottom;}
        .td14{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 32px;vertical-align: bottom;}
        .td15{padding: 0px;margin: 0px;width: 38px;vertical-align: bottom;}
        .td16{padding: 0px;margin: 0px;width: 77px;vertical-align: bottom;}
        .td17{padding: 0px;margin: 0px;width: 44px;vertical-align: bottom;}
        .td18{padding: 0px;margin: 0px;width: 78px;vertical-align: bottom;}
        .td19{padding: 0px;margin: 0px;width: 26px;vertical-align: bottom;}
        .td20{padding: 0px;margin: 0px;width: 83px;vertical-align: bottom;}
        .td21{padding: 0px;margin: 0px;width: 6px;vertical-align: bottom;}
        .td22{padding: 0px;margin: 0px;width: 71px;vertical-align: bottom;}
        .td23{padding: 0px;margin: 0px;width: 172px;vertical-align: bottom;}
        .td24{padding: 0px;margin: 0px;width: 14px;vertical-align: bottom;}
        .td25{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 6px;vertical-align: bottom;}
        .td26{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 7px;vertical-align: bottom;}
        .td27{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 71px;vertical-align: bottom;}
        .td28{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 15px;vertical-align: bottom;}
        .td29{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 3px;vertical-align: bottom;}
        .td30{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 39px;vertical-align: bottom;}
        .td31{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 5px;vertical-align: bottom;}
        .td32{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 31px;vertical-align: bottom;}
        .td33{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 96px;vertical-align: bottom;}
        .td34{padding: 0px;margin: 0px;width: 148px;vertical-align: bottom;}
        .td35{padding: 0px;margin: 0px;width: 218px;vertical-align: bottom;}
        .td36{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 44px;vertical-align: bottom;}
        .td37{padding: 0px;margin: 0px;width: 212px;vertical-align: bottom;}
        .td38{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 50px;vertical-align: bottom;}
        .td39{padding: 0px;margin: 0px;width: 282px;vertical-align: bottom;}
        .td40{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 84px;vertical-align: bottom;}
        .td41{padding: 0px;margin: 0px;width: 90px;vertical-align: bottom;}
        .td42{padding: 0px;margin: 0px;width: 169px;vertical-align: bottom;}
        .td43{padding: 0px;margin: 0px;width: 262px;vertical-align: bottom;}
        .td44{padding: 0px;margin: 0px;width: 7px;vertical-align: bottom;}
        .td45{padding: 0px;margin: 0px;width: 12px;vertical-align: bottom;}
        .td46{padding: 0px;margin: 0px;width: 1px;vertical-align: bottom;}
        .td47{padding: 0px;margin: 0px;width: 31px;vertical-align: bottom;}
        .td48{padding: 0px;margin: 0px;width: 58px;vertical-align: bottom;}
        .td49{padding: 0px;margin: 0px;width: 70px;vertical-align: bottom;}
        .td50{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 52px;vertical-align: bottom;}
        .td51{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 25px;vertical-align: bottom;}
        .td52{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 13px;vertical-align: bottom;}
        .td53{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 7px;vertical-align: bottom;}
        .td54{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 6px;vertical-align: bottom;}
        .td55{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 39px;vertical-align: bottom;}
        .td56{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 5px;vertical-align: bottom;}
        .td57{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 14px;vertical-align: bottom;}
        .td58{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 26px;vertical-align: bottom;}
        .td59{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 12px;vertical-align: bottom;}
        .td60{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 1px;vertical-align: bottom;}
        .td61{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 31px;vertical-align: bottom;}
        .td62{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 19px;vertical-align: bottom;}
        .td63{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 58px;vertical-align: bottom;}
        .td64{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 38px;vertical-align: bottom;}
        .td65{border-top: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 32px;vertical-align: bottom;}
        .td66{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 77px;vertical-align: bottom;}
        .td67{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 78px;vertical-align: bottom;}
        .td68{border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 83px;vertical-align: bottom;}
        .td69{border-left: #000000 1px solid;padding: 0px;margin: 0px;width: 51px;vertical-align: bottom;}
        .td70{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 51px;vertical-align: bottom;}
        .td71{padding: 0px;margin: 0px;width: 15px;vertical-align: bottom;}
        .td72{padding: 0px;margin: 0px;width: 3px;vertical-align: bottom;}
        .td73{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 84px;vertical-align: bottom;}
        .td74{padding: 0px;margin: 0px;width: 5px;vertical-align: bottom;}
        .td75{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 25px;vertical-align: bottom;}
        .td76{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 13px;vertical-align: bottom;}
        .td77{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 76px;vertical-align: bottom;}
        .td78{padding: 0px;margin: 0px;width: 32px;vertical-align: bottom;}
        .td79{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 37px;vertical-align: bottom;}
        .td80{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 77px;vertical-align: bottom;}
        .td81{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 146px;vertical-align: bottom;}
        .td82{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 70px;vertical-align: bottom;}
        .td83{border-left: #000000 1px solid;border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 140px;vertical-align: bottom;}
        .td84{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 84px;vertical-align: bottom;}
        .td85{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 50px;vertical-align: bottom;}
        .td86{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 95px;vertical-align: bottom;}
        .td87{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 63px;vertical-align: bottom;}
        .td88{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 121px;vertical-align: bottom;}
        .td89{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 108px;vertical-align: bottom;}
        .td90{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 45px;vertical-align: bottom;}
        .td91{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 14px;vertical-align: bottom;}
        .td92{padding: 0px;margin: 0px;width: 33px;vertical-align: bottom;}
        .td93{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 38px;vertical-align: bottom;}
        .td94{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 69px;vertical-align: bottom;}
        .td95{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 82px;vertical-align: bottom;}
        .td96{border-right: #000000 1px solid;padding: 0px;margin: 0px;width: 20px;vertical-align: bottom;}
        .td97{border-left: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 76px;vertical-align: bottom;}
        .td98{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 51px;vertical-align: bottom;}
        .td99{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 20px;vertical-align: bottom;}
        .td100{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 45px;vertical-align: bottom;}
        .td101{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 13px;vertical-align: bottom;}
        .td102{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 95px;vertical-align: bottom;}
        .td103{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 25px;vertical-align: bottom;}
        .td104{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 69px;vertical-align: bottom;}
        .td105{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 76px;vertical-align: bottom;}
        .td106{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 121px;vertical-align: bottom;}
        .td107{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 82px;vertical-align: bottom;}
        .td108{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 70px;vertical-align: bottom;}
        .td109{border-left: #000000 1px solid;padding: 0px;margin: 0px;width: 76px;vertical-align: bottom;}
        .td110{border-left: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 51px;vertical-align: bottom;}
        .td111{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 14px;vertical-align: bottom;}
        .td112{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 38px;vertical-align: bottom;}
        .td113{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 57px;vertical-align: bottom;}
        .td114{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 37px;vertical-align: bottom;}
        .td115{border-right: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 77px;vertical-align: bottom;}
        .td116{border-left: #000000 1px solid;border-bottom: #000000 1px solid;padding: 0px;margin: 0px;width: 147px;vertical-align: bottom;}
        .td117{padding: 0px;margin: 0px;width: 39px;vertical-align: bottom;}
        .td118{padding: 0px;margin: 0px;width: 173px;vertical-align: bottom;}
        .td119{padding: 0px;margin: 0px;width: 295px;vertical-align: bottom;}
        .td120{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 6px;vertical-align: bottom;}
        .td121{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 97px;vertical-align: bottom;}
        .td122{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 1px;vertical-align: bottom;}
        .td123{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 31px;vertical-align: bottom;}
        .td124{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 14px;vertical-align: bottom;}
        .td125{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 12px;vertical-align: bottom;}
        .td126{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 7px;vertical-align: bottom;}
        .td127{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 77px;vertical-align: bottom;}
        .td128{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 38px;vertical-align: bottom;}
        .td129{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 78px;vertical-align: bottom;}
        .td130{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 25px;vertical-align: bottom;}
        .td131{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 26px;vertical-align: bottom;}
        .td132{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 89px;vertical-align: bottom;}
        .td133{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 71px;vertical-align: bottom;}
        .td134{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 103px;vertical-align: bottom;}
        .td135{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 186px;vertical-align: bottom;}
        .td136{border-top: #000000 1px solid;padding: 0px;margin: 0px;width: 481px;vertical-align: bottom;}
        .td137{padding: 0px;margin: 0px;width: 103px;vertical-align: bottom;}
        .td138{padding: 0px;margin: 0px;width: 186px;vertical-align: bottom;}
        .td139{padding: 0px;margin: 0px;width: 481px;vertical-align: bottom;}

        .tr0{height: 22px;}
        .tr1{height: 21px;}
        .tr2{height: 20px;}
        .tr3{height: 41px;}
        .tr4{height: 40px;}
        .tr5{height: 19px;}
        .tr6{height: 14px;}
        .tr7{height: 8px;}
        .tr8{height: 18px;}
        .tr9{height: 10px;}
        .tr10{height: 11px;}
        .tr11{height: 15px;}
        .tr12{height: 12px;}
        .tr13{height: 4px;}
        .tr14{height: 17px;}
        .tr15{height: 9px;}
        .tr16{height: 16px;}
        .tr17{height: 7px;}
        .tr18{height: 2px;}
        .tr19{height: 36px;}

        .t0{width: 1032px;margin-top: 21px;font: 12px 'Times New Roman';}
        .t1{width: 788px;font: 12px 'Times New Roman';}
        {/literal}
    </STYLE>
<BODY>
<DIV id="page_1">
    <DIV id="id_1">
        <P class="p0 ft0">Приложение № 1</P>
        <P class="p1 ft0">к постановлению Правительства Российской Федерации от 26.12.2011 № 1137</P>
        <TABLE cellpadding=0 cellspacing=0 class="t0">
            <TR>
                <TD colspan="3" class="tr0 td0"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=12 class="tr0 td3"><P class="p3 ft2"><NOBR>СЧЕТ-ФАКТУРА</NOBR> №</P></TD>
                <TD colspan=4 class="tr1 td4"><P class="p2 center" contenteditable="true">&nbsp;</P></TD>
                <TD colspan=2 class="tr0 td7"><P class="p4 ft2">от "</P></TD>
                <TD colspan="3" class="tr1 td8"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
                <TD class="tr0 td10"><P class="p5 ft2">"</P></TD>
                <TD colspan="4" class="tr1 td11"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
                <TD class="tr0 td15"><P class="p6 ft2">(1)</P></TD>
                <TD colspan="9" class="tr0 td16"><P class="p2 ft1">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan="3" class="tr1 td0"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=10 class="tr1 td23"><P class="p3 ft2">ИСПРАВЛЕНИЕ №</P></TD>
                <TD class="tr1 td24"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan="5" class="tr2 td25"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
                <TD colspan=2 class="tr1 td7"><P class="p4 ft2">от "</P></TD>
                <TD colspan="3" class="tr2 td8"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
                <TD class="tr1 td10"><P class="p5 ft2">"</P></TD>
                <TD colspan="4" class="tr2 td11"><P class="p2 center"  contenteditable="true">&nbsp;</P></TD>
                <TD class="tr1 td15"><P class="p4 ft2">(1а)</P></TD>
                <TD colspan="9" class="tr1 td16"><P class="p2 ft1">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan=2 class="tr3 td16"><P class="p7 ft2">Продавец</P></TD>
                <TD colspan=27 class="tr4 td27"><P class="p2">{$CONFIG.firm_name}</P></TD>

                <TD class="tr3 td15"><P class="p6 ft2">(2)</P></TD>
            </TR>
            <TR>
                <TD class="tr2 td0"><P class="p7 ft2">Адрес</P></TD>
                <TD colspan=28 class="tr5 td33"><P class="p2">{$CONFIG.firm_address}</P></TD>

                <TD class="tr2 td15"><P class="p4 ft2">(2а)</P></TD>
            </TR>
            <TR>
                <TD colspan=5 class="tr1 td34"><P class="p7 ft2">ИНН/КПП продавца </P></TD>
                <TD colspan=24 class="tr2 td28"><P class="p2">{$CONFIG.firm_inn} / {$CONFIG.firm_kpp}</P></TD>

                <TD class="tr1 td15"><P class="p4 ft2">(2б)</P></TD>
            </TR>
            <TR>
                <TD colspan=11 class="tr2 td35"><P class="p7 ft2">Грузоотправитель и его адрес</P></TD>
                <TD colspan=18 class="tr5 td36"><P class="p2" contenteditable="true">&nbsp;</P></TD>

                <TD class="tr2 td15"><P class="p6 ft2">(3)</P></TD>
            </TR>
            <TR>
                <TD colspan=10 class="tr1 td37"><P class="p7 ft2">Грузополучатель и его адрес</P></TD>
                <TD colspan=19 class="tr2 td38"><P class="p2" contenteditable="true">&nbsp;</P></TD>

                <TD class="tr1 td15"><P class="p6 ft2">(4)</P></TD>
            </TR>
            <TR>
                <TD colspan=15 class="tr2 td39"><P class="p7 ft2">К <NOBR>платежно-расчетному</NOBR> документу №</P></TD>
                <TD colspan=6 class="tr5 td4"><P class="p2">{$order.order_num}</P></TD>

                <TD colspan=2 class="tr2 td19"><P class="p8 ft2">от</P></TD>
                <TD colspan=6 class="tr5 td40"><P class="p2">{date("Y.m.d" , strtotime($order.dateof))}</P></TD>

                <TD class="tr2 td15"><P class="p6 ft2">(5)</P></TD>
            </TR>
            <TR>
                <TD colspan=3 class="tr2 td41"><P class="p7 ft2">Покупатель</P></TD>
                <TD colspan=26 class="tr5 td11"><P class="p2">{$user.surname} {$user.name} {$user.midname}</P></TD>

                <TD class="tr2 td15"><P class="p6 ft2">(6)</P></TD>
            </TR>
            <TR>
                <TD class="tr1 td0"><P class="p7 ft2">Адрес</P></TD>
                <TD colspan=28 class="tr2 td33"><P class="p2">{$address->getLineView(true)}</P></TD>
                <TD class="tr1 td15"><P class="p4 ft2">(6а)</P></TD>
            </TR>
            <TR>
                <TD colspan=7 class="tr2 td42"><P class="p7 ft2">ИНН/КПП покупателя</P></TD>
                <TD colspan=22 class="tr5 td29"><P class="p2" contenteditable="true">{if $user.is_company}{$user.company_inn}{/if}</P></TD>
                <TD class="tr2 td15"><P class="p4 ft2">(6б)</P></TD>
            </TR>
            <TR>
                <TD colspan=9 class="tr5 td43"><P class="p7 ft2">Валюта: наименование, код</P></TD>
                <TD colspan="20" class="tr5 td24 border-bottom"><P class="p2" contenteditable="true">{$order.currency}, {$order.currency_stitle}</P></TD>
                <TD colspan=2 class="tr5 td49"><P class="p6 ft2" style="text-align: left; padding-left: 9px;">(7)</P></TD>
            </TR>
            <TR>
                <TD colspan="39" class="tr1 td50"><P class="p2 ft1">&nbsp;</P></TD>
            </TR>
        </TABLE>
        {$page = 1}
        {$counter = 1}
        {foreach $products_array as $product_arr}
        <table cellpadding=0 cellspacing=0 class="t0" style="margin: 0">
            <TR class="head">
                <TD colspan="4" class="tr6 td69 border-right"><P class="p2 ft1">&nbsp;</P></TD>
                <TD  colspan="4" class="tr6 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan="4" class="tr6 td73"><P class="p9 ft3">Единица</P></TD>
                <TD colspan="4" class="tr6 td74 border-right"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td44"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td45"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td46"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td47"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td76"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td45"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td44"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=2 class="tr6 td77"><P class="p10 ft3">Стоимость</P></TD>
                <TD class="tr6 td15"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td75"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td78"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td79"><P class="p2 ft1">&nbsp;</P></TD>
                <TD rowspan=2 class="tr0 td77"><P class="p11 ft4">Сумма</P></TD>
                <TD class="tr6 td17"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td80"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=4 class="tr6 td81"><P class="p11 ft3">Страна происхождения</P></TD>
                <TD class="tr6 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td82"><P class="p2 ft1">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan=4 rowspan=2 class="tr5 td83"><P class="p12 ft4">Наименование товара</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td71"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td72"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=4 rowspan=2 class="tr8 td84"><P class="p9 ft4">измерения</P></TD>
                <TD class="tr7 td74"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td24"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td75"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td44"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td45"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td46"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=3 rowspan=2 class="tr5 td85"><P class="p13 ft4">Цена</P></TD>
                <TD colspan=4 rowspan=2 class="tr5 td86"><P class="p11 ft6">товаров (работ,</P></TD>
                <TD colspan=2 rowspan=2 class="tr5 td87"><P class="p14 ft4">В том</P></TD>
                <TD class="tr7 td78"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td79"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr5 td88"><P class="p15 ft4">Стоимость товаров</P></TD>
                <TD class="tr7 td1"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td2"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr8 td89"><P class="p16 ft4">товара</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td82"><P class="p2 ft5">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr9 td25"><P class="p2 ft7">&nbsp;</P></TD>
                <TD class="tr9 td25"><P class="p2 ft7">&nbsp;</P></TD>
                <TD class="tr9 td28"><P class="p2 ft7">&nbsp;</P></TD>
                <TD class="tr9 td29"><P class="p2 ft7">&nbsp;</P></TD>
                <TD class="tr10 td74"><P class="p2 ft8">&nbsp;</P></TD>
                <TD colspan=3 rowspan=2 class="tr11 td90"><P class="p17 ft4">Коли-</P></TD>
                <TD class="tr10 td44"><P class="p2 ft8">&nbsp;</P></TD>
                <TD class="tr10 td45"><P class="p2 ft8">&nbsp;</P></TD>
                <TD class="tr10 td46"><P class="p2 ft8">&nbsp;</P></TD>
                <TD class="tr10 td78"><P class="p2 ft8">&nbsp;</P></TD>
                <TD class="tr10 td79"><P class="p2 ft8">&nbsp;</P></TD>
                <TD rowspan=2 class="tr11 td77"><P class="p14 ft4">налога,</P></TD>
                <TD class="tr9 td51"><P class="p2 ft7">&nbsp;</P></TD>
                <TD class="tr9 td52"><P class="p2 ft7">&nbsp;</P></TD>
                <TD class="tr10 td21"><P class="p2 ft8">&nbsp;</P></TD>
                <TD rowspan=2 class="tr11 td82"><P class="p18 ft4">Номер</P></TD>
            </TR>
            <TR>
                <TD colspan=4 rowspan=2 class="tr12 td83"><P class="p19 ft9">(описание выполненных</P></TD>
                <TD class="tr13 td21"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td21"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td91"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td72"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td92"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td44"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td21"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td93"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td74"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td44"><P class="p2 ft10">&nbsp;</P></TD>
                <TD colspan=5 rowspan=2 class="tr12 td87"><P class="p19 ft9">(тариф)</P></TD>
                <TD class="tr13 td45"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td44"><P class="p2 ft10">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr12 td77"><P class="p20 ft9">услуг),</P></TD>
                <TD colspan=2 rowspan=2 class="tr12 td87"><P class="p21 ft9">числе</P></TD>
                <TD colspan=2 rowspan=2 class="tr12 td94"><P class="p19 ft9">Налоговая</P></TD>
                <TD colspan=2 rowspan=2 class="tr12 td88"><P class="p22 ft9">(работ, услуг),</P></TD>
                <TD class="tr13 td1"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td2"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td75"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td95"><P class="p2 ft10">&nbsp;</P></TD>
                <TD class="tr13 td21"><P class="p2 ft10">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr14 td96"><P class="p23 ft4">к</P></TD>
                <TD class="tr7 td72"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=4 rowspan=2 class="tr14 td73"><P class="p24 ft6">условное</P></TD>
                <TD class="tr7 td74"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=3 rowspan=2 class="tr14 td90"><P class="p24 ft4">чество</P></TD>
                <TD class="tr7 td44"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td45"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td44"><P class="p2 ft5">&nbsp;</P></TD>
                <TD rowspan=2 class="tr14 td77"><P class="p14 ft6">предъяв-</P></TD>
                <TD class="tr7 td1"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td2"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td75"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td95"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD rowspan=2 class="tr14 td82"><P class="p25 ft4">таможенной</P></TD>
            </TR>
            <TR>
                <TD colspan=4 rowspan=2 class="tr14 td83"><P class="p14 ft4">работ, оказанных услуг),</P></TD>
                <TD class="tr15 td21"><P class="p2 ft11">&nbsp;</P></TD>
                <TD class="tr15 td72"><P class="p2 ft11">&nbsp;</P></TD>
                <TD class="tr15 td74"><P class="p2 ft11">&nbsp;</P></TD>
                <TD class="tr15 td44"><P class="p2 ft11">&nbsp;</P></TD>
                <TD colspan=5 rowspan=2 class="tr14 td87"><P class="p18 ft4">за единицу</P></TD>
                <TD colspan=4 rowspan=2 class="tr14 td86"><P class="p11 ft4">имущественных</P></TD>
                <TD colspan=2 rowspan=2 class="tr14 td87"><P class="p11 ft4">сумма</P></TD>
                <TD colspan=2 rowspan=2 class="tr14 td94"><P class="p11 ft4">ставка</P></TD>
                <TD colspan=2 rowspan=2 class="tr14 td88"><P class="p15 ft4">имущественных прав</P></TD>
                <TD colspan=3 rowspan=2 class="tr14 td87"><P class="p11 ft4">цифровой</P></TD>
                <TD rowspan=2 class="tr14 td95"><P class="p11 ft4">краткое</P></TD>
                <TD class="tr15 td21"><P class="p2 ft11">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr16 td96"><P class="p23 ft4">о</P></TD>
                <TD class="tr7 td72"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=4 rowspan=2 class="tr16 td73"><P class="p24 ft4">обозначение</P></TD>
                <TD class="tr7 td74"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=3 rowspan=2 class="tr16 td90"><P class="p17 ft4">(объем)</P></TD>
                <TD class="tr7 td44"><P class="p2 ft5">&nbsp;</P></TD>
                <TD rowspan=2 class="tr16 td77"><P class="p11 ft4">ляемая</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD rowspan=2 class="tr16 td82"><P class="p18 ft4">декларации</P></TD>
            </TR>
            <TR>
                <TD colspan=4 rowspan=2 class="tr11 td83"><P class="p12 ft4">имущественного права</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td72"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td74"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td44"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=5 rowspan=2 class="tr11 td87"><P class="p25 ft4">измерения</P></TD>
                <TD colspan=4 rowspan=2 class="tr11 td86"><P class="p15 ft4">прав без налога -</P></TD>
                <TD colspan=2 rowspan=2 class="tr11 td87"><P class="p15 ft4">акциза</P></TD>
                <TD class="tr7 td78"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td79"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr11 td88"><P class="p11 ft4">с налогом - всего</P></TD>
                <TD colspan=3 rowspan=2 class="tr11 td87"><P class="p11 ft6">код</P></TD>
                <TD rowspan=2 class="tr11 td95"><P class="p15 ft4">наименование</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr17 td21"><P class="p2 ft12">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr11 td96"><P class="p7 ft4">д</P></TD>
                <TD class="tr17 td72"><P class="p2 ft12">&nbsp;</P></TD>
                <TD colspan=4 rowspan=2 class="tr11 td73"><P class="p24 ft4">(национальное)</P></TD>
                <TD class="tr17 td74"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td24"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td21"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td75"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td44"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td78"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td79"><P class="p2 ft12">&nbsp;</P></TD>
                <TD rowspan=2 class="tr11 td77"><P class="p14 ft4">покупателю</P></TD>
                <TD class="tr17 td21"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td82"><P class="p2 ft12">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr7 td69"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td1"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td2"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td70"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td72"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td74"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td24"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td75"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td44"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td45"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td46"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td47"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td76"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td45"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td44"><P class="p2 ft5">&nbsp;</P></TD>
                <TD colspan=2 rowspan=2 class="tr11 td77"><P class="p10 ft4">всего</P></TD>
                <TD class="tr7 td15"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td75"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td78"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td79"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td17"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td80"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td1"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td2"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td75"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td95"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td21"><P class="p2 ft5">&nbsp;</P></TD>
                <TD class="tr7 td82"><P class="p2 ft5">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr17 td69"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td1"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td2"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td70"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td21"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td21"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td91"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td72"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td92"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td44"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td21"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td93"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td74"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td24"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td21"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td75"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td44"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td45"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td46"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td47"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td21"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td76"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td45"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td44"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td15"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td75"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td78"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td79"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td77"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td17"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td80"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td1"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td2"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td75"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td95"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td21"><P class="p2 ft12">&nbsp;</P></TD>
                <TD class="tr17 td82"><P class="p2 ft12">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan=2 class="tr18 td97"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td52"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td98"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td25"><P class="p2 ft13">&nbsp;</P></TD>
                <TD colspan=2 class="tr18 td99"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td29"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td4"><P class="p2 ft13">&nbsp;</P></TD>
                <TD colspan=3 class="tr18 td98"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td31"><P class="p2 ft13">&nbsp;</P></TD>
                <TD colspan=3 class="tr18 td100"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td26"><P class="p2 ft13">&nbsp;</P></TD>
                <TD colspan=3 class="tr18 td36"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td25"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td101"><P class="p2 ft13">&nbsp;</P></TD>
                <TD colspan=4 class="tr18 td102"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td12"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td103"><P class="p2 ft13">&nbsp;</P></TD>
                <TD colspan=2 class="tr18 td104"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td105"><P class="p2 ft13">&nbsp;</P></TD>
                <TD colspan=2 class="tr18 td106"><P class="p2 ft13">&nbsp;</P></TD>
                <TD colspan=2 class="tr18 td12"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td103"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td107"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td25"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td108"><P class="p2 ft13">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan=2 class="tr6 td109"><P class="p26 ft3">1</P></TD>
                <TD class="tr6 td2"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td70"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=2 class="tr6 td96"><P class="p23 ft3">2</P></TD>
                <TD class="tr6 td72"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td92"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=3 class="tr6 td70"><P class="p27 ft3">2а</P></TD>
                <TD class="tr6 td74"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=3 class="tr6 td90"><P class="p28 ft3">3</P></TD>
                <TD class="tr6 td44"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=3 class="tr6 td17"><P class="p29 ft3">4</P></TD>
                <TD class="tr6 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td76"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=4 class="tr6 td86"><P class="p30 ft3">5</P></TD>
                <TD class="tr6 td15"><P class="p31 ft3">6</P></TD>
                <TD class="tr6 td75"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=2 class="tr6 td94"><P class="p32 ft3">7</P></TD>
                <TD class="tr6 td77"><P class="p12 ft3">8</P></TD>
                <TD colspan=2 class="tr6 td88"><P class="p14 ft3">9</P></TD>
                <TD colspan=3 class="tr6 td87"><P class="p33 ft3">10</P></TD>
                <TD class="tr6 td95"><P class="p34 ft3">10а</P></TD>
                <TD class="tr6 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr6 td82"><P class="p32 ft3">11</P></TD>
            </TR>
            <TR>
                <TD class="tr18 td110"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td51"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td52"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td98"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td25"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td25"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td111"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td29"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td4"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td26"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td25"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td112"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td31"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td8"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td25"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td103"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td26"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td5"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td6"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td32"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td25"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td101"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td5"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td26"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td9"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td113"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td12"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td103"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td14"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td114"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td105"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td36"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td115"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td51"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td52"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td103"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td107"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td25"><P class="p2 ft13">&nbsp;</P></TD>
                <TD class="tr18 td108"><P class="p2 ft13">&nbsp;</P></TD>
            </TR>
            {foreach from=$product_arr key=n item=item name=foo}
                {assign var=product value=$products[$n].product}
                <TR>
                    <TD colspan="4" class="tr11 td110 border-right" style="max-width: 143px;"><P class="p2" style="white-space: pre-wrap;">{$item.cartitem.title}</P></TD>
                {$unit = $product->getUnit()}
                <TD colspan="3" class="tr11 td25 border-right"><P class="p2 center" contenteditable="true">{$unit.code}</P></TD>
                <TD colspan="5" class="tr11 td29 border-right"><P class="p2 center" contenteditable="true">{$unit.stitle}</P></TD>
                <TD colspan="4" class="tr11 td31 border-right"><P class="p2 center">{$item.cartitem.amount}</P></TD>
                <TD colspan="6" class="tr11 td26 border-right"><P class="p2 center">{(float)$item.single_cost_noformat}</P></TD>
                <TD colspan="4" class="tr11 td5 border-right"><P class="p2 center">{$taxes.items[$n].subtotal}</P></TD>
                <TD colspan="2" class="tr11 td12 border-right"><P class="p2 center" contenteditable="true">&nbsp;</P></TD>
                <TD colspan="2" class="tr11 td14 border-right"><P class="p2 center">{round($taxes.items[$n].taxes.rate, 2)}%</P></TD>
                <TD class="tr11 td105"><P class="p2 center">{$taxes.items[$n].taxes.value}</P></TD>
                <TD colspan="2" class="tr11 td36 border-right"><P class="p2 center">{$taxes.items[$n].taxes.cost}</P></TD>
                <TD colspan="3" class="tr11 td51 border-right"><P contenteditable="true" class="p2 center">&nbsp;</P></TD>
                <TD class="tr11 td107"><P contenteditable="true" class="p2 center">&nbsp;</P></TD>
                <TD colspan="2" class="tr11 td25 border-right"><P contenteditable="true" class="p2 center"></P></TD>
                </TR>
            {/foreach}
            {if count($products_array) != $page}
        </TABLE>
        <div class="page-break" style="margin-bottom: 20px"></div>
        {/if}
            {$page = $page + 1}
        {/foreach}
            {if $taxes.delivery}
                <TR>
                    <TD colspan="4" class="tr11 td110 border-right" style="max-width: 143px;"><P class="p2" style="white-space: pre-wrap;">{$taxes.delivery.title}</P></TD>
                    <TD colspan="3" class="tr11 td25 border-right"><P class="p2 center" contenteditable="true"></P></TD>
                    <TD colspan="5" class="tr11 td29 border-right"><P class="p2 center" contenteditable="true"></P></TD>
                    <TD colspan="4" class="tr11 td31 border-right"><P class="p2 center">1</P></TD>
                    <TD colspan="6" class="tr11 td26 border-right"><P class="p2 center">{$taxes.delivery.subtotal}</P></TD>
                    <TD colspan="4" class="tr11 td5 border-right"><P class="p2 center">{$taxes.delivery.subtotal}</P></TD>
                    <TD colspan="2" class="tr11 td12 border-right"><P class="p2 center" contenteditable="true">&nbsp;</P></TD>
                    <TD colspan="2" class="tr11 td14 border-right"><P class="p2 center">{round($taxes.delivery.tax_rate, 2)}%</P></TD>
                    <TD class="tr11 td105"><P class="p2 center">{$taxes.delivery.tax}</P></TD>
                    <TD colspan="2" class="tr11 td36 border-right"><P class="p2 center">{$taxes.delivery.cost}</P></TD>
                    <TD colspan="3" class="tr11 td51 border-right"><P contenteditable="true" class="p2 center">&nbsp;</P></TD>
                    <TD class="tr11 td107"><P contenteditable="true" class="p2 center">&nbsp;</P></TD>
                    <TD colspan="2" class="tr11 td25 border-right"><P contenteditable="true" class="p2 center"></P></TD>
                </TR>
            {/if}
            <TR>
                <TD colspan=5 class="tr11 td116"><P class="p7 ft4">Всего к оплате</P></TD>
                <TD class="tr11 td25"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td28"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td29"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td4"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td26"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td25"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td30"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td31"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td8"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td25"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td13"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td26"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td5"><P class="p2 ft1"></P></TD>
                <TD class="tr11 td6"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td32"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td25"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td101"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan="4" class="tr11 td5 border-right"><P class="p2 center">{$subtotal}</P></TD>

                <TD class="tr11 td12"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td13"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=2 class="tr11 td104"><P class="p2 ft4">Х</P></TD>
                <TD class="tr11 td105"><P class="p2 center">{$all_taxes}</P></TD>
                <TD colspan="2" class="tr11 td36 border-right"><P class="p2 center">{$order_data.total_cost}</P></TD>
            </TR>
            <TR>
                <TD colspan=8 class="tr19 td23"><P class="p7 ft14">Руководитель организации</P></TD>
                <TD class="tr19 td92"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td44"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td117"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td74"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td24"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td19"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td44"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td45"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td46"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td47"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td24"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td45"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td44"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td10"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td48"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td15"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=4 class="tr19 td118"><P class="p4 ft14">Главный бухгалтер</P></TD>
                <TD class="tr19 td17"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td18"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td1"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td2"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td19"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td20"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr19 td22"><P class="p2 ft1">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD colspan=13 class="tr2 td43"><P class="p7 ft14">или иное уполномоченное лицо</P></TD>
                <TD class="tr2 td24"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td19"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td44"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td45"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td46"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td47"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td24"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td45"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td44"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td10"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td48"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td15"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=6 class="tr2 td119"><P class="p4 ft14">или иное уполномоченное лицо</P></TD>
                <TD class="tr2 td1"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td2"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td19"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td20"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr2 td22"><P class="p2 ft1">&nbsp;</P></TD>
            </TR>
            <TR>
                <TD class="tr8 td0"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td1"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td2"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td0"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td71"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td72"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td92"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td44"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td120"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=6 class="tr14 td121"><P class="p35 ft4">(подпись)</P></TD>
                <TD class="tr8 td45"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td122"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td123"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td120"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td124"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td125"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td126"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=2 class="tr14 td127"><P class="p7 ft4">(ф.и.о.)</P></TD>
                <TD class="tr14 td128"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td19"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td78"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td15"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td16"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td17"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td129"><P class="p36 ft4">(подпись)</P></TD>
                <TD class="tr14 td130"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr8 td2"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td131"><P class="p2 ft1">&nbsp;</P></TD>
                <TD colspan=2 class="tr14 td132"><P class="p37 ft4">(ф.и.о.)</P></TD>
                <TD class="tr14 td133"><P class="p2 ft1">&nbsp;</P></TD>
            </TR>
        </TABLE>
        <P class="p38 ft14">Индивидуальный предприниматель</P>
    </DIV>
    <DIV id="id_2">
        <TABLE cellpadding=0 cellspacing=0 class="t1">
            <TR>
                <TD class="tr11 td134"><P class="p36 ft4">(подпись)</P></TD>
                <TD class="tr16 td45"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td135"><P class="p39 ft4">(ф.и.о.)</P></TD>
                <TD class="tr16 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr11 td136"><P class="p40 ft4">(реквизиты свидетельства о государственной регистрации</P></TD>
            </TR>
            <TR>
                <TD class="tr14 td137"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td45"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td138"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td21"><P class="p2 ft1">&nbsp;</P></TD>
                <TD class="tr14 td139"><P class="p15 ft4">индивидуального предпринимателя)</P></TD>
            </TR>
        </TABLE>
    </DIV>
</DIV>
</BODY>
</HTML>
