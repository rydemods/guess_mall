<?php
/********************************************************************* 
// 파 일 명		: affiliates_indb.php 
// 설     명		: 제휴 학교/회사 등록, 수정, 삭제 실행
// 상세설명	: 관리자 제휴 학교/회사 등록, 수정, 삭제를 실행.
// 작 성 자		: 2015.10.26 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/file.class.php");
	include("access.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$mode=$_POST[mode];
	$no=$_POST[no];

	$date=date("YmdHis");

#---------------------------------------------------------------
# DB에 저장한다.
#---------------------------------------------------------------
	if($mode=='affiliates_ins'){				// 신규등록시
		$rf_type						= $_POST[rf_type];	
		$rf_area						= $_POST[rf_area];	
		$rf_name					= $_POST[rf_name];	
		$rf_referrer_url				= $_POST[rf_referrer_url];	
		$rf_referrer_email_url	= $_POST[rf_referrer_email_url];	
		$rf_url						= $_POST[rf_url];	
		$rf_use						= $_POST[rf_use];	
		$rf_output					= $_POST[rf_output];	
		$rf_coupon					= $_POST[rf_coupon];	
		$logofile						= $_FILES["logofile"];
		$vlogoImage				= $_POST["vlogoImage"];

		$rf_row=pmysql_fetch_object(pmysql_query("select name from tblaffiliatesinfo where name='{$rf_name}' "));
		if ($rf_row) {
			alert_go("학교/회사명이 중복되었습니다.",-1);
		}
		if(ord($logofile["name"])){
			$logofilePATH = $Dir.DataDir."shopimages/affiliates_logo/";
			
			if ( is_file($logofilePATH.$vlogoImage) ) {
				unlink($logofilePATH.$vlogoImage);
			}
			
			if (ord($logofile['name']) && file_exists($logofile['tmp_name'])) {
				$ext = strtolower(pathinfo($logofile['name'],PATHINFO_EXTENSION));
				//exdebug($ext);
				if(in_array($ext,array('gif','jpg','png'))) {
					$up_logoFile = date("YmdHis")."_logo".".".$ext;
					move_uploaded_file($logofile['tmp_name'], $logofilePATH.$up_logoFile);
					chmod($logofilePATH.$up_logoFile,0664);
				} else {
					$up_logoFile	="";
				}
			} 
							
		}

		$qry="insert into tblaffiliatesinfo (type,area,name,referrer_url, referrer_email_url,url,logoimage,use,output,coupon,regdate)values('{$rf_type}', '{$rf_area}', '{$rf_name}', '{$rf_referrer_url}', '{$rf_referrer_email_url}', '{$rf_url}', '{$up_logoFile}', '{$rf_use}', '{$rf_output}', '{$rf_coupon}', '{$date}')";
		
		pmysql_query($qry);
		$msg="등록 되었습니다.";
		msg($msg,"affiliates_board.php");	
		
	}else if($mode=='affiliates_mod'){			// 수정시
		$rf_type				= $_POST[rf_type];	
		$rf_area				= $_POST[rf_area];	
		$rf_name			= $_POST[rf_name];	
		$rf_referrer_url		= $_POST[rf_referrer_url];	
		$rf_referrer_email_url	= $_POST[rf_referrer_email_url];	
		$rf_url				= $_POST[rf_url];	
		$rf_use				= $_POST[rf_use];	
		$rf_output			= $_POST[rf_output];	
		$rf_coupon			= $_POST[rf_coupon];	
		$logofile				= $_FILES["logofile"];
		$vlogoImage		= $_POST["vlogoImage"];

		$rf_row=pmysql_fetch_object(pmysql_query("select name from tblaffiliatesinfo where name='{$rf_name}' AND idx!='{$no}' "));
		if ($rf_row) {
			alert_go("학교/회사명이 중복되었습니다.",-1);
		}

		if(ord($logofile["name"])){
			$logofilePATH = $Dir.DataDir."shopimages/affiliates_logo/";
			
			if ( is_file($logofilePATH.$vlogoImage) ) {
				unlink($logofilePATH.$vlogoImage);
			}
			
			if (ord($logofile['name']) && file_exists($logofile['tmp_name'])) {
				$ext = strtolower(pathinfo($logofile['name'],PATHINFO_EXTENSION));
				//exdebug($ext);
				if(in_array($ext,array('gif','jpg','png'))) {
					$up_logoFile = date("YmdHis")."_logo".".".$ext;
					move_uploaded_file($logofile['tmp_name'], $logofilePATH.$up_logoFile);
					chmod($logofilePATH.$up_logoFile,0664);
				} else {
					$up_logoFile	="";
				}
			} 
							
		}
		
		$qry	 = "update tblaffiliatesinfo set type='{$rf_type}', area='{$rf_area}', name='{$rf_name}', referrer_url='{$rf_referrer_url}', referrer_email_url='{$rf_referrer_email_url}', url='{$rf_url}', use='{$rf_use}', output='{$rf_output}', coupon='{$rf_coupon}' ";
		if ($up_logoFile) $qry	.= ", logoimage='{$up_logoFile}' ";
		$qry	.= "where idx='{$no}'";
		
		pmysql_query($qry);
		$msg="수정 되었습니다.";
		msg($msg,"affiliates_board.php");	
	}else if($mode=='affiliates_del'){		// 삭제시
		
		$del_row=pmysql_fetch_object(pmysql_query("select logoimage from tblaffiliatesinfo where idx='{$no}'"));
		$logoimage=$del_row->logoimage;

		$logofilePATH = $Dir.DataDir."shopimages/affiliates_logo/";

		if ($logoimage) {
			if ( is_file($logofilePATH.$logoimage) ) {
				unlink($logofilePATH.$logoimage);
			}
		}

		$qry="delete from tblaffiliatesinfo where idx='{$no}'";
		pmysql_query($qry);
		
		$msg="삭제 되었습니다.";
		msg($msg,"affiliates_board.php");	
		
	}else if($mode=='affiliates_img_del'){		// 이미지 삭제시

		$delprdtimg	= $_POST["delprdtimg"];
		$vimage			= $_POST["vimage"];

		$delarray = array (&$vimage);
		$delname = array ("logoImage");

		$logofilePATH = $Dir.DataDir."shopimages/affiliates_logo/";

		if(ord($delarray[$delprdtimg]) && file_exists($logofilePATH.$delarray[$delprdtimg])) {
			unlink($logofilePATH.$delarray[$delprdtimg]);
		}
		
		$qry	 = "update tblaffiliatesinfo set ".$delname[$delprdtimg]."=NULL ";
		$qry	.= "where idx='{$no}'";

		pmysql_query($qry);
		
		$msg="이미지가 삭제 되었습니다.";
		msg($msg,"affiliates_register.php?mode=affiliates_mod&no={$no}");	
		
	}else if($mode=="affiliates_use_output"){		// 사용,출력 변경시
		$num			= $_POST['num'];
		$use			= $_POST['use'];
		$output		= $_POST['output'];
		
		foreach($num as $k){		
			$up_qry="update tblaffiliatesinfo set use='0', output='0' where idx='{$k}'";
			//echo $up_qry."<br>";
			pmysql_query($up_qry);
		}
		
		foreach($use as $v){
			$up_qry="update tblaffiliatesinfo set use='1' where idx='{$v}'";
			//echo $up_qry."<br>";
			pmysql_query($up_qry);
		}
		
		foreach($output as $y){
			$up_qry="update tblaffiliatesinfo set output='1' where idx='{$y}'";
			//echo $up_qry."<br>";
			pmysql_query($up_qry);
		}
		//exit;
		$msg="변경되었습니다.";
		msg($msg,"affiliates_board.php");	
	}
?>
