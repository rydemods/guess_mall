<?
	/**
	* sync.class.php 클래스
	* 최초작성일 : 2016-08-16
	*
	* @author : back hee yeon(libe90@commercelab.co.kr)
	* @since : 2016-08-16 libe90 세정API 호출 전부 추가
	*/
	class Sync{

		var $authKey;
		var $encryptKey;
		var $sendUrl;
		var $sendUri;
		var $sendType;
		var $jsonPostData;
		var $arrayData;
		var $timeStamp;
		var $flagSuccess;
		var $returnArray;

		var $jsonDecodeData;
		var $debug = true;
		var $elapsed_time;
        var $ssl_verifypeer = false; // ssl 적용 2016-12-08 유동혁

		function Sync(){
			# 키값들은 추후 바뀔수 있고, 현재 IP마다 다르다고 함.
			$arrAuthKeys = array(
				"218.234.32.9"    => "c854170e90943506a20bb6e284576a242398d1f9658735b61831fd5779c464cb",
				"218.234.32.12"   => "a8052b5fadc47918d2e506492d773e8b",
				"218.234.32.59"   => "3014795a8297c2cbca07cd9d1070510a",
				"218.234.32.63"   => "2b356720ce6646f2c5a97f25c633ad420fb2fddf02f6ff2e7b0c3166379aca0b",
				"218.234.32.105"  => "80a595373072feee2a05c94e86342097",
				"218.234.32.118"  => "85a69110058effacd82d1e9d8d890b6e",
				"218.234.32.123"  => "abe977c839a333f0545ac74aed11c7ac",
				"182.162.154.106" => "6e39a4f3f5c0d2c1b694b72abc66541accd90a6745520ab04fecf122a5845f4d69bf65",
				"182.162.154.107" => "bca6e3d56c9cb766b8223bf0842f131cbbafd6d4238c75476471e1344fbe2d84",
				"182.162.154.111" => "63248e208d8f0b0435699a3a1261704f6e4db9aa535f4b7daae1e9ff25e3bae1",
				"182.162.154.110" => "0f0627b984ea0b3108830c5beb594edf9dc11ecae067a5e3bbd0863969c2afb5", // hott 실서버 2
				"117.52.153.101"  => "0f0627b984ea0b3108830c5beb594edf9dc11ecae067a5e3bbd0863969c2afb5", // hott 실서버 2
				"52.231.36.21" => "0f0627b984ea0b3108830c5beb594edf9dc11ecae067a5e3bbd0863969c2afb5",
				"52.231.37.17" => "0f0627b984ea0b3108830c5beb594edf9dc11ecae067a5e3bbd0863969c2afb5",
				"11.0.1.4" => "0f0627b984ea0b3108830c5beb594edf9dc11ecae067a5e3bbd0863969c2afb5",
				"11.0.1.5" => "0f0627b984ea0b3108830c5beb594edf9dc11ecae067a5e3bbd0863969c2afb5",
				"52.231.28.42" => "0f0627b984ea0b3108830c5beb594edf9dc11ecae067a5e3bbd0863969c2afb5"
			);

			//$this->authKey = "6780cfdd0d87982911ee3ecf34c07d9a";
			if(!$_SERVER['SERVER_ADDR']){

				//$_SERVER['SERVER_ADDR']="182.162.154.120";

                $command="/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'";
                $localIP = @exec ($command);

                if($localIP) $_SERVER['SERVER_ADDR'] = $localIP;
                else $_SERVER['SERVER_ADDR']="117.52.153.101";
			}
			$this->authKey = $arrAuthKeys[$_SERVER['SERVER_ADDR']];
            # hott 개발서버
			//$this->sendUrl = "https://dev-shinwon.synccommerce.co.kr/openApi";
			$this->sendUrl = "https://shinwon.synccommerce.co.kr/openApi";


			$f = fopen("../data/backup/sync_key_".date("Ymd").".txt","a+");
			fwrite($f,"########################################## START sync_key_".date("Y-m-d H:i:s")."\r\n");
			fwrite($f," SERVER_ADDR = ".$_SERVER['SERVER_ADDR']."\r\n");
			fwrite($f," authKey = ".$this->authKey."\r\n");
			fwrite($f,"########################################## END sync_key_".date("Y-m-d H:i:s")."\r\n\r\n");
			fclose($f);
			chmod("../data/backup/sync_key_".date("Ymd").".txt",0777);

			

            # hott 실서버 2016-10-14 유동혁
            //$this->sendUrl = "https://hott.synccommerce.co.kr/openApi";
            # ?
			//$this->sendUrl = "http://210.182.19.104:9090";

            // ssl 적용 2016-12-08 유동혁
            if( $_SERVER['HTTPS'] == "on" ){
                $this->ssl_verifypeer = true;
            }

		}

		function call() {
			$this->log( "[{$httpcode}] {$this->sendType} ".$this->elapsed_time." {$curl_getinfo[url]}\r\n\t[jsonPostData] {$this->jsonPostData}\r\n\t[output]{$server_output}\r\n" );
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->sendUrl.$this->sendUri);


			if($this->sendType == "POST"){
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->jsonPostData);
			}elseif($this->sendType == "PUT"){
				$headers[] = 'Content-Length: '.strlen($this->jsonPostData);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->jsonPostData);
			}else{
				// GET
				$this->jsonPostData = "";
				curl_setopt($ch, CURLOPT_POST, 0);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
            // ssl 적용 2016-12-08 유동혁
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1 );

			$headers = array( "Content-type: multipart/form-data", "Content-Type: application/x-www-form-urlencoded; charset=utf-8" );

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			if($this->debug) $time[] = microtime();
			$server_output = curl_exec ($ch);
			if($this->debug) $time[] = microtime();
			$this->elapsed_time = get_microtime($time[0],$time[1],3);

			if( isdev() ){
				$this->output = $server_output;
			}
			$apiOrderData = json_decode( $server_output );

			if( is_array( $server_output->message ) ) {
				foreach( $server_output->message as $msgVal ){
					$tmpMessage .= $msgVal.PHP_EOL;
				}
			} else {
				$tmpMessage = $server_output->message;
			}

			if($this->debug){
				$curl_getinfo = curl_getinfo($ch);
				$httpcode = $curl_getinfo[http_code];
			}else
				$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close ($ch);
			if($httpcode == "200"){
				$jsonDecodeData = json_decode($server_output);
				$jsonDecodeData2 = json_encode($this->jsonPostData);
				$this->jsonDecodeData = $jsonDecodeData;
				$this->flagSuccess = "succ";
				$this->log( "[{$httpcode}] {$this->sendType} ".$this->elapsed_time." {$curl_getinfo[url]}\r\n\t[jsonPostData] {$jsonDecodeData->message}\r\n\t[output]{$jsonDecodeData2}\r\n\t[decode]".json_encode($this->returnArray)."\r\n" );
			}else{
				$this->log( "[{$httpcode}] {$this->sendType} ".$this->elapsed_time." {$curl_getinfo[url]}"."::fail" );
				$this->flagSuccess = "fail";
				$this->httpcode = $httpcode;
				$this->log( "[{$httpcode}] {$this->sendType} ".$this->elapsed_time." {$curl_getinfo[url]}\r\n\t[jsonPostData] {$this->jsonPostData}\r\n\t[output]{$server_output}\r\n" );
			}
			$this->flagSuccessChk=$jsonDecodeData->message;
		}

		function log($txt){
			if($this->debug){
				$d = date("Y-m-d H:i:s ");
				$n = DirPath.DataDir."log/api-shinwon-".date("Y-m-d").".log";
				$f = fopen($n,"a+");
				fwrite($f,$d.$txt."\r\n");
				fclose($f);
				@chmod($n,0777);
			}
		}


		function OrderInsert($arrayData){
			$oiSql = "
				SELECT
					oi.ordercode, oi.id, oi.price, oi.paymethod,
					oi.is_mobile, oi.oi_step1, oi.oi_step2, oi.paymethod,
					oi.sender_name, oi.sender_email, oi.sender_tel,
					oi.receiver_name, oi.receiver_tel2, oi.receiver_addr, oi.receiver_tel1,
					oi.regdt, oi.order_msg2, oi.sync_yn,
					op.tot_price, op.original_price, op.dc_price, oi.reserve, op.use_point, op.use_epoint,
					op.deli_price
				FROM
				(
					SELECT
						ordercode, id, paymethod, is_mobile, 
						oi_step1, oi_step2, sender_name, sender_email, sender_tel,
						receiver_name, receiver_tel1, receiver_tel2, receiver_addr,
						regdt, price, order_msg2, reserve, (select min(sync_yn) from tblsync_check where ordercode='".$arrayData[ordercode]."' and order_type='I') as sync_yn
					FROM
						tblorderinfo
					WHERE
						ordercode = '".$arrayData[ordercode]."'
				) AS oi
				JOIN
				(
					SELECT
						ordercode, SUM( ( price * quantity ) - coupon_price - use_point - use_epoint + deli_price ) AS tot_price,
						SUM( price * quantity ) AS original_price, SUM( coupon_price ) AS dc_price, 
						SUM( deli_price ) AS deli_price, sum(use_point) as use_point, sum(use_epoint) as use_epoint 
					FROM
						tblorderproduct
					WHERE
						ordercode = '".$arrayData[ordercode]."'
						{$arrayData[sync_idx]}
					AND
						(delivery_type != '0' or (length(store_code) > 0 and store_code!='A1801B') )
					GROUP BY 
						ordercode
				) AS op ON( oi.ordercode = op.ordercode )
				
			";
			$oiResult = pmysql_query( $oiSql, get_db_conn() );
			$oiRow    = pmysql_fetch_object( $oiResult );
			pmysql_free_result( $oiResult );

			if( $oiRow ){

				$tmpPayMethod    = '';      // 아자샵 paymethod type  => 싱크커머스 paymethod type 변경
				$tmpLocType      = '';      // 아자샵 lociotion type  => 싱크커머스 loction type 변경
				$tmpStatus       = '';      // 아자샵 주문단계 type   => 싱크커머스 주문단계 type 변경
				$tmpReceiverTel2 = '';      // 수취인 선택 전화번호

				// 결제방법 ( B : 계좌관련, C : 카드관련, T 핸드폰 )
				/*
				if( strstr( "VBOQ", $oiRow->paymethod[0] ) ){ // 계좌이체, 무통장, 가상계좌, 매매보호 가상계좌
					$tmpPayMethod = 'B';
				} elseif( strstr( "CY", $oiRow->paymethod[0] ) ){ // 카드, 페이코
					$tmpPayMethod = 'C';
				} elseif( strstr( "M", $oiRow->paymethod[0] ) ){ // 휴대폰
					$tmpPayMethod = 'T';
				}
				*/
				$tmpPayMethod = substr($oiRow->paymethod[0],0,1);

				// location >> PC : O, Mobile : N
				if( $oiRow->is_mobile == '0' ){
					$tmpLocType = 'O';
				} else {
					$tmpLocType = 'N';
				}

				// 주문상태
				// ( S:발송준비 / Y:배송중, 반송신청 / C:주문취소 / R:반송 / D:취소요청 / E:환불대기 / N:미입금,입금확인,주문실패 / H:배송(정산보류) / F:배송완료 )
				if( $oiRow->oi_step2 != 0 ){ // 취소상태
					if( $oiRow->oi_step2 == 40 ) { // 취소신청
						$tmpStatus = 'D';
					} elseif( $oiRow->oi_step2 == 42 ) { // 환불접수
						$tmpStatus = 'E';
					} elseif( $oiRow->oi_step2 == 44 ) { // 주문취소완료 / 환불완료
						$tmpStatus = 'C';
					} elseif( $oiRow->oi_step2 == 54 ) { // 주문실패
						$tmpStatus = 'N';
					}
				} else {
					if( $oiRow->oi_step1 == 1 ){ // 입금확인 1 >> 배송준비중 2
						$tmpStatus = 'A'; // 입금확인상태 변경 S >> A
					} else { // 미입금 / 입금확인
                        // 재주문 입금확인 추가 2016-10-13 유동혁
                        if( $arrayData['redelivery_type'] == 'G' ){
                            $tmpStatus = 'S';
                        } else {
                            $tmpStatus = 'N';
                        }
					}
				}
				/*
                     elseif( $oiRow->oi_step1 == 3 ) { // 배송중 >> 오류
                        $tmpStatus = 'Y';
                    } elseif( $oiRow->oi_step1 == 4 ) { // 배송완료 >> 오류
                        $tmpStatus = 'F';
                    }
                */

				// 수취인 전화번호 2
				if( strlen( str_replace( '-', '', $oiRow->receiver_tel1 ) ) >= 9 ){
					$tmpReceiverTel2 = $oiRow->receiver_tel1;
				}
                # 재주문 추가 2016-10-13 유동혁
                $old_order_code = '';
                $old_prod_idx = '';
                if( $arrayData['redelivery_type'] == 'G' ){
                    $oldSql = "
                        SELECT 
                          a.oldordno, b.opt2_name, c.idx
                        FROM tblorderinfo a 
                        JOIN tblorderproduct b on a.ordercode = b.ordercode
                        JOIN tblorderproduct c on ( a.oldordno = c.ordercode AND b.opt2_name = c.opt2_change AND c.redelivery_type = 'G' )
                        WHERE a.ordercode = '".$arrayData[ordercode]."' 
                        Order by b.idx asc
                    ";
                    $oldRes = pmysql_query( $oldSql, get_db_conn() );
                    $oldRow = pmysql_fetch_object( $oldRes );
                    pmysql_free_result( $oldRes );
                    $old_order_code = $oldRow->oldordno;
                    $old_prod_idx   = $oldRow->idx;
                }

				# 주문 내용
				// 필수내용
				$orderCodeArray['auth_key']         = $this->authKey;                   // 인증키
				$orderCodeArray['order_code']       = $arrayData[ordercode];                // 주문코드
				$orderCodeArray['original_price']   = $oiRow->original_price;    // 총 주문금액
				$orderCodeArray['tot_price']        = $oiRow->tot_price;         // 총 결제금액
				$orderCodeArray['pay_method']       = $tmpPayMethod;             // 결제방법 ( B : 계좌관련, C : 카드관련, T 핸드폰 )
				$orderCodeArray['loc_type']         = $tmpLocType;               // 접속방법
				// 주문상태
				// ( S:발송준비 / Y:배송중, 반송신청 / C:주문취소 / R:반송 / D:취소요청 / E:환불대기 / N:미입금,입금확인,주문실패 / H:배송(정산보류) / F:배송완료 )
				$orderCodeArray['status']           = $tmpStatus;
				$orderCodeArray['sender_name']      = $oiRow->sender_name;       // 주문자명
				$orderCodeArray['sender_email']     = $oiRow->sender_email;      // 주문자 email
				$orderCodeArray['sender_tel']       = $oiRow->sender_tel;        // 주문자 전화번호
				$orderCodeArray['receiver_name']    = $oiRow->receiver_name;     // 수취자명
				$orderCodeArray['receiver_tel1']    = $oiRow->receiver_tel2;     // 수취자 전화번호
				$orderCodeArray['receiver_addr']    = $oiRow->receiver_addr;     // 수취자 주소
				$orderCodeArray['order_date']       = strlen($oiRow->regdt) > 0 ? $oiRow->regdt : substr($arrayData[ordercode],0,14);             // 주문일( type Date : 2016-07-25 )

				// 선택 내용
				$orderCodeArray['coupon_price']     = $oiRow->dc_price;          // 쿠폰 사용액
				$orderCodeArray['point_price']      = $oiRow->use_point;       // 총 마일리지 사용 금액
				$orderCodeArray['epoint_price']      = $oiRow->use_epoint;       // 총 e포인트 사용 금액
				$orderCodeArray['delivery_price']   = $oiRow->deli_price;        // 배송비
				$orderCodeArray['receiver_tel2']    = $tmpReceiverTel2;          // 수취자 전화번호2
				$orderCodeArray['save_point']       = $oiRow->reserve;   	 // 적립될 포인트
				$orderCodeArray['discount']         = $oiRow->tot_price;         // 할인된 금액 ( 총 결제금이랑 같음 )
				$orderCodeArray['delivery_message'] = $oiRow->order_msg2;        // 배송 msg

                # 재주문 추가
                if( $arrayData['redelivery_type'] == 'G' ){
                    $orderCodeArray['old_order_code'] = $old_order_code;    // 기존 주문코드
                    $orderCodeArray['old_prod_idx']   = $old_prod_idx;      // 기존 상품 idx
                }

				# orderproduct 반송에 관련된 내용은 빠져있음
				$opSql = "
					SELECT
						ordercode,( ( ( price + option_price ) * quantity ) - coupon_price - use_point - use_epoint ) AS real_price,
						idx, productcode, quantity, price,
						delivery_type, op_step, productname, coupon_price, deli_price,
						use_point, use_epoint, reserve, deli_com, deli_num,
						order_conf_date, store_code,
						opt1_name, opt2_name, reservation_date,
						reserve, gps_x, gps_y
					FROM
						tblorderproduct
					WHERE
						ordercode = '".$arrayData[ordercode]."'
						{$arrayData[sync_idx]}
					AND
						(delivery_type != '0' or (length(store_code) > 0 and store_code!='A1801B') )
				";
				$opResult = pmysql_query( $opSql, get_db_conn() );
				$cnt = 0;
				while( $opRow = pmysql_fetch_object( $opResult ) ){
					//$tmpDetailArray[$cnt] = $detailArray; // 상세주문
					$tmpDetailArray[$cnt] = array(); // 상세주문

					$tmpSize         = $opRow->opt2_name; // 상품 size
					$tmpOpStaus      = ''; // 주문상태
					# 발생구분
					//if($opRow->delivery_type=='0'){
					//	$tmpDeliveryType = '2';
					//}else if($opRow->delivery_type=='2'){
					//	$tmpDeliveryType = '3';
					//}else{
					//	$tmpDeliveryType = $opRow->delivery_type;
					//}
					$tmpDeliveryType = $opRow->delivery_type;
					# 주문상태
					// S:발송준비, Y:배송중, 반송신청, C:주문취소, R:반송, D:취소요청, E:환불대기, N:미입금,입금확인,주문실패, H:배송(정산보류), F:배송완료
					if( $opRow->op_step == 1 ){ // 입금확인
						$tmpOpStaus = 'A';
					} elseif( $opRow->op_step == 2 ){ // 발송준비
						$tmpOpStaus = 'S';
					} elseif( $opRow->op_step == 3 ) { // 배송중
						$tmpOpStaus = 'Y';
					} elseif( $opRow->op_step == 4 ) { // 배송완료
						$tmpOpStaus = 'F';
					} elseif( $opRow->op_step == 40 ) { // 취소요청
						$tmpOpStaus = 'D';
					} elseif( $opRow->op_step == 44 ) { // 취소완료
						$tmpOpStaus = 'C';
					} else {
						$tmpOpStaus = 'N';
					}
					$orderCodeArray['card_id'] = $oiRow->card_id;

					# 주문상세 내용
					// 필수
					$tmpDetailArray[$cnt]['order_prod_idx'] = $opRow->idx;              // 상품 index
					$tmpDetailArray[$cnt]['product_code']   = $opRow->productcode;  // 제품코드
					$tmpDetailArray[$cnt]['size']   		= $tmpSize;  // 사이즈코드
					$tmpDetailArray[$cnt]['quantity']       = $opRow->quantity;         // 수량
					$tmpDetailArray[$cnt]['price']          = $opRow->real_price;       // 실결제금액
					$tmpDetailArray[$cnt]['delivery_type']  = $tmpDeliveryType;         // 발생구분
					$tmpDetailArray[$cnt]['status']         = $tmpOpStaus;              // 주문상태

					// 선택
					$tmpDetailArray[$cnt]['item_name']      = $opRow->productname;      // 상품명
					$tmpDetailArray[$cnt]['coupon_price']   = $opRow->coupon_price;     // 쿠폰적용금액
					$tmpDetailArray[$cnt]['delivery_price'] = $opRow->deli_price;       // 배송비
					$tmpDetailArray[$cnt]['point_price']    = $opRow->use_point;        // 사용적립금
					$tmpDetailArray[$cnt]['epoint_price']    = $opRow->use_epoint;        // e포인트
					$tmpDetailArray[$cnt]['save_point']     = $opRow->reserve;  // 상품별 적립금
					$tmpDetailArray[$cnt]['delivery_com']   = $opRow->deli_com;         // 배송회사코드
					$tmpDetailArray[$cnt]['delivery_num']   = $opRow->deli_num;         // 송장번호
					$tmpDetailArray[$cnt]['delivery_date']  = $opRow->order_conf_date;  // 배송완료일
					$tmpDetailArray[$cnt]['receive_date']   = $opRow->reservation_date; // 방문예정일

					# 당일발송은 gps를 따로 저장해둔다
					if( $opRow->delivery_type == '3' ){
						$tmpDetailArray[$cnt]['gps_x'] = $opRow->gps_x; // 고객 수령위치 좌표 x
						$tmpDetailArray[$cnt]['gps_y'] = $opRow->gps_y; // 고객 수령위치 좌표 y
					}

					$tmpDetailArray[$cnt]['store_code']       = $opRow->store_code;        // 매장코드
					$cnt++;
					# 주문이 있을경우 true
					$orderStateFlag = true;
				}
				pmysql_free_result( $opResult );

				# 주문 상세정보
				$orderCodeArray['details'] = json_encode( $tmpDetailArray );
			}
			$this->jsonPostData = $orderCodeArray;

			$this->sendType = "POST";
			$sendUri = "/orders";
			$this->sendUri = $sendUri;
			$this->call();
			if($this->flagSuccessChk == "success"){
				$usql = "update tblsync_check set sync_yn = 'Y' where ordercode = '".$arrayData[ordercode]."' and order_type='I'";
				pmysql_query($usql);

				$rtn=$this->returnArray;
			}else{
				$rtn='fail';
			}
			return $rtn;
		}

		function StatusChange($arrayData){

			$orderArray=Array(
				'auth_key'=>$this->authKey,                   // 인증키
				'_method'=>'put',
				'order_code'=>$arrayData[ordercode],
			);

            // 송장처리 2016-11-28 유동혁
            if( $arrayData[sync_status] == 'Y' ){
                // 회사 명
                $orderArray['details'][0][delivery_name] = $arrayData[delivery_name];
                // 송장회사 코드
                $orderArray['details'][0][delivery_com] = $arrayData[delivery_com];
                // 송장번호
                $orderArray['details'][0][delivery_num] = $arrayData[delivery_num];
                // 발송일
                $orderArray['details'][0][delivery_send_date] = $arrayData[delivery_send_date];
            }

			$orderArray['details'][0][order_prod_idx]=$arrayData[sync_idx];
			/*sync_status:D-취소신청,C-취소,M-물류,F-배송완료*/
			$orderArray['details'][0][status]=$arrayData[sync_status];
			$orderArray['details']=json_encode($orderArray['details']);

			# 주문 상세정보
			$orderCodeArray =  $orderArray ;

			$this->jsonPostData = $orderCodeArray;

			$this->sendType = "PUT";
			$sendUri = "/orders/status";
			$this->sendUri = $sendUri;
			$this->call();
			if($this->flagSuccess == "fail"){

				$rtn='fail';
			}else{
				$rtn=$this->returnArray;
			}
			
			return $rtn;
		}
		
		function StoreChange($arrayData){
				
			$orderArray=Array(
					'auth_key'=>$this->authKey,                   // 인증키
					'_method'=>'put',
					);
			// 			// 송장처리 2016-11-28 유동혁
			// 			if( $arrayData[sync_status] == 'Y' ){
			// 				// 회사 명
			// 				$orderArray['details'][0][delivery_name] = $arrayData[delivery_name];
			// 				// 송장회사 코드
			// 				$orderArray['details'][0][delivery_com] = $arrayData[delivery_com];
			// 				// 송장번호
			// 				$orderArray['details'][0][delivery_num] = $arrayData[delivery_num];
			// 				// 발송일
			// 				$orderArray['details'][0][delivery_send_date] = $arrayData[delivery_send_date];
			// 			}
		
			// 			$orderArray['details'][0][order_prod_idx]=$arrayData[sync_idx];
			// 			/*sync_status:D-취소신청,C-취소,M-물류,F-배송완료*/
			// 			$orderArray['details'][0][status]=$arrayData[sync_status];
				
			$orderArray['details'][0][name] = $arrayData[name];
			$orderArray['details'][0][addr1] = $arrayData[address];
			$orderArray['details'][0][phone] = $arrayData[phone];
			//$orderArray['details'][0][open_time] = $arrayData[stime];
			//$orderArray['details'][0][close_time] = $arrayData[etime];
			$orderArray['details'][0][shop_code] = $arrayData[store_code];
			$orderArray['details'][0][coordinate] = $arrayData[coordinate];
			//$orderArray['details'][0][vender] = $arrayData[vender];
			//$orderArray['details'][0][category] = $arrayData[category];	// 브랜드와 구분값 변경시 매장코드가 변경됨으로 변경불가
			$orderArray['details'][0][area_code] = $arrayData[area_code];
			$orderArray['details'][0][display_yn] = $arrayData[view];
			$orderArray['details']=json_encode($orderArray['details']);
			$orderCodeArray =  $orderArray ;
		
			$this->jsonPostData = $orderCodeArray;
		
			$this->sendType = "POST";
			$sendUri = "/shops/".$arrayData[store_code];
			$this->sendUri = $sendUri;
			$this->call();
			if($this->flagSuccess == "fail"){
				$rtn='fail';
			}else{
				$rtn=$this->returnArray;
			}
			return $rtn;
		}
	}
?>
