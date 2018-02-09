<?
@include_once($Dir."alimtalk/config.php");
class ALIM_TALK{

    public $jsonData = array();					// 요청 데이터 배열
    public $jsonDecodeData = array();		// 요청 데이터 배열
    public $ataContents = array();				// 치환된 본문 내용
    public $ataDbFlag = true;
    public $storeArr = array();				// 업주알림 매장정보
    public $pdateArr = array();				// 업주알림 픽업날자
    public $priceArr = array();				// 업주알림 결제금액
    public $pronameArr = array();				// 업주알림 상품명
    public $t_staus = 0;					// 업주알림톡 변수	[0 기존 1 업주 알림톡]
    /*
        #{브랜드} : #{brand_name}
        #{고객명} : #{order_name}
        #{상품코드} : #{product_code}
        #{실결제금액} : #{order_price}
        #{가상계좌번호} : #{virtual_number}
        #{은행명} : #{bank_name}
        #{입금자명} : #{deposit_name}
        #{입금액} : #{deposit_price}
        #{주문조회 url} : #{order_url}
        #{배송지 주소1+주소2} : #{deli_address}
        #{색상} : #{color_str}
        #{사이즈} : #{size_str}
        #{수량} : #{quantity}
        #{운송장번호} : #{deli_code}
        #{구매확정 url} : #{confirm_url}
        #{play store url} : #{play_store_url}
        #{ios app store url} : #{app_store_url}

        #{픽업매장명} : #{pickup_store}
        #{픽업매장연락처} : #{pickup_tel}
        #{픽업매장주소} : #{pickup_address}
        #{픽업일자} : #{pickup_date}

        #{매장명} : #{store_name}

        #{예약매장명} : #{reserve_store}
        #{예약매장연락처} : #{reserve_tel}
        #{예약매장주소} : #{reserve_address}
        #{수령일자} : #{reserve_date}
    */

    function ALIM_TALK(){
        $this->jsonData = "";
        $this->jsonDecodeData = "";
    }

    function makeJsonDecodeData(){
        $this->jsonDecodeData = json_decode($this->jsonData);
    }

    function makeAlimTalkSearchData($ordercode, $template,$temp_phone){
        GLOBAL $pg_code, $alimConf;
//         exdebug($ordercode." = ".$template);
//         exit();
        /*
        주문 리스트에 있는 조건 가져와서 사용
        tblorderproduct a join tblorderinfo b

        oi_step1 = "0" > 주문접수
        oi_step1 = "1" > 결제완료
        oi_step1 = "2" > 배송준비중
        oi_step1 = "3" > 배송중
        oi_step1 = "4" > 배송완료
        oi_step1 = "44" > 입금전취소완료
        oi_step1 = "67" > 교환신청
        oi_step1 = "61" > 교환접수
        oi_step1 = "62" > 교환완료
        oi_step1 = "68" > 반품신청
        oi_step1 = "63" > 반품접수
        oi_step1 = "64" > 반품완료
        oi_step1 = "65" > 환불접수
        oi_step1 = "66" > 환불완료

        OR

        if(count($oi_type_arr)) {
            foreach($oi_type_arr as $k => $v) {
                switch($v) {
                    case 44 : $subWhere[] = " (b.oi_step1 = 0 And b.oi_step2 = 44) "; break;    //입금전취소완료
                    case 67 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 40) "; break;   //교환신청
                    case 61 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 41) "; break;   //교환접수
                    case 62 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 44) "; break;   //교환완료
                    case 68 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 40) "; break;    //반품신청
                    case 63 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 41) "; break;    //반품접수
                    case 64 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And a.op_step = 42) "; break;   //반품완료(배송중 이상이면서 환불접수단계)
                    case 65 : $subWhere[] = " (a.redelivery_type != 'G' and b.bank_date is not null And ((b.oi_step1 in (1,2) and a.op_step = 41) OR a.op_step = 42) And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '')))"; break;  //환불접수
                    case 66 : $subWhere[] = " (a.redelivery_type != 'G' and b.oi_step1 > 0 And a.op_step = 44 And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = ''))) "; break;  //환불완료
                }
            }
        }
        */
        $jion_temp_phone = '';
        $delicomlist=array();
        $sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
        $result=pmysql_query($sql,get_db_conn());
        while($row=pmysql_fetch_object($result)) {
            $delicomlist[]=$row;
        }
        pmysql_free_result($result);

        switch($template) {
            case 'WEB01' :
                //주문접수
                $subWhere[] = " a.op_step = 0";
            break;
            case 'WEB02' :
                //가상계좌 결제완료 BOQ
                $subWhere[] = "(b.paymethod = 'B' OR b.paymethod = 'OA' OR b.paymethod = 'QA') AND a.op_step = 1";
//                 	$subWhere[] = "(b.paymethod = 'B".$pg_code."' OR b.paymethod = 'O".$pg_code."' OR b.paymethod = 'Q".$pg_code."') AND a.op_step = 1";
            break;
            case 'WEB03' :
                //신용카드 결제완료 카드 [CA,VA] 페이코 [YF] 핸드폰 [MA]
            	$subWhere[] = "(b.paymethod = 'CA' OR b.paymethod = 'VA' OR b.paymethod = 'MA' OR b.paymethod = 'YF') AND a.op_step = 1";
//             		$subWhere[] = "(b.paymethod = 'C".$pg_code."' OR b.paymethod = 'V".$pg_code."') AND a.op_step = 1";
            break;
            /*
            case 2 :
                //배송준비중
                $subWhere[] = " a.op_step = 2";
            break;
            */
            case 'WEB04' :
                //배송중
                $subWhere[] = " a.op_step = 3";
            break;
            case 'WEB05' :
                //배송완료
                $subWhere[] = " a.op_step = 4";
            break;

//             case 'WEB06' :
            case 'WEB11' :
                //배송 전 취소접수
                $subWhere[] = " (b.oi_step1 = '1' AND (coalesce(a.opt1_change, '') = '' AND coalesce(a.opt2_change, '') = '') AND a.op_step = 41 or a.op_step = 44) ";
            break;
            case 'WEB16' :
            	// 업주알림톡 전송
            	$this->t_staus = 1;
            break;
            /*
            case 67 :
                 //교환신청
                $subWhere[] = " (b.oi_step1 = 67)";
                $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 40) ";
            break;
            case 61 :
                //교환접수
                $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 41) ";
            break;
            case 62 :
                //교환완료
                $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 44) ";
            break;
            */
            case 'WEB07' :
                //반품신청
                $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 40) ";
            break;
            case 'WEB08' :
                //반품접수
                $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 41) ";
            break;
            /*
            case 64 :
                //반품완료(배송중 이상이면서 환불접수단계)
                $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And a.op_step = 42) ";
            break;
            */
            case 'WEB09' :
                //환불접수
                $subWhere[] = " (a.redelivery_type != 'G' and b.bank_date is not null And ((b.oi_step1 in (1,2) and a.op_step = 41) OR a.op_step = 42) And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '')))";
            break;
            case 'WEB10' :
                //환불완료
                $subWhere[] = " (a.redelivery_type != 'G' and b.oi_step1 > 0 And a.op_step = 44 And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = ''))) ";
            break;
        }

        $idx_arr = explode("|", $idx);
        if($idx){
            $subWhere[] = " a.idx in ('".implode("','", $idx_arr)."')";
        }

        if($oc_no){
            $subWhere[] = " a.oc_no='".$oc_no."' ";
        }


        if(count($subWhere)) {
            $sub = " AND (".implode(" AND ", $subWhere)." ) ";
        }

        # 브랜드
//         $arrayBrandName = array(
//             "M"=>array(
//                 "A"=>"브루노바피",
//                 "D"=>"데일리스트",
//                 "I"=>"인디안",
//                 "E"=>"두아니",
//                 "G"=>"헤리토리골프"),
//             "V"=>"올리비아로렌",
//             "C"=>"센터폴",
//             "T"=>"트레몰로",
//             "N"=>"NII",
//             "K"=>"크리스크리스티");

        if($ordercode && $template){
        	
        	if($template == "SCC04"){
//         		$arraySendOrder['order_name'] = $row->order_name;
        		$orderSql1 = "SELECT name,mobile FROM tblmember WHERE id = '".$ordercode."'";
        		backup_save_sql( $orderSql1 );
        		$orderResult1 = pmysql_query($orderSql1, get_db_conn());
        		$arraySendOrder1 = array("template"=>$template);
        		while($orderRow1 = pmysql_fetch_object($orderResult1)) {
        			$arraySendOrder1['order_name'] = $orderRow1->name;
        			$jion_temp_phone = $orderRow1->mobile;
        		}

        		$json1 = json_encode($arraySendOrder1);
        		$this->jsonData = $json1;
        		
        		$this->makeJsonDecodeData();
        		$this->makeAlimTalkMsg($jion_temp_phone);
        		
        	} else {
	            $orderSql = "SELECT
	                       		b.pay_data, b.receiver_addr, a.vender, v.brandname, a.ordercode, a.productcode, 
	            				a.productname, a.opt1_name, a.opt2_name, a.quantity,a.price, a.option_price, a.deli_com, 
	            				a.deli_num, a.deli_date, a.deli_price, a.coupon_price, a.op_step, a.opt1_change, 
	            				a.opt2_change, a.oc_no, a.date, a.idx, b.id, b.sender_name, b.sender_tel, b.point, b.paymethod, 
	            				b.oi_step1, b.oi_step2, a.redelivery_type, b.is_mobile, a.delivery_type, a.reservation_date, 
	            				a.store_code, a.use_point, a.use_epoint,REPLACE(REPLACE(opt1_name, CHR(10), ''), CHR(13), '') option_str1, 
	            				REPLACE(REPLACE(opt2_name, CHR(10), ''), CHR(13), '') option_str2
	                    	FROM
	                        	tblorderproduct a
	                            	JOIN tblorderinfo b ON a.ordercode = b.ordercode
	                                LEFT JOIN tblproductbrand v ON a.vender = v.vender
	                                WHERE b.ordercode ='".$ordercode."' ".$sub." ORDER BY a.vender, a.idx";
	           // exdebug($orderSql);
	//             echo $orderSql."<br>";
	//             exit();
	            backup_save_sql( $orderSql );
	            $orderResult=pmysql_query($orderSql, get_db_conn());
	            $count = 0;
	            $orderPriceTotal = 0;
	            while($orderRow=pmysql_fetch_object($orderResult)) {
	            	
	                $orderRow->order_product2 = implode(" / ", $arrOption);
	                $orderRow->order_count = $count;
	
	                $orderRow->order_name = $orderRow->sender_name;
	                $orderPriceTotal += (($orderRow->price+$orderRow->option_price)*$orderRow->quantity)-$orderRow->coupon_price-$orderRow->use_point-$orderRow->use_epoint+$orderRow->deli_price;
					//if($count==0){$orderPriceTotal  = $orderPriceTotal  - $orderRow->point; }
	                $orderRow->deli_address = str_replace("\n", " ", $orderRow->receiver_addr);
	                $orderRow->order_url = "http://".$_SERVER['HTTP_HOST']."/m/mypage_orderlist_view.php?ordercode=".$orderRow->ordercode;
	
	                $arrPayData = explode(" ", $orderRow->pay_data);
	                $orderRow->bank_name = $arrPayData[0];
	                $orderRow->virtual_number = $arrPayData[1];
	
	                $product_name = explode('] ', $orderRow->productname);
	                $orderRow->product_code = $product_name[1] ? $product_name[1] : $orderRow->productname;
	
	
	                $orderRow->quantity = $orderRow->quantity;
	                $orderRow->option_str = implode(" / ", $arrOption);
	
	                if(!$orderRow->deli_code) $orderRow->deli_code = $orderRow->deli_num;
	                #$orderRow->confirm_url = "주문 확정 URL";
	                #주문 상세로 연결
	                $orderRow->confirm_url = "http://".$_SERVER['HTTP_HOST']."/m/mypage_orderlist_view.php?ordercode=".$orderRow->ordercode;
	//                 $orderRow->play_store_url = "https://play.google.com/store/apps/details?id=com.sejung.android";
	//                 $orderRow->app_store_url = "https://itunes.apple.com/kr/app/hook-sejung-deohug-o2o-syopingmol/id1153471571";
	
	                $arrListOrder[$orderRow->ordercode] = $orderRow;
	
	                if($this->t_staus == "1" && $orderRow->delivery_type == '1'){
	                	// 업주 알림톡 전송
	                	array_push($this->storeArr, $orderRow->store_code);
	                	array_push($this->pdateArr, $orderRow->reservation_date);
	                	array_push($this->pronameArr, $orderRow->productname);
	                	array_push($this->priceArr, ($orderRow->price + $orderRow->deli_price - $orderRow->coupon_price));
	                }
	                
	                $count++;
	            }
	
	            $arraySendOrder = array("template"=>$template);
	
	            if($count > 0){
	                foreach($arrListOrder as $key => $row) {
	                    $order_product = $row->productcode;
	                   // $order_product_name = $row->productname;
	                    if($row->order_count > 0){
	                        $order_product .= " 외 ".number_format($row->order_count)."건";
	                    }else{
	                    }
	                    $arraySendOrder['ordercode'] = $row->ordercode;																			# 주문번호
	                   // $arraySendOrder['ordercodename'] = $order_product_name;																			# 주문번호
	                    $arraySendOrder['order_name'] = $row->order_name;																		# 주문자
	                    $arraySendOrder['order_product'] = $order_product;																		# 상품
	                    $arraySendOrder['order_price'] = number_format($orderPriceTotal);														# 결제 금액
	                    $arraySendOrder['deli_address'] = $row->deli_address;																	# 배송지
	                    $arraySendOrder['order_url'] = $row->order_url;
	                    $shortUrlOrder = $this->getShortURL($arraySendOrder['order_url']);
	                    $arraySendOrder['order_url'] = $shortUrlOrder ? $shortUrlOrder : $arraySendOrder['order_url'];							# 주문 내역 주소
	
	                    $arraySendOrder['brand_name'] = $row->brand_name;																		# 브랜드 명
	                    #$arraySendOrder['brand_name'] = "";																					# 브랜드 명 - 상품명에 브랜드명이 존재 하여 빈값
	                    $arraySendOrder['deposit_price'] = number_format($orderPriceTotal);													# 입금금액
	                    $arraySendOrder['virtual_number'] = $row->virtual_number;															# 입금계좌
	                    $arraySendOrder['bank_name'] = $row->bank_name;																		# 입금은행
	                    $arraySendOrder['cellphone'] = $row->sender_tel;																			# 입금은행
	
	                   	$arraySendOrder['quantity'] = $row->quantity;																					# 수량
	                    $arraySendOrder['product_code'] = $row->product_code;																# 상품 코드 (명)
	                    if($row->order_count > 0){
	                        $arraySendOrder['product_code'] .= " 외 ".number_format($row->order_count)."건";
	                    }else{
	                    }
	                    $arraySendOrder['option_str'] = $row->option_str1." / ".$row->option_str2;
	
	
	                    for($yy=0;$yy<count($delicomlist);$yy++) {
	                        if($row->deli_com>0 && $row->deli_com==$delicomlist[$yy]->code) {
	                            $deli_url = $delicomlist[$yy]->deli_url;
	                            $trans_num = $delicomlist[$yy]->trans_num;
	                            $company_name = $delicomlist[$yy]->company_name;
	                        }
	                    }
	                    $arraySendOrder['deli_code'] = $deli_url.$row->deli_code;
	                    $shortUrl = $this->getShortURL($arraySendOrder['deli_code']);
	                    $arraySendOrder['deli_code'] = $shortUrl ? $shortUrl : $arraySendOrder['deli_code'];						# 배송 코드
	                   // $arraySendOrder['pick_date'] = $row->reservation_date;
	                    
	                    /*
	                    $arraySendOrder['play_store_url'] = $row->play_store_url;
	                    $shortUrlAndroid = $this->getShortURL($arraySendOrder['play_store_url']);
	                    $arraySendOrder['play_store_url'] = $shortUrlAndroid ? $shortUrlAndroid : $arraySendOrder['play_store_url'];
	                    $arraySendOrder['play_store_url'] = "[ ".$arraySendOrder['play_store_url']." ]";								# 플레이스토어 주소
	
	                    $arraySendOrder['app_store_url'] = $row->app_store_url;
	                    $shortUrlApple = $this->getShortURL($arraySendOrder['app_store_url']);
	                    $arraySendOrder['app_store_url'] = $shortUrlApple ? $shortUrlApple : $arraySendOrder['app_store_url'];
	                    $arraySendOrder['app_store_url'] = "[ ".$arraySendOrder['app_store_url']." ]";								# 앱스토어 주소
	                    */
	
	                    $arraySendOrder['confirm_url'] = $row->confirm_url;
	                    $shortUrlConfirm = $this->getShortURL($arraySendOrder['confirm_url']);
	                    $arraySendOrder['confirm_url'] = $shortUrlConfirm ? $shortUrlConfirm : $arraySendOrder['confirm_url'];
	                    $arraySendOrder['confirm_url'] = "[ ".$arraySendOrder['confirm_url']." ]";										# 주문 확정 URL
	                }
	                $json = json_encode($arraySendOrder);
	                $this->jsonData = $json;
	
	                $this->makeJsonDecodeData();
	                $this->makeAlimTalkMsg();
	
	            }else{
	            	if($temp_phone != ''){
	            		$this->makeAlimTalkMsg($temp_phone);
	            	} else {
		                $this->ataDbFlag = "none";
	            	}
	            }
        	}

        }else{
        	if($temp_phone != ''){
        		$this->jsonDecodeData->template= $template;
        		$this->makeAlimTalkMsg($temp_phone,$template);
        	} else {
        		$this->ataDbFlag = "none";
        	}
            $this->ataDbFlag = "none";
        }
    }

    function makeAlimTalkSearchNewData($ordercode, $template, $idx, $oc_no){
		GLOBAL $pg_code, $alimConf;
//         exdebug($ordercode." = ".$template);
//         exit();
        /*
        주문 리스트에 있는 조건 가져와서 사용
        tblorderproduct a join tblorderinfo b

        oi_step1 = "0" > 주문접수
        oi_step1 = "1" > 결제완료
        oi_step1 = "2" > 배송준비중
        oi_step1 = "3" > 배송중
        oi_step1 = "4" > 배송완료
        oi_step1 = "44" > 입금전취소완료
        oi_step1 = "67" > 교환신청
        oi_step1 = "61" > 교환접수
        oi_step1 = "62" > 교환완료
        oi_step1 = "68" > 반품신청
        oi_step1 = "63" > 반품접수
        oi_step1 = "64" > 반품완료
        oi_step1 = "65" > 환불접수
        oi_step1 = "66" > 환불완료

        OR

        if(count($oi_type_arr)) {
            foreach($oi_type_arr as $k => $v) {
                switch($v) {
                    case 44 : $subWhere[] = " (b.oi_step1 = 0 And b.oi_step2 = 44) "; break;    //입금전취소완료
                    case 67 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 40) "; break;   //교환신청
                    case 61 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 41) "; break;   //교환접수
                    case 62 : $subWhere[] = " (a.redelivery_type = 'G' And a.op_step = 44) "; break;   //교환완료
                    case 68 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 40) "; break;    //반품신청
                    case 63 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And (coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '') And a.op_step = 41) "; break;    //반품접수
                    case 64 : $subWhere[] = " (a.redelivery_type = 'Y' and b.oi_step1 in (2,3,4) And a.op_step = 42) "; break;   //반품완료(배송중 이상이면서 환불접수단계)
                    case 65 : $subWhere[] = " (a.redelivery_type != 'G' and b.bank_date is not null And ((b.oi_step1 in (1,2) and a.op_step = 41) OR a.op_step = 42) And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '')))"; break;  //환불접수
                    case 66 : $subWhere[] = " (a.redelivery_type != 'G' and b.oi_step1 > 0 And a.op_step = 44 And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = ''))) "; break;  //환불완료
                }
            }
        }
        */
        $jion_temp_phone = '';
        $delicomlist=array();
        $sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
        $result=pmysql_query($sql,get_db_conn());
        while($row=pmysql_fetch_object($result)) {
            $delicomlist[]=$row;
        }
        pmysql_free_result($result);

		switch($template) {
             case 'WEB12' :
                //재품 품절
                //$subWhere[] = " (a.redelivery_type != 'G' and b.oi_step1 > 0 And a.op_step = 44 And ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = ''))) ";
            break;
            case 'WEB13' :
                //배송지연
                //$subWhere[] = " idx='$idx'";
            break;
            case 'WEB14' :
                //매장입찰 자동	
                //$subWhere[] = " idx='$idx'";
            break;
            case 'WEB15' :
                //배송지연 자동	
                //$subWhere[] = " a.deli_gbn='N' AND a.date > '$day_late' ";
            break;
		}

        $idx_arr = explode("|", $idx);
        if($idx){
            $subWhere[] = " a.idx in ('".implode("','", $idx_arr)."')";
        }

        if($oc_no){
            $subWhere[] = " a.oc_no='".$oc_no."' ";
        }


        if(count($subWhere)) {
            $sub = " AND (".implode(" AND ", $subWhere)." ) ";
        }

        # 브랜드
//         $arrayBrandName = array(
//             "M"=>array(
//                 "A"=>"브루노바피",
//                 "D"=>"데일리스트",
//                 "I"=>"인디안",
//                 "E"=>"두아니",
//                 "G"=>"헤리토리골프"),
//             "V"=>"올리비아로렌",
//             "C"=>"센터폴",
//             "T"=>"트레몰로",
//             "N"=>"NII",
//             "K"=>"크리스크리스티");

        if($ordercode && $template){
        	
			$orderSql = "SELECT
							b.pay_data, b.receiver_addr, a.vender, v.brandname, a.ordercode, a.productcode, 
							a.productname, a.opt1_name, a.opt2_name, a.quantity,a.price, a.option_price, a.deli_com, 
							a.deli_num, a.deli_date, a.deli_price, a.coupon_price, a.op_step, a.opt1_change, 
							a.opt2_change, a.oc_no, a.date, a.idx, b.id, b.sender_name, b.sender_tel, b.point, b.paymethod, 
							b.oi_step1, b.oi_step2, a.redelivery_type, b.is_mobile, a.delivery_type, a.reservation_date, 
							a.store_code, a.use_point, a.use_epoint,REPLACE(REPLACE(opt1_name, CHR(10), ''), CHR(13), '') option_str1, 
							REPLACE(REPLACE(opt2_name, CHR(10), ''), CHR(13), '') option_str2
						FROM
							tblorderproduct a
								JOIN tblorderinfo b ON a.ordercode = b.ordercode
								LEFT JOIN tblproductbrand v ON a.vender = v.vender
								WHERE b.ordercode ='".$ordercode."' ".$sub." ORDER BY a.vender, a.idx";
//	            exdebug($orderSql);
//             echo $orderSql."<br>";
//             exit();
			backup_save_sql( $orderSql );
			$orderResult=pmysql_query($orderSql, get_db_conn());
			$count = 0;
			$orderPriceTotal = 0;
			while($orderRow=pmysql_fetch_object($orderResult)) {
				
				$orderRow->order_product2 = implode(" / ", $arrOption);
				$orderRow->order_count = $count;

				$orderRow->order_name = $orderRow->sender_name;
				$orderPriceTotal += (($orderRow->price+$orderRow->option_price)*$orderRow->quantity)-$orderRow->coupon_price-$orderRow->use_point-$orderRow->use_epoint+$orderRow->deli_price;
				//if($count==0){$orderPriceTotal  = $orderPriceTotal  - $orderRow->point; }
				$orderRow->deli_address = str_replace("\n", " ", $orderRow->receiver_addr);
				$orderRow->order_url = "http://".$_SERVER['HTTP_HOST']."/m/mypage_orderlist_view.php?ordercode=".$orderRow->ordercode;

				$arrPayData = explode(" ", $orderRow->pay_data);
				$orderRow->bank_name = $arrPayData[0];
				$orderRow->virtual_number = $arrPayData[1];

				$product_name = explode('] ', $orderRow->productname);
				$orderRow->product_code = $product_name[1] ? $product_name[1] : $orderRow->productname;


				$orderRow->quantity = $orderRow->quantity;
				$orderRow->option_str = implode(" / ", $arrOption);

				if(!$orderRow->deli_code) $orderRow->deli_code = $orderRow->deli_num;
				#$orderRow->confirm_url = "주문 확정 URL";
				#주문 상세로 연결
				$orderRow->confirm_url = "http://".$_SERVER['HTTP_HOST']."/m/mypage_orderlist_view.php?ordercode=".$orderRow->ordercode;
//                 $orderRow->play_store_url = "https://play.google.com/store/apps/details?id=com.sejung.android";
//                 $orderRow->app_store_url = "https://itunes.apple.com/kr/app/hook-sejung-deohug-o2o-syopingmol/id1153471571";

				$arrListOrder[$orderRow->ordercode] = $orderRow;

				$count++;
			}

			$arraySendOrder = array("template"=>$template);

			if($count > 0){
				foreach($arrListOrder as $key => $row) {
					$order_product = $row->productcode;
				   // $order_product_name = $row->productname;
					if($row->order_count > 0){
						$order_product .= " 외 ".number_format($row->order_count)."건";
					}else{
					}
					$arraySendOrder['ordercode'] = $row->ordercode;																			# 주문번호
				   // $arraySendOrder['ordercodename'] = $order_product_name;																			# 주문번호
					$arraySendOrder['order_name'] = $row->order_name;																		# 주문자
					$arraySendOrder['order_product'] = $order_product;																		# 상품
					$arraySendOrder['order_price'] = number_format($orderPriceTotal);														# 결제 금액
					$arraySendOrder['deli_address'] = $row->deli_address;																	# 배송지
					$arraySendOrder['order_url'] = $row->order_url;
					$shortUrlOrder = $this->getShortURL($arraySendOrder['order_url']);
					$arraySendOrder['order_url'] = $shortUrlOrder ? $shortUrlOrder : $arraySendOrder['order_url'];							# 주문 내역 주소

					$arraySendOrder['brand_name'] = $row->brand_name;																		# 브랜드 명
					$arraySendOrder['deposit_price'] = number_format($orderPriceTotal);													# 입금금액
					$arraySendOrder['virtual_number'] = $row->virtual_number;															# 입금계좌
					$arraySendOrder['bank_name'] = $row->bank_name;																		# 입금은행
					$arraySendOrder['cellphone'] = $row->sender_tel;																			# 받는사람 전화번호
//	                    $arraySendOrder['cellphone'] = '01028413981';																			# 받는사람 전화번호

					$arraySendOrder['quantity'] = $row->quantity;																					# 수량
					$arraySendOrder['product_code'] = $row->product_code;																# 상품 코드 (명)

					$arraySendOrder['option_str'] = $row->option_str1." / ".$row->option_str2;
				}
				$json = json_encode($arraySendOrder);
				$this->jsonData = $json;

				$this->makeJsonDecodeData();
				$this->makeAlimTalkMsg();

			}else{
				if($temp_phone != ''){
					$this->makeAlimTalkMsg($temp_phone);
				} else {
					$this->ataDbFlag = "none";
				}
			}

        }else{
        	if($temp_phone != ''){
        		$this->jsonDecodeData->template= $template;
        		$this->makeAlimTalkMsg($temp_phone,$template);
        	} else {
        		$this->ataDbFlag = "none";
        	}
            $this->ataDbFlag = "none";
        }
    }

    function makeAlimTalkMsg($temp_phone){
    	//echo "makeAlimTalkMsg<br>";
        if($this->jsonDecodeData->template){
            $filename = realpath(dirname(__FILE__).'/')."/../alimtalk/template/".$this->jsonDecodeData->template.".php";
            $handle = fopen($filename, "r");
            $contents = fread($handle, filesize($filename));
            fclose($handle);

            // db로 변경 2017-02-10 유동혁
            /*
            $sql = "select code, message from ata_msg_talbe where code = '".$this->jsonDecodeData->template."' ";
            $res = pmysql_query( $sql, get_db_conn() );
            $row = pmysql_fetch_array( $res );
            $contents = $row['message'];
            pmysql_free_result( $res );
            */
            if( $contents ){
                foreach($this->jsonDecodeData as $kk => $vv){
                    if($kk == 'template') continue;
                    $contents = str_replace("#{".$kk."}", $vv, $contents);
                }
                $contents = str_replace("'", "''", $contents);
                $this->ataContents = $contents;
                //debug($contents);
                $this->insertAlimTalkMsg($temp_phone);
            } else {
                $this->ataDbFlag = "none";
            }
        }else{
            $this->ataDbFlag = "none";
        }
    }

    function insertAlimTalkMsg($temp_phone){
        GLOBAL $alimConf;
        /*
            insert into ata_mmt_tran (mt_pr, date_client_req, subject, content, callback, service_type, broadcast_yn, msg_status, recipient_num, msg_type, sender_key, template_code )
            values(nextval('sq_ata_mmt_tran_01'), now(), ' ', 'Test Message 입니다', ' ', '3', 'N', '1', '1009', '10', 'aaaaa22222bbbbb33333c', 'A000_00');

            subject : ''
            content : content
            callback : ''
            service_type : '3' (3-카카오톡 알림톡)
            broadcast_yn : 'N' ( 사용안함  )
            msg_status : '1' ( 1-전송대기, 2-결과대기, 3-완료 )
            recipient_num : '01000000000' ( 수신자 전화번호 )
            msg_type : '1008' ( 1008-카카오톡 알림톡, 1009-카카오톡 친구톡 )
            sender_key : 'asdasdasd' ( 카카오톡 알림톡 발신 프로필키 )
            template_code : 'WEB01' ( 템플릿 코드 )
        */
        $order_type = '';
        $t_phone = $this->jsonDecodeData->cellphone;
        
        switch ($this->jsonDecodeData->template){
        	case 'WEB01' :
        		$order_type = '주문접수';
        		break;
        	case 'WEB02' :
        		$order_type = '가상계좌 입금확인';
        		break;
        	case 'WEB03' :
        		$order_type = '신용카드 결제완료';
        		break;
        	case 'WEB04' :
        		$order_type = '배송중';
        		break;
        	case 'WEB05' :
        		$order_type = '배송완료';
        		break;
//         	case 'WEB06' :
        	case 'WEB11' :
        		$order_type = '주문취소 접수 (배송전)';
        		break;
        	case 'WEB07' :
        		$order_type = '반품신청 (배송후)';
        		break;
        	case 'WEB08' :
        		$order_type = '반품접수 (배송후)';
        		break;
        	case 'WEB09' :
        		$order_type = '환불접수 (배송후)';
        		break;
        	case 'WEB10' :
        		$order_type = '환불완료';
        		break;
        	case 'WEB16' :
        		$order_type = '업주알림';
        		$result = array_unique($this->storeArr);
        		break;
        	case 'SCC01' :
        		$order_type = '(재고 有) - 상품준비 (매장확인)';
        		break;
        	case 'SCC02' :
        		$order_type = '(재고 有) - 고객수령';
        		break;
        	case 'SCC03' :
        		$order_type = '(재고 無) - RT 완료 (발송매장)';
        		break;
        	case 'SCC04' :
        		$order_type = '회원가입';
        		$t_phone = $temp_phone;;
        		echo $order_type."=".$t_phone."<br>";
        		break;
        	case 'SCC05' :
        		$order_type = '(재고 無) - RT 상품수령';
        		break;
        	case 'SCC06' :
        		$order_type = '1:1문의 ';				// 기존 (재고 無) - 낙찰완료
        		if($temp_phone != ''){
        			$t_phone = $temp_phone;
        		}
        		break;
        	case 'SCC07' :
        		$order_type = '(재고 無) - 배송중';
        		break;
        	case 'SCC08' :
        		$order_type = '(재고 無) - 상품수령';
        		break;
        	case 'SCC09' :
        		$order_type = '(재고 無) - 배송중';
        		break;
        	case 'SCC10' :
        		$order_type = '(재고 無) - 상품수령';
        		break;
        	case 'SCC11' :
        		$order_type = '(재고 無) - 결제완료';
        		break;
        	case 'SCC12' :
        		$order_type = '(재고 無) - 고객수령';
        		break;
        	case 'WEB12' :
        		$order_type = '품절';
        		break;
        	case 'WEB13' :
        		$order_type = '매장발송(수동)';
        		break;
        	case 'WEB14' :
        		$order_type = '매장발송(자동)';
        		break;
        	case 'WEB15' :
        		$order_type = '배송지연';
        		break;
		}

		if($this->t_staus == 1){
			// 업주 알림톡 발송
			for($i = 0;$i < COUNT($result) ; $i ++){
				
				$sub_sql = "
        				SELECT name,owner_ph FROM tblstore WHERE store_code = '".$result[$i]."'
        				";
				$sub_result = pmysql_query( $sub_sql, get_db_conn() );
				while( $sub_row = pmysql_fetch_object( $sub_result ) ){
					
					$contents = str_replace("#{shop_name}", $sub_row->name, $this->ataContents);
					if($sub_row->owner_ph != '') {
						$t_phone2 = $sub_row->owner_ph;
					}
				}

				$contents = str_replace("#{pick_date}", $this->pdateArr[$i], $contents);
				$contents = str_replace("#{order_price2}", $this->priceArr[$i], $contents);
				$contents = str_replace("#{brand_name2}", $this->pronameArr[$i], $contents);
				$contents = str_replace("'", "''", $contents);
				
				$sql = "INSERT INTO kko_msg (
					serialnum
					,id
					,status
					,phone
					,callback
					,reqdate
					,msg
					,template_code
					,profile_key
					,url
					,url_button_txt
					,etc1
					,etc2
					,etc3
        		) VALUES (
					null
        			,null
        			,1
        			,'".$t_phone2."'
        			,'1661-2585'
        			,now()
        			,'".$contents."'
        			,'".$this->jsonDecodeData->template."'
        			,'".$alimConf['senderKey']."'
        			,null
        			,null
        			,'".$this->jsonDecodeData->order_name."'
        			,'".$this->jsonDecodeData->order_product."'
        			,'".$order_type."'
        		)
        		";
				backup_save_sql( $sql );
				pmysql_query( $sql, get_db_conn() );
				$this->ataDbFlag = "succ";
				if( pmysql_errno() ){
					$this->ataDbFlag = "fail";
				}
			}
		} else {
			// 기존 알림톡
			$sql = "INSERT INTO kko_msg (
					serialnum
					,id
					,status
					,phone
					,callback
					,reqdate
					,msg
					,template_code
					,profile_key
					,url
					,url_button_txt
					,etc1
					,etc2
					,etc3
        		) VALUES (
					null
        			,null
        			,1
        			,'".$t_phone."'
        			,'1661-2585'
        			,now()
        			,'".$this->ataContents."'
        			,'".$this->jsonDecodeData->template."'
        			,'".$alimConf['senderKey']."'
        			,null
        			,null
        			,'".$this->jsonDecodeData->order_name."'
        			,'".$this->jsonDecodeData->order_product."'
        			,'".$order_type."'
        		)
        		";
			//         echo $sql;
			//         exit();
			// $this->jsonDecodeData->cellphone
			backup_save_sql( $sql );
			pmysql_query( $sql, get_db_conn() );
			$this->ataDbFlag = "succ";
			if( pmysql_errno() ){
				$this->ataDbFlag = "fail";
			}
		}
		
    }


    function getOrderOptions($groupOpRow){
        $returnVal = "";
        $opt_name	= "";
        if( strlen( trim( $groupOpRow->option_str1 ) ) > 0 ) {
            $opt1_name_arr	= explode("@#", $groupOpRow->option_str1);
            $opt2_name_arr	= explode(chr(30), $groupOpRow->option_str2);
            if($groupOpRow->company_code!='99') {
                $groupOpRow->option_str1=$opt1_name_arr[1];
                $groupOpRow->option_str2=$opt2_name_arr[1];
            }
            $s_cnt	= 0;
            for($s=0;$s < sizeof($opt1_name_arr);$s++) {
                if(($groupOpRow->company_code!='99' && $s==1)||$groupOpRow->company_code=='99') {
                    if ($opt2_name_arr[$s]) {
                        if ($s_cnt > 0) $opt_name .= " / ";
                        $opt_name .= $opt1_name_arr[$s] . ' : ' . $opt2_name_arr[$s];
                        $s_cnt++;
                    }
                }else{
                    if( $groupOpRow->company_code!='99' && $s==0 ){
                        $commonColor = common_color( array( 'color'=>$opt2_name_arr[$s] ) );
                        $opt2_name_arr[$s] = $commonColor;
                    }
                    $opt_name2= $opt1_name_arr[$s] . ' : ' . $opt2_name_arr[$s];
                }
            }
        }

        if( strlen( trim( $groupOpRow->text_opt_subject ) ) > 0 ) {
            $text_opt_subject_arr	= explode("@#", $groupOpRow->text_opt_subject);
            $text_opt_content_arr	= explode("@#", $groupOpRow->text_opt_content);

            for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
                if ($text_opt_content_arr[$s]) {
                    if ($opt_name != '') $opt_name .= " / ";
                    $opt_name .= $text_opt_subject_arr[$s] . ' : ' . $text_opt_content_arr[$s];
                }
            }
        }


        $opt_string = $opt_name;
        if($opt_name2) $opt_string .= " / ".$opt_name2;

        if($opt_name || $opt_name2){
            $returnVal = $opt_string;
        }


        $returnArray = array("opt1"=> $opt_name, "opt2"=>$opt_name2);

        $returnArray = array_filter($returnArray);

        return $returnArray;
    }


    function getShortURL($longURL) {
        # 통신 지연으로 인해 주석처리 2017-01-05 유동혁
        /*
        GLOBAL $alimConf;
        if($alimConf['googleShortUrlKEy']){
            $curlopt_url = "https://www.googleapis.com/urlshortener/v1/url?key=".$alimConf['googleShortUrlKEy'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $curlopt_url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $jsonArray = array('longUrl' => $longURL);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonArray));
            $shortURL = curl_exec($ch);    curl_close($ch);
            $result_array = json_decode($shortURL, true);
            $shortURL = curl_exec($ch);
            curl_close($ch);

            return $result_array['id'];
        }else{
            return "";
        }
        */
        return $longURL;
    }


}
?>
