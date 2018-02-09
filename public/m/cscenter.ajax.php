<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");
	include_once($Dir."/lib/file.class.php");

	$imagepath = $Dir.DataDir."shopimages/personal/";

	$banner_file = new FILE($imagepath);

	$ip=$_SERVER["REMOTE_ADDR"];
	$date=date("YmdHis");

	$mode = $_POST['mode'];
	$idx = $_POST['idx'];
	if($mode=="write_exe" || $mode=="modify_exe") {
		$productcode = $_POST['productcode'];
		$head_title = $_POST['head_title'];
		$up_subject = pg_escape_string($_POST['up_subject']);
		$up_content = pg_escape_string($_POST['up_content']);
		$v_up_filename = $_POST['v_up_filename'];
		$ori_filename = $_POST["ori_filename"];
		$up_email = $_POST['up_email'];
		$hp = $_POST['hp'];
		$chk_sms = $_POST['chk_sms'] ? $_POST['chk_sms'] : 'N';
		$chk_mail = $_POST['chk_mail'] ? $_POST['chk_mail'] : 'N';
		//exdebug($_POST);
		//exdebug($_FILES);
		if ($mode=="modify_exe") {
			$sql = "SELECT * FROM tblpersonal WHERE idx='{$idx}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				$_pdata=$row;
			} else {
				echo "<html></head><body onload=\"alert('해당 문의내역이 없습니다.');\"></body></html>";exit;
			}
			pmysql_free_result($result);
		}
		//exdebug($_pdata);
		//exit;

		$banner_img=$banner_file->upFiles();

// 		if($banner_img["up_filename"][0]["v_file"] || $v_up_filename[0] == ''){
// 			if (($_pdata->up_filename !="" || $v_up_filename[0] =='') && $mode=="modify_exe") {
// 				$banner_file->removeFile($_pdata->up_filename);
// 				$up_filename	= "";
// 			}
// 			if($banner_img["up_filename"][0]["v_file"]) $up_filename=$banner_img["up_filename"][0]["v_file"];
// 		}

		if($ori_filename != $_pdata->ori_filename && $mode=="modify_exe"){
			$banner_file->removeFile($_pdata->up_filename);
			$up_filename=$banner_img["up_filename"][0]["v_file"];
			
		}else if($ori_filename == $_pdata->ori_filename && $mode=="modify_exe"){
			$up_filename=$_pdata->up_filename;
			$ori_filename=$_pdata->ori_filename;
		}else if($mode=="write_exe"){
			$up_filename=$banner_img["up_filename"][0]["v_file"];
		}

		if($mode=="write_exe") {
			$sql = "
						INSERT INTO
							tblpersonal
							(
								id,
								name,
								email,
								ip,
								subject,
								date,
								content	,
								head_title,
								\"HP\",
								chk_sms,
								chk_mail,
								productcode,
								up_filename,
								ori_filename
							)
						VALUES
							(
								'".$_ShopInfo->getMemid()."',
								'".$_ShopInfo->getmemname()."',
								'{$up_email}',
								'{$ip}',
								'{$up_subject}',
								'{$date}',
								'{$up_content}',
								{$head_title},
								'{$hp}',
								'{$chk_sms}',
								'{$chk_mail}',
								'{$productcode}',
								'{$up_filename}',
								'{$ori_filename}'
							)
			";
		} else {
			$sql  = "UPDATE tblpersonal SET ";
			$sql .= "email				= '{$up_email}', ";
			$sql .= "subject			= '{$up_subject}', ";
			$sql .= "content			= '{$up_content}', ";
			$sql .= "head_title		= '{$head_title}', ";
			$sql .= "\"HP\"			= '{$hp}', ";
			$sql .= "chk_sms		= '{$chk_sms}', ";
			$sql .= "chk_mail			= '{$chk_mail}', ";
			$sql .= "up_filename	= '{$up_filename}', ";
			$sql .= "ori_filename	= '{$ori_filename}', ";
			$sql .= "productcode	= '{$productcode}' ";
			$sql .= "WHERE idx={$idx}";
		}
		exdebug($sql);
		if(pmysql_query($sql,get_db_conn())) {
			if($mode=="write_exe") {
				//echo "<html></head><body onload=\"alert('정상적으로 등록되었습니다.');parent.location.href='/m/cscenter.php?csMenu=personal';\"></body></html>";exit;
				echo "<html></head><body onload=\"alert('정상적으로 등록되었습니다.');parent.location.href='/m/mypage_personal.php';\"></body></html>";exit;
			} else {
				echo "<html></head><body onload=\"alert('정상적으로 수정되었습니다.');parent.location.href='/m/mypage_personal.php';\"></body></html>";exit;
			}
		} else {
			echo "<html></head><body onload=\"alert('오류가 발생하였습니다.');\"></body></html>";exit;
		}
	} else if ($mode == 'del_exe'){
		$sql = "SELECT * FROM tblpersonal WHERE id='".$_ShopInfo->getMemid()."' AND idx='{$idx}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$_pdata=$row;
		} else {
			alert_go('해당 문의내역이 없습니다.','c');
		}

		list($productname)=pmysql_fetch("SELECT productname FROM tblproduct WHERE productcode = '".$row->productcode."'");

		if ($productname == '') $productname	= "-";
		pmysql_free_result($result);

		if ($_pdata->up_filename !="") $banner_file->removeFile($_pdata->up_filename);
		$sql = "DELETE FROM tblpersonal WHERE id='".$_ShopInfo->getMemid()."' AND idx='{$idx}'";

		if(pmysql_query($sql,get_db_conn())) {
			echo "<html></head><body onload=\"alert('정상적으로 삭제되었습니다.');parent.location.reload();\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('오류가 발생하였습니다.');\"></body></html>";exit;
		}
	}
?>