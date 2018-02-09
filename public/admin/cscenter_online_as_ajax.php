<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/cscenter_ascode.php");

$mode=$_POST["mode"];
$receipt_no=$_POST["receipt_no"];

//경로
$filepath = $Dir.DataDir."shopimages/cscenter/";
//파일
$noticefile = new FILE($filepath);

#처리이력 수정
if($mode=="log"){
	$logno=$_POST["logno"];
	$logtime_h=$_POST["logtime_h"];
	$logtime_i=$_POST["logtime_i"];
	$logtime_s=$_POST["logtime_s"];
	$logday=$_POST["logday"];

	for($i=0;$i<count($logno);$i++){
		$regdt=str_replace("-","",$logday[$i]).$logtime_h[$i].$logtime_i[$i].$logtime_s[$i];
		pmysql_query("update tblcsaslog set regdt='".$regdt."' where no='".$logno[$i]."' and receipt_no='".$receipt_no."'");
		
	}
	
/*
	$logno_cut=explode(",", $logno);
	$logtime_h_cut=explode(",", $logtime_h);
	$logtime_i_cut=explode(",", $logtime_i);
	$logtime_s_cut=explode(",", $logtime_s);
	$logday_cut=explode(",", $logday);

	echo $logtime_h[1];
	for($i=0;$i<count($logno_cut);$i++){
		$regdt=str_replace("-","",$logday_cut[$i]).$logtime_h_cut[$i].$logtime_i_cut[$i].$logtime_s_cut[$i];
		pmysql_query("update tblcsaslog set regdt='".$regdt."' where no='".$logno_cut[$i]."' and receipt_no='".$receipt_no."'");
	}
	*/
	echo "처리이력이 저장되었습니다.";

#상담가능한연락처 수정
}else if($mode=="tel"){
	$as_name=$_POST["as_name"];
	$as_tel_1=$_POST["as_tel_1"];
	$as_tel_2=$_POST["as_tel_2"];
	$as_tel_3=$_POST["as_tel_3"];

	pmysql_query("update tblcsasreceiptinfo set as_name='".$as_name."', as_tel='".$as_tel_1."-".$as_tel_2."-".$as_tel_3."' where no='".$receipt_no."'");
	echo "상담 가능한 연락처가 수정되었습니다.";
	
}else if($mode=="memo"){
	$memo_no=$_POST["memo_no"];
	$memo_del_sql="select filename from tblcscenterfile where receipt_no='".$receipt_no."' and memo_no='".$memo_no."' and route_type='onlineas'";
	$memo_del_result=pmysql_query($memo_del_sql);
	while($memo_del_data=pmysql_fetch_array($memo_del_result)){
		$noticefile->removeFile( $memo_del_data["filename"] );	
	}

	pmysql_query("delete from tblcscenterfile  where receipt_no='".$receipt_no."' and memo_no='".$memo_no."' and route_type='onlineas'");
	pmysql_query("delete from tblcscentermemo  where receipt_no='".$receipt_no."' and no='".$memo_no."' and route_type='onlineas'");

	
}else if($mode=="img"){
	$img_no=$_POST["img_no"];
	list($img_name)=pmysql_fetch("select filename from tblcscenterfile where no='".$img_no."' and route_type='onlineas'");
	$noticefile->removeFile( $img_name );	
	pmysql_query("delete from tblcscenterfile  where no='".$img_no."' and route_type='onlineas'");
	
}

if($mode=="memo" || $mode=="img"){
	
	#메모 정보 쿼리
	$memo_sql="select * from tblcscentermemo where receipt_no='".$receipt_no."' and route_type='onlineas' order by regdt";
	$memo_result=pmysql_query($memo_sql);
	while($memo_data=pmysql_fetch_array($memo_result)){
		$memo_while[$memo_data["no"]]=$memo_data;

		$file_sql="select * from tblcscenterfile where receipt_no='".$receipt_no."' and memo_no='".$memo_data["no"]."' and route_type='onlineas' order by no";
		$file_result=pmysql_query($file_sql);
		while($file_data=pmysql_fetch_array($file_result)){
			$memo_while[$memo_data["no"]]["filename"][$file_data["no"]]=$file_data["filename"];
		}
	}
	$html="";
	if($memo_while){
		foreach($memo_while as $mw=>$mwv){
			#접수일
			$memo_date=substr($mwv['regdt'],'0','4').'-'.substr($mwv['regdt'],'4','2').'-'.substr($mwv['regdt'],'6','2').' '.substr($mwv['regdt'],'8','2').':'.substr($mwv['regdt'],'10','2').':'.substr($mwv['regdt'],'12','2');

			$html.="<h4>[".$as_progress[$mwv["step_code"]]."] <strong>".$mwv["admin_name"]."(".$mwv["admin_id"].")</strong> ".$memo_date;
			if($mwv["admin_id"]==$_ShopInfo->id){
				$html.="&nbsp;<div class='btn-wrap1'><span><a href=\"javascript:ajaxValue('memo', '".$mw."')\" class=\"btn-type1\" style=\"width:50px;\">삭제</a></span></div>";
			}
			$html.="</h4>";
			
			$html.="<div class=\"cont\">";
			if($mwv["filename"]){
				foreach($mwv["filename"] as $mwf=>$mwfv){
					$html.="<img src='".$filepath.$mwfv."'>";
					
					if($mwv["admin_id"]==$_ShopInfo->id){
						$html.="&nbsp;<div class=\"btn-wrap1\"><span><a href=\"javascript:ajaxValue('img', '".$mwf."')\" class=\"btn-type1\" style=\"width:50px;\">삭제</a></span></div>";
					}
					$html.="<br><br>";
				}
			}
			$html.=$mwv["cs_memo"];
			$html.="</div>";
			
		}
	}

	echo "html||".$html;
}
?>