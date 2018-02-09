<?
/*******************************************
1. 핫티 ERP 관련 함수 모음
********************************************/
/*** 나중에 주석처리 하자 S ***/
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib_test.php");
include_once($Dir."lib/lib_erp_test.php");


$res_status		= "";
$res_message	= "";
$eshop_id	= $_REQUEST['eshop_id'];
$type	= $_REQUEST['in_type'];

if ($type == 'U') { // 정보수정
	$meberinfo2	= getErpShopMeberinfo($eshop_id);
	$p_err_code	= $meberinfo2['p_err_code'];
	$p_err_text		= $meberinfo2['p_err_text'];
	$p_data			= $meberinfo2['p_data'];
	if ($p_err_code == '0') {

		$sql = "SELECT * FROM tblmember WHERE id='".$p_data['eshop_id']."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->member_out=="Y") {
				$res_status	= "N";
				$res_message	= "실패 (사유 : 탈퇴한 회원입니다.)";
			} else {
				if($row->news_yn=="Y" || $row->news_yn=="M") {
					$news_mail_yn="Y";
				} else if($row->news_yn=="S" || $row->news_yn=="N") {
					$news_mail_yn="N";
				}

				$news_sms_yn=$p_data['sms_yn'];
						
				if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
					$news_yn="Y";
				} else if($news_mail_yn=="Y") {
					$news_yn="M";
				} else if($news_sms_yn=="Y") {
					$news_yn="S";
				} else {
					$news_yn="N";
				}
				
				$news_kakao_yn=$p_data['kakao_yn'];

				$u_sql = "UPDATE tblmember SET ";
				$u_sql.= "home_post	= '".$p_data['home_zip_no']."', ";
				$u_sql.= "home_addr	= '".$p_data['home_addr1']."↑=↑".$p_data['home_addr2']."', ";
				$u_sql.= "home_tel		= '".$p_data['home_tel_no1']."-".$p_data['home_tel_no2']."-".$p_data['home_tel_no3']."', ";
				$u_sql.= "job				= '".$erp_job_cd_arr[$p_data['job_cd']]."', ";
				$u_sql.= "job_code		= '".$p_data['job_cd']."', ";
				$u_sql.= "news_yn		= '{$news_yn}', ";
				$u_sql.= "kko_yn		= '{$news_kakao_yn}' ";
				$u_sql.= "WHERE id='".$p_data['eshop_id']."' ";

				//echo $u_sql;
				//exit;

				pmysql_query($u_sql,get_db_conn());
				//if($err=pmysql_error()) echo $err;

				if (pmysql_errno()==0) {
					$res_status	= "Y";
					$res_message	= "성공.";
				} else {
					$res_status	= "N";
					$res_message	= "실패 (사유 : 회원 수정에 에러가 발생했습니다.)";
				}
			}
		} else {
			$res_status	= "N";
			$res_message	= "실패 (사유 : 회원 아이디가 존재하지 않습니다.)";
		}

	} else {
		$res_status	= "N";
		$res_message	= "실패 (사유 : ".$p_err_text.")";
	}
} else if ($type == 'D') { // 회원탈퇴

	$sql = "SELECT * FROM tblmember WHERE id='".$eshop_id."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		if($row->member_out=="Y") {
			$res_status	= "N";
			$res_message	= "실패 (사유 : 이미 탈퇴한 회원입니다.)";
		} else {

			$exitname=$row->name;
			$exitemail=$row->email;
			$exitmobile=$row->mobile;
			
			//로그 저장 텍스트를 만든다.
			$savetemp = "====================".date("Y-m-d H:i:s")."====================\n";
			//if ($row=pmysql_fetch_object($result)) {
				foreach($row as $key=>$val){
					$savetemp.= $key." : ".$val."\n";
				}
			//}
			$savetemp.= "\n";

			//진행중인 주문건
			list($t_order_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(a.*) t_order_cnt from tblorderinfo a join tblorderproduct b on a.ordercode=b.ordercode WHERE id='".$eshop_id."' 
AND b.op_step >= 40 AND b.op_step < 44 and b.order_conf = '0'"));
			if ($t_order_cnt > 0 ) {
				$res_status	= "N";
				$res_message	= "실패 (사유 : 진행중인 주문이 있습니다.)";
			} else {
				$d_sql = "SELECT COUNT(*) as cnt FROM tblorderinfo WHERE id='".$eshop_id."' ";
				$d_result= pmysql_query($d_sql,get_db_conn());
				$d_row = pmysql_fetch_object($d_result);
				/*if($d_row->cnt==0) {
					$u_sql ="DELETE FROM tblmember WHERE id='".$eshop_id."' ";
					$state="Y";
				} else {*/
					$u_sql = "UPDATE tblmember SET ";
					$u_sql.= "passwd			= '', ";
					$u_sql.= "resno			= '', ";
					$u_sql.= "email			= '', ";
					$u_sql.= "mobile			= '', ";
					$u_sql.= "news_yn			= 'N', ";
					$u_sql.= "age				= '', ";
					$u_sql.= "gender			= '', ";
					$u_sql.= "job				= '', ";
					$u_sql.= "birth			= '', ";
					$u_sql.= "home_post		= '', ";
					$u_sql.= "home_addr		= '', ";
					$u_sql.= "home_tel		= '', ";
					$u_sql.= "office_post		= '', ";
					$u_sql.= "office_addr		= '', ";
					$u_sql.= "office_tel		= '', ";
					$u_sql.= "memo			= '', ";
					$u_sql.= "reserve			= 0, ";
					$u_sql.= "joinip			= '', ";
					$u_sql.= "ip				= '', ";
					$u_sql.= "authidkey		= '', ";
					$u_sql.= "group_code		= '', ";
					$u_sql.= "member_out		= 'Y', ";
					$u_sql.= "dupinfo		= '', ";
					$u_sql.= "sns_type		= '', ";
					$u_sql.= "act_point		= 0, ";
					$u_sql.= "etcdata			= '' ";
					$u_sql.= "WHERE id = '".$eshop_id."'";
					$state="V";
				//}
				pmysql_free_result($d_result);
				pmysql_query($u_sql,get_db_conn());


				//echo $u_sql;
				//exit;

				pmysql_query($u_sql,get_db_conn());
				//if($err=pmysql_error()) echo $err;

				if (pmysql_errno()==0) {
					$res_status	= "Y";
					$res_message	= "성공.";

					//탈퇴회원정보를 파일로 저장한다.
					$file = "../data/backup/tblmember_out_".date("Y")."_".date("m")."_".date("d").".txt";
					if(!is_file($file)){
						$f = fopen($file,"a+");
						fclose($f);
						chmod($file,0777);
					}
					file_put_contents($file,$savetemp,FILE_APPEND);

					$sql = "DELETE FROM tblcouponissue WHERE id='".$eshop_id."'";
					pmysql_query($sql,get_db_conn());
					$sql = "DELETE FROM tblmemo WHERE id='".$eshop_id."'";
					pmysql_query($sql,get_db_conn());
					$sql = "DELETE FROM tblpersonal WHERE id='".$eshop_id."'";
					pmysql_query($sql,get_db_conn());

					$sql = "INSERT INTO tblmemberout ( 
					id, name, email, tel, ip, 
					state, date, out_reason, out_reason_content) VALUES (
					'".$eshop_id."', '".$exitname."', '".$exitemail."', '".$exitmobile."', '".$_SERVER['REMOTE_ADDR']."', 
					'".$state."', '".date("YmdHis")."', '0', '') ";
					pmysql_query($sql,get_db_conn());
					
					$out_access_type = "erp";
					//---------------------------------------------------- 탈퇴시 로그를 등록한다. ----------------------------------------------------//
					$memLogSql = "INSERT INTO tblmemberlog (id,type,access_type,date) VALUES ('".$eshop_id."','out','".$out_access_type."','".date("YmdHis")."')";
					pmysql_query($memLogSql,get_db_conn());
					//---------------------------------------------------------------------------------------------------------------------------------//
					//SMS 발송
					sms_autosend( 'mem_out', $eshop_id, '', '' );
					//SMS 관리자 발송
					sms_autosend( 'admin_out', $eshop_id, '', '' );
				} else {
					$res_status	= "N";
					$res_message	= "실패 (사유 : 회원 탈퇴에 에러가 발생했습니다.)";
				}
			}
		}
	} else {
		$res_status	= "N";
		$res_message	= "실패 (사유 : 회원 아이디가 존재하지 않습니다.)";
	}
}

$resData	= array(
	'APPR_YN' => $res_status,
	'APPR_REJECT_REASON' => $res_message
);

$returnData	= json_encode_kr($resData);
echo $returnData;
?>