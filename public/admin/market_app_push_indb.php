<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");

$mode = $_POST['mode'];


function sendPushCurl($pushNum, $os = ''){

    $sqlPush = "select * from tblpushlist where no = '".$pushNum."'";
    $resultPush = pmysql_query($sqlPush, get_db_conn());
    $rowPush = pmysql_fetch_object($resultPush);

    $startDate = $rowPush->push_start_date;
    $endDate = $rowPush->push_end_date;
    $memSearchType = $rowPush->mem_search_type;

    if($memSearchType == 'a'){
        $sql = "select id, push_token, push_token_ios from tblmember where (push_token != '' or push_token_ios != '')";
        if($_SERVER['REMOTE_ADDR']=='218.234.32.4') $sql .= "and id='dong' ";
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

    $arrMsgDb = $arrMsgDbIos = array();
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


    //debug($arrMsgDb);
    $strNo = $pushNum;
    $strTitle = $rowPush->push_title;
    $strMsg = $rowPush->push_content;
    $strUrl = $rowPush->push_url;
    $strBigPicture = $rowPush->push_img;
    if(!$strBigPicture) $strBigPicture = "/";

    $totalSendCount = 0;

    if($os == "Android"){
        # 안드로이드의 경우 처리


        foreach($arrMsgDb as $tokenArrkey => $tokenArrVal){
            //debug($tokenArrVal);
            // 헤더 부분
            // AIzaSyC5qK5vntLcd17n3otSjulmkIoT1TkqgMQ
            // AIzaSyBNi68syLTcNEyduuw_mFe3vqjCZwzOzYI
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
            #exdebug($arr);

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
        $iOsMsg = "";

        $apnsPort = 2195;

        // dev
        //$apnsHost = 'gateway.sandbox.push.apple.com';
        //$apnsCert = $_SERVER['DOCUMENT_ROOT']."/apns_develop.pem";

        // production
        $apnsHost = 'gateway.push.apple.com';
        $apnsCert = $_SERVER['DOCUMENT_ROOT']."/apns.pem";
//		print_r($arrMsgDbIos);exit;	 
        //debug($arrMsgDbIos);
        foreach($arrMsgDbIos as $tokenArrVal){
            //$token = $v['token'];
            //$token = "7d6bf9217274c1817111e56715c6ba640d7e60d170be79c93c2c9ed0627c643e";
            $streamContext = stream_context_create();
            stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

            $apns = null;
            $apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);

            if ($apns) {
                $payload['aps'] = array('alert' => $strMsg, 'badge' => 1, 'sound' => 'default');
                $output = json_encode($payload);
                $token = pack('H*', str_replace(' ', '', $tokenArrVal));
                $apnsMessage = chr(0) . chr(0) . chr(32) . $token . chr(0) . chr(strlen($output)) . $output;
                $writeFlag = fwrite($apns, $apnsMessage);
                $totalSendCount++;


                # 전송 실패
                /*
                if(!$writeFlag){
                    $errorResponse = @fread($apns, 6);
                    if ($errorResponse != FALSE) {
                        $iOsMsg .= '에러값 = [' . $errorResponse . ']   <br>';
                    }
                }else{
                    $totalSendCount++;
                }
                */
            }else{
                # 전송 실패
            }
            fclose($apns);
        }
    }






    /*
    $arrMsg = array();
    //$arrMsg[0][token] = "APA91bFFHvzdRDdcXvDDPnmOG1vWeAa9XF7DunNSMGf4Uvlbm5w9PjO7ySLz2wi3eSm9TnrSFG6YE7KcG62_MXHowzVdkjJwh8igrzSteSLOk-P_mSrl95AsNbA3ujYvO74gk5quDrbu";
    $arrMsg[0][token] = "WEB_554cdf518aada";
    $arrMsg[1][token] = "WEB_55141da86be63";
    debug($arrMsg);
    */

    $push_insert_sql = "UPDATE tblpushlist SET push_count = '".$totalSendCount."' WHERE no = '".$pushNum."'";
    pmysql_query($push_insert_sql,get_db_conn());

    return $totalSendCount;
}

/*
sendPushCurl(19);
exit;
*/


switch ($mode){
    case "memLoad":
        $mem_search_type = $_POST['mem_search_type'];
        $search_start = $_POST['search_start'];
        $search_end = $_POST['search_end'];


        if($mem_search_type == "a"){
            $sql = "select count(id) mem_count from tblmember";
            $result = pmysql_query($sql,get_db_conn());
            $row = pmysql_fetch_object($result);
        }else{
            $searchsql[] = "1=1";
            if($search_start && $search_end) {
                $date_start = str_replace("-","",$search_start)."000000";
                $date_end = str_replace("-","",$search_end)."235959";

                $searchsql[] = "ordercode >= '{$date_start}' AND ordercode <= '{$date_end}' ";
            }

            $sql = "select 
								count(id) mem_count
							from 
								(
									select 
										a.id 
									from 
										tblorderinfo a JOIN 
										tblmember b on a.id = b.id 
									where 
										".implode(" AND ", $searchsql)."
									group by a.id
								) a";
            $result = pmysql_query($sql,get_db_conn());
            $row = pmysql_fetch_object($result);
        }

        echo number_format($row->mem_count);
        break;


    case "insertData":
        $arrSendFlag = array("n" => "1", "r" => "0");
        $msg = "";
        $uploaddir = '../images/push';
        $uploadfile_ext = end(explode(".", basename($_FILES['push_img']['name'])));
        $newFile = time().str_pad(rand(0, 10000), 5, "0", STR_PAD_LEFT).".".$uploadfile_ext;
        $uploadfile = $uploaddir ."/". $newFile;

        $fullFileUrl = "http://".$_SERVER['HTTP_HOST']."/images/push/".$newFile;

        if(@mkdir($uploaddir, 0777)) {
            if(is_dir($uploaddir)) {
                @chmod($uploaddir, 0777);
            }
        }

        $succFlag = false;
        // 파일이 존재 하고, 텍스트 + 이미지면 업로드 처리
        if($_FILES['push_img']['name'] && $_POST['sendType'] == "m"){
            if(($_FILES['push_img']['error'] > 0) || ($_FILES['push_img']['size'] <= 0)){
                $msg = "파일 업로드에 실패하였습니다.";
            } else {
                if(!is_uploaded_file($_FILES['push_img']['tmp_name'])) {
                    $msg = "HTTP로 전송된 파일이 아닙니다.";
                } else {
                    // move_uploaded_file은 임시 저장되어 있는 파일을 ./uploads 디렉토리로 이동합니다.
                    if (move_uploaded_file($_FILES['push_img']['tmp_name'], $uploadfile)) {
                        $msg = "성공적으로 등록 되었습니다.";
                        $succFlag = true;
                    } else {
                        $msg = "파일 업로드 실패입니다.";
                    }
                }
            }
        }else{
            $msg = "성공적으로 등록 되었습니다.";
            $fullFileUrl = "";
            $succFlag = true;
        }

        if($succFlag){
            $pushContents = "";
            // 텍스트, 텍스트 + 이미지의 핸드폰 표현 방식이 달라 textarea와 text로 분기 처리
            // 이미지가 첨부되어 있으면 기종별로 여러줄 표현이 다르므로 이미지가 첨부되어 있으면 한줄만 입력하도록 함
            if($_POST['sendType'] == "t"){
                $pushContents = pg_escape_string($_POST['push_content_text']);
            }else{
                $pushContents = pg_escape_string($_POST['push_content_mix']);
            }
            $pushTitle = pg_escape_string($_POST['push_title']);

            if($_POST['mem_search_type'] == 'a'){
                $_POST['search_start'] = "";
                $_POST['search_end'] = "";
            }

            $push_os = $_POST['push_os'];


            $push_insert_sql = "
													INSERT INTO tblpushlist
														(
															push_type, push_title, push_content, 
															push_img, push_url, push_send_type, 
															push_send_day, push_send_hour, push_count, 
															date, push_start_date, push_end_date,
															push_send_flag, mem_search_type, push_os
														)
													VALUES
														(
															'".$_POST['sendType']."', '".$pushTitle."', '".$pushContents."', 
															'".$fullFileUrl."', '".$_POST['push_url']."', '".$_POST['push_send_type']."', 
															'".$_POST['push_send_day']."', '".$_POST['push_send_hour']."', 0, 
															now(), '".$_POST['search_start']."', '".$_POST['search_end']."',
															'".$arrSendFlag[$_POST['push_send_type']]."', '".$_POST['mem_search_type']."', '".$push_os."'
														)";
            pmysql_query($push_insert_sql,get_db_conn());


            $result_seq = pmysql_query("SELECT currval('tblpushlist_no_seq') as currval_seq", get_db_conn());   // 테이블 시퀀스
            $row_seq = pmysql_fetch_object($result_seq);
            //sendPushCurl($row_seq->currval_seq, $_POST['search_start'], $_POST['search_end']);
            //alert_go($msg, $_SERVER['HTTP_REFERER']);

            echo $msg."::::".$row_seq->currval_seq;
        }else{
            echo $msg."::::0";
        }
        break;



    case "sendAjaxPush":

        $result_seq = pmysql_query("SELECT push_send_type, push_os FROM tblpushlist WHERE no = '".$_POST['no']."'", get_db_conn());   // 테이블 시퀀스
        $row_seq = pmysql_fetch_object($result_seq);
        if($row_seq->push_send_type == 'n'){
            $returnData = sendPushCurl($_POST['no'], $row_seq->push_os);

            echo $returnData;
        }else{
            echo "n";
        }
        break;



    case "sendCronPush":
        break;
}
?>