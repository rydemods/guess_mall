<?php
include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$stores       = $_POST['stores']; // 매장정보
$status       = $_POST['status'];
$code         = 0;
$message      = "success";
$bSuccess     = true;
$resultTotArr = array();

BeginTrans();

$outText = '========================='.date("Y-m-d H:i:s")."=============================".PHP_EOL;

if( $stores ){
    try {
        foreach( $stores as $k => $v ){
            $store_code      = $v['store_code'];        //매장코드
            $part_div      = $v['part_div'];         	//erp 유통망
            $part_no      = $v['part_no'];         		//erp 매장번호
            $brand_cd      = $v['brand_cd'];         	//erp 브랜드
            $owner_ph      = $v['owner_ph'];         	// 업주 전화번호
            $status      = $v['status'];         	// 상태

            if($status == 1){

            	if ( empty( $store_code ) ) {
            		throw new Exception( "매장코드가 없습니다.", 1 );
            	} elseif ( empty( $part_no ) ) {
            		throw new Exception( "매장픽업 코드가 없습니다.", 1 );
            	} elseif ( empty( $brand_cd ) ) {
            		throw new Exception( "매장발송 브랜드가 없습니다.", 1 );
            	} elseif ( empty( $owner_ph ) ) {
            		throw new Exception( "매장발송 업주 전화번호가  없습니다.", 1 );
            	} 
            	
            	$sql = "
	                UPDATE
	                    TBLSTORE
	                SET
	                    OWNER_PH	= '".$owner_ph."'
	                WHERE STORE_CODE = '".$store_code."'
	                AND PART_NO = '".$part_no."'
	               	AND BRANDCD = '".$brand_cd."'
	            ";
            	pmysql_query( $sql, get_db_conn() );

            	# ===========[로그데이터]============
            	$outText .= "#UPDATE NUMBER   >> ".$k.PHP_EOL;
            	$outText .= "  store_code	= '".$store_code."'".PHP_EOL;
            	$outText .= "  part_div	= '".$part_div."'".PHP_EOL;
            	$outText .= "  part_no	= '".$part_no."'".PHP_EOL;
            	$outText .= "  brand_cd	= '".$brand_cd."'".PHP_EOL;
            	$outText .= "  owner_ph = '".$owner_ph."'".PHP_EOL;
            	$outText .= "  status	= '".$status."'".PHP_EOL;
            	$outText .= "  SQL	= '".$sql."'".PHP_EOL;
            	$outText .= "  error	= '".pmysql_error()."'".PHP_EOL;
            	$outText .= PHP_EOL;
            	# ===============================
            	
            	if( pmysql_error() ){
            		$message = "[ERROR]"."매장정보 변경이 실패했습니다. ( 매장코드 : ".$store_code." )";
            		throw new Exception( "매장정보 변경이 실패했습니다. ( 매장코드 : ".$store_code." : ".pmysql_error()." )", 1 );
            	} else {
            		$message = "[SUCCESS]";
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

# ===================[로그저장]========================
$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/sync_api_alimtalk_store_phone_'.date("Ym").'/';
if(!is_dir($textDir)){
    mkdir($textDir, 0700);
    chmod($textDir, 0777);
}
$upQrt_f = fopen( $textDir.'sync_api_alimtalk_store_phone_'.date("Ymd").'.txt', 'a' );
fwrite( $upQrt_f, $outText );
fclose( $upQrt_f );
chmod( $textDir."sync_api_alimtalk_store_phone_".date("Ymd").".txt", 0777 );
# ==================================================

$resultTotArr = array(
    'code'    => $code,
    'message' => $message
);

echo json_encode( $resultTotArr );

