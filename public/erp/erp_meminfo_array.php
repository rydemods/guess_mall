<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

/*
# 로그
# log_type : 로그 타입
# log : 로그 내역
# mem_seq : 회원 번호
# mem_id : 회원 아이디
*/
function erpDataLog($log_type, $log, $mem_id=null, $mem_seq=null) {

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
$res = json_decode(file_get_contents('php://input'));
//var_dump($res->memlist);
//exit;

$log_type	= "erp_meminfo";
erpDataLog($log_type."_request", $res);

$res_status		= "";
$res_message	= "";
$resData			= array();
if ($res->memlist) {
	foreach($res->memlist as $key => $val) {
		$mem_seq	= trim($val->MEMBERNO);
		$id			= trim($val->WEBID);
		if (ord($mem_seq) || ord($id)) {
			$name			= trim($val->MEMBERNAME);
			$email			= trim($val->EMAIL);
			$mobile			= trim($val->MOBILE);
			$news_yn		= trim($val->NEWS_YN);
			$gender			= trim($val->GENDER);
			$birth				= trim($val->BIRTH);
			$home_post	= trim($val->HOME_POST);
			$home_addr	= trim($val->HOME_ADDR)."↑=↑".trim($val->HOME_ADDR2);
			$home_tel		= trim($val->HOME_TEL);
			$group_code	= trim($val->GROUPCODE);
			$member_out	= trim($val->MEMBER_OUT);
			$staff_yn		= trim($val->STAFF_YN);
			$staffcardno	= trim($val->STAFFCARDNO);
			$memo			= trim($val->MEMO);

			list($mem_cnt) = pmysql_fetch_array(pmysql_query("SELECT count(id) as mem_cnt FROM tblmember WHERE mem_seq ='{$mem_seq}' AND id='{$id}' "));

			if($mem_cnt > 0) {
				if ($member_out == 'N') {								// 수정

					$error	= "N";

					// 멤버그룹 변경시 로그를 남긴다.
					/*$sel_qry		= "SELECT * 
										FROM tblmember m 
										LEFT JOIN tblmembergroup mg 
										ON m.group_code=mg.group_code 
										WHERE m.id='{$id}'
										";
					$sel_result	= pmysql_query($sel_qry);
					$sel_data	= pmysql_fetch_object($sel_result);

					if($sel_data->group_code!=$group_code){

						$sum_sql = "SELECT sum(price) as sumprice FROM tblorderinfo ";
						$sum_sql.= "WHERE id = '{$id}' AND deli_gbn = 'Y'";
						$sum_result = pmysql_query($sum_sql,get_db_conn());
						$sum_data=pmysql_fetch_object($sum_result);
						$sumprice="0";
						$sumprice=$sum_data->sumprice+$sel_data->sumprice;

						list($after_group)=pmysql_fetch_array(pmysql_query("select group_name from tblmembergroup where group_code='{$group_code}'"));

						$qry="insert into tblmemberchange (
						mem_id,
						before_group,
						after_group,
						accrue_price,
						change_date
						) values (
						'".$id."',
						'".$sel_data->group_name."',
						'".$after_group."',
						'".$sumprice."',
						'".date("Y-m-d")."'
						)";
						pmysql_query($qry,get_db_conn());

						if( !pmysql_error() ){
							$res_status		= "Y";
							$res_message	= "tblmembergroup에 정상적으로 정보가 저장되었습니다.";
						} else {
							$res_status		= "N";
							$res_message	= "실패 (사유 : tblmembergroup에 정상적으로 정보가 저장되지 않았습니다.)";
						}

						$log	= array(
							'res_status' => $res_status,
							'res_message' => $res_message,
							'sql' => $qry
						);
						erpDataLog($log_type."_updateDB_tblmemberchange", $log, $mem_seq, $id);

						$error	= $res_status;
					}	*/
					
					if ($error == 'N') {
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
						$sql.= "WHERE mem_seq ='{$mem_seq}' AND id='{$id}' ";

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
						erpDataLog($log_type."_updateDB_tblmember", $log, $mem_seq, $id);
					}
				} else if ($member_out == 'Y') {					// 삭제
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
		erpDataLog($log_type."_result", $log, $mem_seq, $id);

		$resData[]	= array(
			'APPR_YN' => $res_status,
			'APPR_REJECT_REASON' => $res_message
		);
	}
} else {
	$res_status		= "N";
	$res_message	= "실패 (사유 : 처리할 데이터 값이 없습니다.)";

	$resData[]	= array(
		'APPR_YN' => $res_status,
		'APPR_REJECT_REASON' => $res_message
	);
}

erpDataLog($log_type."_response", $resData);
$returnData	= json_encode_kr($resData);
echo $returnData;
?>