<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

/*
# 로그
# log_type : 로그 타입
# log : 로그 내역
# mem_seq : 회원 번호
# mem_id : 회원 아이디
*/
function erpMeminfoLog($log_type, $log, $mem_id=null, $mem_seq=null) {

	// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/erp_meminfo_logs_'.date("Ym").'/';
	$outText = "";
	if ($log_type == "erp_meminfo_request")  $outText .= "\n=========================".date("Y-m-d H:i:s")."=============================\n";
	$outText .= "[".$log_type."] (".date("Y-m-d H:i:s").") - ";
	if ($mem_seq) $outText.= "mem_seq : ".($mem_seq)." / ";
	if ($mem_id) $outText.= "mem_id : ".($mem_id)." / ";
	$outText.= "log : ".json_encode_kr($log)."\n";

	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'erp_meminfo_logs_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."erp_meminfo_logs_".date("Ymd").".txt",0777);
	// 로그를 남긴다.-E--------------------------------------------------------------------------------------//

}

//넘어올 데이터 복호화 (object)
$res_val = json_decode(file_get_contents('php://input'));

$log_type	= "erp_meminfo";
erpMeminfoLog($log_type."_request", $res_val);

$res_status		= "";
$res_message	= "";
$resData			= "";
if ($res_val) {
	$mem_seq	= trim($res_val->MEMBERNO);
	$id			= trim($res_val->WEBID);
	if (ord($mem_seq)) {
		$name			= trim($res_val->MEMBERNAME);
		$email			= trim($res_val->EMAIL);
		$mobile			= trim($res_val->MOBILE);
		$news_yn		= trim($res_val->NEWS_YN);
		$gender			= trim($res_val->GENDER);
		$birth				= trim($res_val->BIRTH);
		$home_post	= trim($res_val->HOME_POST);
		$home_addr	= trim($res_val->HOME_ADDR)."↑=↑".trim($res_val->HOME_ADDR2);
		$home_tel		= trim($res_val->HOME_TEL);
		$group_code	= trim($res_val->GROUPCODE);
		$member_out	= trim($res_val->MEMBER_OUT);
		$staff_yn		= trim($res_val->STAFF_YN);
		$staffcardno	= trim($res_val->STAFFCARDNO);
		$memo			= trim($res_val->MEMO);

		list($mem_cnt) = pmysql_fetch_array(pmysql_query("SELECT count(id) as mem_cnt FROM tblmember WHERE mem_seq ='{$mem_seq}' "));

		if($mem_cnt > 0) {
			if ($member_out == 'N') {								// 수정
				$sql = "UPDATE tblmember SET ";
				$sql.= "email		= '{$email}', ";
				$sql.= "mobile		= '{$mobile}', ";
				$sql.= "news_yn		= '{$news_yn}', ";
				$sql.= "gender		= '{$gender}', ";
				$sql.= "birth		= '{$birth}', ";
				if(ord($home_post)) $sql.= "home_zonecode	= '{$home_post}', ";
				$sql.= "home_post	= '{$home_post}', ";
				$sql.= "home_addr	= '{$home_addr}', ";
				$sql.= "home_tel	= '{$home_tel}', ";
				//$sql.= "group_code	= '{$group_code}', ";
				$sql.= "member_out	= '{$member_out}', ";
				$sql.= "staff_yn	= '{$staff_yn}', ";
				$sql.= "staffcardno		= '{$staffcardno}', ";
				$sql.= "memo	= '{$memo}' ";
				$sql.= "WHERE mem_seq ='{$mem_seq}' ";

				pmysql_query($sql,get_db_conn());

				if( !pmysql_error() ){
					$res_status		= "Y";
					$res_message	= "tblmember에 정상적으로 정보가 저장되었습니다.";
				} else {
					$res_status		= "N";
					$res_message	= "실패 (사유 : tblmember에 정상적으로 정보가 저장되지 않았습니다.)";
				}

				$log	= array(
					'res_status' => $res_status,
					'res_message' => $res_message,
					'sql' => $sql
				);
				erpMeminfoLog($log_type."_updateDB_tblmember", $log, $mem_seq, $id);
			} else if ($member_out == 'Y') {					// 삭제

				$out_reason				= "7";
				$out_reason_content	= "ERP에서 탈퇴";
				$error						= "N";

				$sql = "SELECT id, name, email, mobile FROM tblmember WHERE mem_seq ='{$mem_seq}' ";
				$result = pmysql_query($sql,get_db_conn());
				if ($row=pmysql_fetch_object($result)) {
					if($row->member_out=="Y") {
						$res_status		= "N";
						$res_message	= "실패 (사유 : 이미 탈퇴한 회원입니다.)";
					}
					if ($res_status !='N') {
						$id=$row->id;
						$exitname=$row->name;
						$exitemail=$row->email;
						$exitmobile=$row->mobile;
						
						//로그 저장 텍스트를 만든다.
						$savetemp = "====================".date("Y-m-d H:i:s")."====================\n";		
						foreach($row as $key=>$val){
							$savetemp.= $key." : ".$val."\n";
						}
						$savetemp.= "\n";

						//진행중인 주문건
						list($t_order_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) t_order_cnt from tblorderinfo WHERE id='".$id."' AND oi_step2 < 44"));
						if ($t_order_cnt > 0) {
							$res_status		= "N";
							$res_message	= "실패 (사유 : 진행중인 주문 ".number_format($t_order_cnt)."건이 있습니다.)";						
						}
					}

				} else {
					$res_status		= "N";
					$res_message	= "실패 (사유 : 회원이 존재하지 않습니다.)";
				}
				pmysql_free_result($result);
			
				if ($res_status !='N') {
					$sql = "SELECT COUNT(*) as cnt FROM tblorderinfo WHERE id='".$id."' ";
					$result= pmysql_query($sql,get_db_conn());
					$row = pmysql_fetch_object($result);
					if($row->cnt==0) {
						$sql ="DELETE FROM tblmember WHERE id='".$id."' ";
						$state="Y";
					} else {
						$sql = "UPDATE tblmember SET ";
						$sql.= "passwd			= '', ";
						$sql.= "resno			= '', ";
						$sql.= "email			= '', ";
						$sql.= "mobile			= '', ";
						$sql.= "news_yn			= 'N', ";
						$sql.= "age				= '', ";
						$sql.= "gender			= '', ";
						$sql.= "job				= '', ";
						$sql.= "birth			= '', ";
						$sql.= "home_post		= '', ";
						$sql.= "home_addr		= '', ";
						$sql.= "home_tel		= '', ";
						$sql.= "office_post		= '', ";
						$sql.= "office_addr		= '', ";
						$sql.= "office_tel		= '', ";
						$sql.= "memo			= '', ";
						$sql.= "reserve			= 0, ";
						$sql.= "joinip			= '', ";
						$sql.= "ip				= '', ";
						$sql.= "authidkey		= '', ";
						$sql.= "group_code		= '', ";
						$sql.= "member_out		= 'Y', ";
						$sql.= "dupinfo		= '', ";
						$sql.= "sns_type		= '', ";
						$sql.= "act_point		= 0, ";
						$sql.= "etcdata			= '', ";
						$sql.= "WHERE id = '".$id."'";
						$state="V";
					}
					pmysql_free_result($result);
					pmysql_query($sql,get_db_conn());

					if( !pmysql_error() ){

						//탈퇴회원정보를 파일로 저장한다.
						$file = "../data/backup/tblmember_out_".date("Y")."_".date("m")."_".date("d").".txt";

						if(!is_file($file)){
							$f = fopen($file,"a+");
							fclose($f);
							chmod($file,0777);
						}
						file_put_contents($file,$savetemp,FILE_APPEND);

						$res_status		= "Y";
						$res_message	= "tblmember에 정상적으로 정보가 삭제되었습니다.";
					} else {
						$res_status		= "N";
						$res_message	= "실패 (사유 : tblmember에 정상적으로 정보가 삭제되지 않았습니다.)";
					}

					$log	= array(
						'res_status' => $res_status,
						'res_message' => $res_message,
						'sql' => $sql
					);
					erpMeminfoLog($log_type."_deleteDB_tblmember", $log, $mem_seq, $id);

					if ($res_status == 'Y') {

						$sql = "DELETE FROM tblcouponissue WHERE id='".$id."'";
						pmysql_query($sql,get_db_conn());

						$sql = "DELETE FROM tblmemo WHERE id='".$id."'";
						pmysql_query($sql,get_db_conn());

						$sql = "DELETE FROM tblpersonal WHERE id='".$id."'";
						pmysql_query($sql,get_db_conn());

						$sql = "INSERT INTO tblmemberout ( 
						id, name, email, tel, ip, 
						state, date, out_reason, out_reason_content) VALUES (
						'".$id."', '".$exitname."', '".$exitemail."', '".$exitmobile."', '".$_SERVER['REMOTE_ADDR']."', 
						'".$state."', '".date("YmdHis")."', '".$out_reason."', '".$out_reason_content."') ";
						pmysql_query($sql,get_db_conn());
						
						//---------------------------------------------------- 탈퇴시 로그를 등록한다. ----------------------------------------------------//
						$memLogSql = "INSERT INTO tblmemberlog (id,type,access_type,date) VALUES ('".$id."','out','erp','".date("YmdHis")."')";
						pmysql_query($memLogSql,get_db_conn());
						//---------------------------------------------------------------------------------------------------------------------------------//

						//SMS 발송
						sms_autosend( 'mem_out', $id, '', '' );
						//SMS 관리자 발송
						sms_autosend( 'admin_out', $id, '', '' );

						//탈퇴메일 발송 처리
						if(ord($exitemail)) {
							//SendOutMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->out_msg, $_data->info_email, $exitemail, $exitname);
						}
					}
				}
			}
		} else {
			$res_status		= "N";
			$res_message	= "실패 (사유 : 회원이 존재하지 않습니다.)";
		}
	} else {
		$res_status		= "N";
		$res_message	= "실패 (사유 : 처리할 데이터 값이 없습니다.)";
	}
	
	if ($res_status == 'Y') $res_message	= "성공";
	$log	= array(
		'res_status' => $res_status,
		'res_message' => $res_message
	);
	erpMeminfoLog($log_type."_result", $log, $mem_seq, $id);

	$resData	= array(
		'APPR_YN' => $res_status,
		'APPR_REJECT_REASON' => $res_message
	);
} else {
	$res_status		= "N";
	$res_message	= "실패 (사유 : 처리할 데이터 값이 없습니다.)";

	$resData	= array(
		'APPR_YN' => $res_status,
		'APPR_REJECT_REASON' => $res_message
	);
}

erpMeminfoLog($log_type."_response", $resData);
$returnData	= json_encode_kr($resData);
echo $returnData;
?>