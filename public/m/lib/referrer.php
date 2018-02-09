<?
//if($_SERVER["REMOTE_ADDR"] == '218.234.32.102'){

/********************************************************************* 
// 파 일 명		: referrer.php 
// 설     명		: 리퍼러 도메인 체크
// 상세설명	: 승인된 가입경로에서만 가입할수있도록 리퍼러 도메인 체크.
// 작 성 자		: 2015.10.27 - 김재수
// 수 정 자		: 코드 추가 (2015.12.03 - 김재수 추가)
// 
// 
*********************************************************************/ 

#---------------------------------------------------------------
# 도메인명에서 메인 사이트명만 가져오기
#---------------------------------------------------------------
	function getBaseDomain($dom) { 
		$matches = array(); 
		preg_match('/[^\.]+\.([^\.]{4}|[^\.]{3}|(co|or|pe|ac)\.[^\.]{2}|[^\.]{2})$/i', $dom, &$matches); 
		return $matches[0]; 
	} 

#---------------------------------------------------------------
# 기본정보 설정을 한다.
#---------------------------------------------------------------
	$referrer_tmp			= parse_url($_SERVER['HTTP_REFERER']);
	$referrer_url			= preg_replace("`^www\.`i", "", $referrer_tmp['host']);
	$referrer_bace_url	= getBaseDomain($referrer_url);
	$referrer_path			= explode("/",$referrer_tmp['path']);
	preg_match("/\.(ac\.kr)$/i", $referrer_tmp['host'],$refere_email);

#---------------------------------------------------------------
# 승인된 가입경로에서만 가입할수있도록 리퍼러 도메인 체크한다.
#---------------------------------------------------------------	
	if ($referrer_path[1] != 'admin' && $referrer_path[1] != 'vender') {	// 관리자 페이지가 아닐경우
		
		//코드 추가 (2015.12.03 - 김재수 추가)
		$rfcode	= $_GET['rfcode'];
		if ($rfcode) {	// 유입코드값이 있으면
			if($referrer_url != preg_replace("`^www\.`i", "", $_SERVER['HTTP_HOST']) && $referrer_url !="") { // 다른곳에서 유입 되었다면
				$rf_url_address_type	= substr($rfcode,0,1);								// 유입 타입(E : 이메일 / B : 배너)
				$rf_url_address_no		= str_replace($rf_url_address_type,"", $rfcode);	// 유입 학교/회사 코드 idx값

				$r_sql		= "SELECT idx, type, name, logoimage FROM tblaffiliatesinfo WHERE idx='{$rf_url_address_no}' and use='1' and referrer_email_url = '{$referrer_bace_url}' order by idx desc limit 1";
				//echo $r_sql;
				$r_result		= pmysql_query($r_sql,get_db_conn());
				if($r_row = pmysql_fetch_object($r_result)) {
					$af_idx				= $r_row->idx;
					$af_type				= $r_row->type;
					$af_name			= $r_row->name;
					$af_logoimage	= $r_row->logoimage;
					set_session('rf_url_address', $_SERVER['HTTP_REFERER']);
					set_session('rf_url_address_type', $rf_url_address_type);
					set_session('rf_url_id', $af_idx);
					set_session('rf_url_type', $af_type);
					set_session('rf_url_name', $af_name);
					set_session('rf_url_img', $af_logoimage);
					//echo "check : Y1<br>";
				} else {
					set_session('rf_url_address', '');
					set_session('rf_url_address_type', '');
					set_session('rf_url_id', '');
					set_session('rf_url_type', '');
					set_session('rf_url_name', '');
					set_session('rf_url_img', '');
					//echo "check : N1<br>";
				}
			
			}

		} else { // 유입코드값이 없으면
			
			if($referrer_url != preg_replace("`^www\.`i", "", $_SERVER['HTTP_HOST']) && $referrer_url !="") { // 이전 유입경로가 있을경우
				$r_sql		= "SELECT idx, type, name, logoimage FROM tblaffiliatesinfo WHERE use='1' and referrer_url = '{$referrer_url}' order by idx desc limit 1";
				$r_result		= pmysql_query($r_sql,get_db_conn());
				if($r_row = pmysql_fetch_object($r_result)) {
					$af_idx				= $r_row->idx;
					$af_type				= $r_row->type;
					$af_name			= $r_row->name;
					$af_logoimage	= $r_row->logoimage;
					set_session('rf_url_address', $_SERVER['HTTP_REFERER']);
					set_session('rf_url_address_type', 'B');
					set_session('rf_url_id', $af_idx);
					set_session('rf_url_type', $af_type);
					set_session('rf_url_name', $af_name);
					set_session('rf_url_img', $af_logoimage);
					//echo "check : Y2<br>";
				} else {
					$r_sql2		= "SELECT idx, type, name, logoimage FROM tblaffiliatesinfo WHERE use='1' and (referrer_email_url = '{$referrer_url}' or referrer_email_url = '{$referrer_bace_url}') order by idx desc limit 1";
					$r_result2	= pmysql_query($r_sql2,get_db_conn());
					if($r_row2 = pmysql_fetch_object($r_result2)) {
						$af_idx				= $r_row2->idx;
						$af_type				= $r_row2->type;
						$af_name			= $r_row2->name;
						$af_logoimage	= $r_row2->logoimage;
						set_session('rf_url_address', $_SERVER['HTTP_REFERER']);
						set_session('rf_url_address_type', 'E');
						set_session('rf_url_id', $af_idx);
						set_session('rf_url_type', $af_type);
						set_session('rf_url_name', $af_name);
						set_session('rf_url_img', $af_logoimage);
						//echo "check : Y3<br>";
					} else {
						set_session('rf_url_address', '');
						set_session('rf_url_address_type', '');
						set_session('rf_url_id', '');
						set_session('rf_url_type', '');
						set_session('rf_url_name', '');
						set_session('rf_url_img', '');
						//echo "check : N3<br>";
					}
				}			
			}
		}

		(get_session('rf_url_address')) ? $rf_url_address	= get_session('rf_url_address') : $rf_url_address	= '';
		(get_session('rf_url_address_type')) ? $rf_url_address_type	= get_session('rf_url_address_type') : $rf_url_address_type	= '';
		(get_session('rf_url_id')) ? $rf_url_id	= get_session('rf_url_id') : $rf_url_id	= '';
		(get_session('rf_url_type')) ? $rf_url_type	= get_session('rf_url_type') : $rf_url_type	= '';
		(get_session('rf_url_name')) ? $rf_url_name	= get_session('rf_url_name') : $rf_url_name	= '';
		(get_session('rf_url_img')) ? $rf_url_img	= get_session('rf_url_img') : $rf_url_img	= '';
		/*echo "rf_url_address : ".$rf_url_address."<br>";
		echo "rf_url_address_type : ".$rf_url_address_type."<br>";
		echo "rf_url_id : ".$rf_url_id."<br>";
		echo "rf_url_type : ".$rf_url_type."<br>";
		echo "referrer_url : ".$referrer_url."<br>";
		echo "af_name : ".$af_name."<br>";
		$_rfInfo	= array(
			"rf_url_address" =>$rf_url_address,
			"rf_url_address_type" =>$rf_url_address_type,
			"rf_url_id" =>$rf_url_id,
			"rf_url_type" =>$rf_url_type,
			"referrer_url" =>$referrer_url,
			"rf_url_name" =>$rf_url_name,
			"rf_url_img" =>$rf_url_img);
		exdebug($_rfInfo);*/
	}

/*} else {	
	@include_once($Dir."lib/referrer.old.20151204.php");
}*/
?>