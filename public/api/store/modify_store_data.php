<?php
include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$stores       = $_POST['stores']; // 매장정보
$status       = $_POST['status'];
$code         = 0;
$message      = "success";
$bSuccess     = true;
$resultTotArr = array();
$resultArr    = array();

BeginTrans();

$outText = '========================='.date("Y-m-d H:i:s")."=============================".PHP_EOL;

if( $stores ){
    try {
        foreach( $stores as $k => $v ){
            $store_code      = $v['store_code'];         //매장코드
            $pickup_yn       = $v['pickup_yn'];          //매장픽업 사용유무
            $delivery_yn     = $v['delivery_yn'];        //매장발송 사용유무
            $day_delivery_yn = $v['day_delivery_yn'];    //당일발송 사용뮤우
            $display_yn      = $v['display_yn'];         //노출

            $phone      = $v['phone'];         // 전화번호
            $addr1      = $v['addr1'];         // 주소

            if($status == 2){
            	// 신규로직 > 싱크 커머스에서 매장정보 수정시 변경
            	
            	$sql="select * from tblstore where store_code='".$store_code."'";
            	$result=pmysql_query($sql);
            	$rownum=pmysql_num_rows($result);
            	
            	if($rownum){
            		pmysql_query("update tblstore set address = '".$addr1."', phone = '".$phone."' where store_code='".$store_code."'");
            		
            		if( pmysql_error() ){
            			throw new Exception( "매장정보 변경이 실패했습니다. ( 매장코드 : ".$store_code." )", 1 );
            		}
            	} else {
            		throw new Exception( "매장코드와 일치한 매장정보가 없습니다. ( 매장코드 : ".$store_code." )", 1 );
            	}
            	
//             	$query = "
// 	                UPDATE
// 	                    tblstore
// 	                SET
// 	                    pickup_yn       = '".$pickup_yn."',
// 	                    delivery_yn     = '".$delivery_yn."',
// 	                    day_delivery_yn = '".$day_delivery_yn."',
// 	                    display_yn      = '".$display_yn."'
// 	                WHERE store_code = '".$store_code."'
//             	";
//             	pmysql_query( $query, get_db_conn() );
            } else {
            	// 기존로직
	            if ( empty( $store_code ) ) {
	                throw new Exception( "매장코드가 없습니다.", 1 );
	            } elseif ( empty( $pickup_yn ) ) {
	                throw new Exception( "매장픽업 사용유무가 없습니다.", 1 );
	            } elseif ( empty( $delivery_yn ) ) {
	                throw new Exception( "매장발송 사용유무가 없습니다.", 1 );
	            } elseif ( empty( $day_delivery_yn ) ) {
	                throw new Exception( "당일발송 사용유무가 없습니다.", 1 );
	            } elseif ( empty( $display_yn ) ) {
	                throw new Exception( "노출 유무가 없습니다.", 1 );
	            }
	
	            $outText .= "#UPDATE NUMBER   >> ".$k.PHP_EOL;
	            $outText .= "  store_code      = '".$store_code."'".PHP_EOL;
	            $outText .= "  pickup_yn       = '".$pickup_yn."'".PHP_EOL;
	            $outText .= "  delivery_yn     = '".$delivery_yn."'".PHP_EOL;
	            $outText .= "  day_delivery_yn = '".$day_delivery_yn."'".PHP_EOL;;
	            $outText .= "  display_yn      = '".$display_yn."'".PHP_EOL;
	            $outText .= PHP_EOL;
	
	            $sql = "
	                UPDATE
	                    tblstore
	                SET
	                    pickup_yn       = '".$pickup_yn."',
	                    delivery_yn     = '".$delivery_yn."',
	                    day_delivery_yn = '".$day_delivery_yn."',
	                    display_yn      = '".$display_yn."'
	                WHERE store_code = '".$store_code."'
	            ";
	            pmysql_query( $sql, get_db_conn() );
	            if( pmysql_error() ){
	                throw new Exception( "매장정보 변경이 실패했습니다. ( 매장코드 : ".$store_code." )", 1 );
	            } else {
	                //shell_exec ("/data/WWWROOT/shoemarker/hott/public/batch/run_get_erp_stock.sh &> /dev/null &");
	            }
            }
            
        }
    } catch ( Exception $e ) {
        $bSuccess = false;
        $message  = $e->getMessage();
        $code     = $e->getCode();
        RollbackTrans();
    }
} else {
    $bSuccess   = false;
    $message    = "매장 정보가 없습니다.";
    $code       = 1;
}

if ( $bSuccess && $code == 0 ) {
    CommitTrans();
} else {
    $bSuccess = false;
    RollbackTrans();
}

$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/sync_api_modify_store_data_'.date("Ym").'/';
if(!is_dir($textDir)){
    mkdir($textDir, 0700);
    chmod($textDir, 0777);
}
$upQrt_f = fopen( $textDir.'sync_api_modify_store_data_'.date("Ymd").'.txt', 'a' );
fwrite( $upQrt_f, $outText );
fclose( $upQrt_f );
chmod( $textDir."sync_api_modify_store_data_".date("Ymd").".txt", 0777 );

$resultTotArr = array(
    'result'  => $resultArr,
    'code'    => $code,
    'message' => $message
);

echo json_encode( $resultTotArr );

