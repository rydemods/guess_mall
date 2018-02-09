<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-8";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$code=$_POST["code"];
$codes=$_POST["codes"];

if($mode=="movesave" && ord($codes)) {	//이동된 분류 순서 저장
	$tok1=explode("!",$codes);
	for($i=0;$i<count($tok1);$i++) {
		if(strlen(trim($tok1[$i]))>=12) {
			$code = substr($tok1[$i],0,12);
			$sequence=9999-$i;
			$sql = "UPDATE tblforumcode SET sequence='{$sequence}' ";
			$sql.= "WHERE code_a='".substr($code,0,3)."' ";
			$sql.= "AND code_b='".substr($code,3,3)."' AND code_c='".substr($code,6,3)."' ";
			$sql.= "AND code_d='".substr($code,9,3)."' ";
			pmysql_query($sql,get_db_conn());

			if(strcmp("@",substr($tok1[$i],13))) {
				$tok2=explode("@",substr($tok1[$i],13));
				for($ii=0;$ii<count($tok2);$ii++) {
					if(strlen(trim($tok2[$ii]))>=12) {
						$code=substr($tok2[$ii],0,12);
						$sequence=9999-$ii;
						$sql = "UPDATE tblforumcode SET sequence='{$sequence}' ";
						$sql.= "WHERE code_a='".substr($code,0,3)."' ";
						$sql.= "AND code_b='".substr($code,3,3)."' AND code_c='".substr($code,6,3)."' ";
						$sql.= "AND code_d='".substr($code,9,3)."' ";
						pmysql_query($sql,get_db_conn());

						if(strcmp("#",substr($tok2[$ii],13))) {
							$tok3=explode("#",substr($tok2[$ii],13));
							for($iii=0;$iii<count($tok3);$iii++) {
								if(strlen(trim($tok3[$iii]))>=12) {
									$code=substr($tok3[$iii],0,12);
									$sequence=9999-$iii;
									$sql = "UPDATE tblforumcode SET sequence='{$sequence}' ";
									$sql.= "WHERE code_a='".substr($code,0,3)."' ";
									$sql.= "AND code_b='".substr($code,3,3)."' AND code_c='".substr($code,6,3)."' ";
									$sql.= "AND code_d='".substr($code,9,3)."' ";
									pmysql_query($sql,get_db_conn());
									
									if(strcmp("$",substr($tok3[$iii],13))) {
										$tok4=explode("$",$tok3[$iii]);
										for($iiii=1;$iiii<count($tok4);$iiii++) {
											$code=$tok4[$iiii];
											$sequence=9999-$iiii;
											$sql = "UPDATE tblforumcode SET sequence='{$sequence}' ";
											$sql.= "WHERE code_a='".substr($code,0,3)."' ";
											$sql.= "AND code_b='".substr($code,3,3)."' AND code_c='".substr($code,6,3)."' ";
											$sql.= "AND code_d='".substr($code,9,3)."' ";
											pmysql_query($sql,get_db_conn());
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	$onload="<script>parent.CodeMoveResult();alert('분류순서 변경 수정이 완료되었습니다.');</script>";
} else if($mode=="delete" && strlen($code)==12) {	//분류 삭제
	$code_a=substr($code,0,3);
	$code_b=substr($code,3,3);
	$code_c=substr($code,6,3);
	$code_d=substr($code,9,3);
	$sql = "SELECT * FROM tblforumcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
	$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' ";
	$result = pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		$sql = "DELETE FROM tblforumcode WHERE code_a = '{$code_a}' ";
		$likecode=$code_a;
		if($code_b!="000") {
			$sql.= "AND code_b='{$code_b}' ";
			$likecode.=$code_b;
			if($code_c!="000") {
				$sql.= "AND code_c='{$code_c}' ";
				$likecode.=$code_c;
				if($code_d!="000") {
					$sql.= "AND code_d='{$code_d}' ";
					$likecode.=$code_d;
				}
			}
		}
		pmysql_query($sql,get_db_conn());
	}
	
	//상단 카테고리 type 수정
	$cate_len=strlen($likecode)/3;

	if($code_b!='000' && $cate_len>1){
		
		$add_qry='';
		$h_cate=substr($likecode,0,-3);

		if($cate_len=='2'){

			$add_qry=" code_a ";
		}else if($cate_len=='3'){

			$add_qry=" code_a || code_b ";
		}else if($cate_len=='4'){

			$add_qry=" code_a || code_b || code_c ";
		}


		$chk_qry="select count(*) as cnt from tblforumcode where ".$add_qry." like '".$h_cate."%'";
		$chk_res=pmysql_query($chk_qry,get_db_conn());
		$chk_row=pmysql_fetch_object($chk_res);

		if($chk_row->cnt==1){
		
			$qry="update tblforumcode set type=(type||'X') where ".$add_qry."='".$h_cate."'";

			pmysql_query($qry,get_db_conn());
		}
		
	}
	
	pmysql_free_result($result);
	//$onload="<script>parent.CodeDeleteResult('{$code}');alert('선택하신 분류를 삭제하였습니다.');</script>";
	$onload="<script>alert('선택하신 분류를 삭제하였습니다.');parent.document.location.href='forum_code.php?category=".$likecode."'</script>";
}
echo $onload;
