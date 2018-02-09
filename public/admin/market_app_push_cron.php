<?php

/*deco@182.162.154.102:/public/data/config.php
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/adminlib.php");
*/
	///data/WWWROOT/test-deco/public/admin/market_app_push_cron.php

	$allSumCount = sendPushCurl();
	echo $allSumCount;

	function sendPushCurl(){
		$searchMonth = date('Y-m-d');
		$searchHour = date('H');
		$searchMinute = substr(date('i'), 0, 1)."0";

		//$sql_s = "SELECT * FROM tblpushlist WHERE push_send_flag = 0 AND push_send_type = 'r' AND push_send_day = '".$searchMonth."' AND push_send_hour = '".$searchHour."'";
		$sql_s = "SELECT * FROM tblpushlist WHERE push_send_flag = 0 AND push_send_type = 'r' AND push_send_day = '".$searchMonth."' AND push_send_hour = '".$searchHour.":".$searchMinute."'";
		$result_s = pmysql_query($sql_s, get_db_conn());
		$allSumCount = 0;
		while ($row_s=pmysql_fetch_object($result_s)) {
			$sqlPush = "select * from tblpushlist where no = '".$row_s->no."'";
			$resultPush = pmysql_query($sqlPush, get_db_conn());
			$rowPush = pmysql_fetch_object($resultPush);

			$startDate = $rowPush->push_start_date;
			$endDate = $rowPush->push_end_date;
			$memSearchType = $rowPush->mem_search_type;
			if($memSearchType == 'a'){
				$sql = "select id, push_token from tblmember where (push_token != '' or push_token_ios != '')";
				$result = pmysql_query($sql,get_db_conn());
			}else{
				$searchsql[] = "1=1";
				$searchsql[] = "(b.push_token != '' or b.push_token_ios != '')";
				if($startDate && $endDate) {
					$date_start = str_replace("-","",$startDate)."000000";
					$date_end = str_replace("-","",$endDate)."235959";

					$searchsql[] = "ordercode >= '{$date_start}' AND ordercode <= '{$date_end}' ";
				}
				
				$sql = "
								select 
									a.id, b.push_token, b.push_token_ios
								from 
									tblorderinfo a JOIN 
									tblmember b on a.id = b.id 
								where 
									".implode(" AND ", $searchsql)."
								group by a.id, b.push_token, b.push_token_ios";
				$result = pmysql_query($sql,get_db_conn());
			}

			$arrMsgDb = array();
			$pushCount1st = $pushCount2ns = -1;



			while ($row=pmysql_fetch_object($result)) {
				if($row->push_token){
					$pushCount2ns++;
					if($pushCount2ns%900 == 0){
						$pushCount2ns = 0;
						$pushCount1st++;
						$arrMsgDb[$pushCount1st][$pushCount2ns][token] = $row->push_token;
					}else{
						$arrMsgDb[$pushCount1st][$pushCount2ns][token] = $row->push_token;
					}
				}

				if($row->push_token_ios){
					$arrMsgDbIos[] = $row->push_token_ios;
				}
			}
			pmysql_free_result($result);

			$totalSendCount = 0;


			//debug($arrMsgDb);
			$strNo = $row_s->no;
			$strTitle = $rowPush->push_title;
			$strMsg = $rowPush->push_content;
			$strUrl = $rowPush->push_url;
			$strBigPicture = $rowPush->push_img;
			if(!$strBigPicture) $strBigPicture = "/";

			if($row_s->push_os == "Android"){
				# 안드로이드의 경우 처리

				foreach($arrMsgDb as $tokenArrkey => $tokenArrVal){
					//debug($tokenArrVal);
					// 헤더 부분
					$headers = array(
							'Content-Type:application/json',
							'Authorization:key=AIzaSyCN3PAJs3LIAvwAmhJdwOYZbbIkkwnrAeE'
					);
				 
					// 푸시 내용, data 부분을 자유롭게 사용해 클라이언트에서 분기할 수 있음.
					$arr = array();
					$arr['data'] = array();

					foreach($tokenArrVal as $k => $v){
						//$arr['data']['message'] = '111::::푸시 테스트02::::푸시 내용 ABCD~::::http://dev3-franchise.synccommerce.co.kr/sale/edit';
						$arr['data']['message'] = $strNo."::::".$strTitle."::::".$strMsg."::::".$strUrl."::::".$strBigPicture;
						$arr['registration_ids'][$k] = $v['token'];
					}

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arr));
					$response = curl_exec($ch);
					curl_close($ch);
				 
					// 푸시 전송 결과 반환.
					$obj = json_decode($response);
				 
					// 푸시 전송시 성공 수량 반환.
					$cnt = $obj->{"success"};
					if(!$cnt) $cnt = 0;
					$totalSendCount += $cnt;
				}



			}else{
				# 아이폰의 경우 처리
				 
				$apnsPort = 2195;
				 
				// dev
				//$apnsHost = 'gateway.sandbox.push.apple.com';
				//$apnsCert = $_SERVER['DOCUMENT_ROOT']."/apns_develop.pem";  
				 
				// production
				$apnsHost = 'gateway.push.apple.com';
				// 본섭 테섭 폴더 체크 필요
				// $apnsCert = "/data/WWWROOT/deco/public/apns.pem";  
				$apnsCert = realpath(dirname(__FILE__).'/')."/../apns.pem";
				$iOsMsg = '';

				#debug($arrMsgDbIos);
				foreach($arrMsgDbIos as $tokenArrVal){
					$streamContext = stream_context_create();
					stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

					$apns = null;
					$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);

					#debug($strMsg);

					if ($apns) {
						$payload['aps'] = array('alert' => $strMsg, 'badge' => 1, 'sound' => 'default');
						$output = json_encode($payload);
						$token = pack('H*', str_replace(' ', '', $tokenArrVal));

						$apnsMessage = chr(0) . chr(0) . chr(32) . $token . chr(0) . chr(strlen($output)) . $output;
						$writeFlag = fwrite($apns, $apnsMessage);
						$totalSendCount++;


						/*
						# 전송 실패
						if(!$writeFlag){
							$errorResponse = @fread($apns, 6);
							if ($errorResponse != FALSE) {
								$iOsMsg .= '에러값 = [' . $errorResponse . ']   <br>';
							}
						}else{
							$totalSendCount++;
						}*/
					}else{
						# 전송 실패
					}
					fclose($apns);
				}

			}

			$push_update_sql = "UPDATE tblpushlist SET push_count = '".$totalSendCount."', push_send_flag = 1 WHERE no = '".$row_s->no."'";
			pmysql_query($push_update_sql,get_db_conn());

			$allSumCount += $totalSendCount;
		}
		pmysql_free_result($result_s);

		return $allSumCount;
	}




































	class DBConn {
		public $con_str="";
		public $connect="";
	}
	
	function get_db_conn() {
		global $DB_CONN;
		if (!$DB_CONN) {
			$f=@file(realpath(dirname(__FILE__).'/')."/../data/config.php");
			#$f=@file("/data/WWWROOT/deco/public/data/config.php");
			for($i=1;$i<=4;$i++) $f[$i]=trim($f[$i]);
			$DB_CONN = @pmysql_connect($f[1],$f[2],$f[3]);
			$status = @pmysql_select_db($f[4],$DB_CONN);

			if (!$status) {
			   echo("DB Select 에러가 발생하였습니다.");
			}
		}
		return $DB_CONN;
	}

	function pmysql_connect($hostname, $user_id, $password ) {
		$dbconn = new DBConn();
		$dbconn->con_str = "host=$hostname user=$user_id password=$password";
		$dbconn->connect = @pg_connect($dbconn->con_str." dbname=postgres");
		return $dbconn;
	}


	function pmysql_query( $query, $connect=NULL ) {
		$injectionCode = "--";
		if(strstr($query, $injectionCode)){
			$query = str_replace($injectionCode, "－－", $query);
		}
		$time[] = microtime();
		if(is_null($connect)) $connect = get_db_conn();
		$result = @pg_query($connect->connect,$query);
		return $result;
	}

	function pmysql_select_db( $dbname, $connect ) {
		$connect->connect = @pg_connect($connect->con_str." dbname=$dbname");
		return ($connect->connect!==FALSE);
	}

	function pmysql_free_result($result) {
		if($result===FALSE) return FALSE;
		return @pg_free_result($result);
	}
	function pmysql_fetch_object($result) {
		if($result===FALSE) return FALSE;
		return @pg_fetch_object($result);
	}

	function debug($data)
	{
		echo "<div style='background:#ffffff;text-align:left;'>";
		echo "<pre>"; print_r($data); echo "</pre>"; 
		echo "</div>";
	}
?>