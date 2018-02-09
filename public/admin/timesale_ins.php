<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.php");
####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_REQUEST['mode'];
$sno=$_REQUEST['sno'];
$imagepath = $cfg_img_path['timesale'];
$filedata= new FILE($imagepath);

$_POST['price']=str_replace(",","",$_POST['price']);
$_POST['s_price']=str_replace(",","",$_POST['s_price']);

$errmsg = $filedata->chkExt();

if($errmsg==''){

	$up_file = $filedata->upFiles();

	if($mode=='ins'){

		//재고체크
		$cqry="select quantity from tblproduct where productcode='".$_POST['pdt_code']."'";
		$cres=pmysql_query($cqry);
		$crow=pmysql_fetch_array($cres);

		//동일상품 등록체크
		$chkqry="select count(*) as cnt from tbl_timesale_list where productcode='".$_POST['pdt_code']."' and ((sdate between '".$_POST['sdate']."' and '".$_POST['edate']."') or (edate between '".$_POST['sdate']."' and '".$_POST['edate']."')) and sno!='".$sno."'";
		$chkres=pmysql_query($chkqry);
		$chkrow=pmysql_fetch_array($chkres);

		if($crow['quantity']!='' and $crow['quantity']<$_POST['ea']){
			msg('재고 수량이 부족합니다.','timesale_list.php');
		}else if($chkrow['cnt']>0){
			msg('이미 등록된 상품입니다.','timesale_list.php');
		}else{
			
			$qry="insert into tbl_timesale_list(title, productcode, price, s_price, ea, add_ea, rolling_v_img, rolling_r_img, view_v_img, view_r_img, view_type, rolling_type, sdate, edate, regdt)
			values('".$_POST['title']."','".$_POST['pdt_code']."','".$_POST['price']."','".$_POST['s_price']."','".$_POST['ea']."','".$_POST['add_ea']."','".$up_file['rolling_file'][0]['v_file']."','".$up_file['rolling_file'][0]['r_file']."','".$up_file['view_file'][0]['v_file']."','".$up_file['view_file'][0]['r_file']."','".$_POST['view_type']."','".$_POST['rolling_type']."','".$_POST['sdate']."','".$_POST['edate']."',now())";
			
			
			if(pmysql_query($qry)){
				msg('등록되었습니다.','timesale_list.php');
			}else{	
				msg('등록실패','timesale_list.php');
			}
		}
	}else if($mode=='mod'){
		
		$sno=$_REQUEST['sno'];

		$cgqry="select count(*) as cnt from tbl_timesale_list where productcode='".$_POST['pdt_code']."' and ((sdate between '".$_POST['sdate']."' and '".$_POST['edate']."') or (edate between '".$_POST['sdate']."' and '".$_POST['edate']."')) and sno!='".$sno."'";
		$cgres=pmysql_query($cgqry);
		$cgrow=pmysql_fetch_array($cgres);

		//재고체크
		$cqry="select quantity from tblproduct where productcode='".$_POST['pdt_code']."'";
		$cres=pmysql_query($cqry);
		$crow=pmysql_fetch_array($cres);

		if($crow['quantity']!='' and $crow['quantity']<$_POST['ea']){
			msg('재고 수량이 부족합니다.',-1);
		}else if($cgrow['cnt']>0){
			msg('동일한 기간에 진행되는 동일한 상품이 있습니다.',-1);
		}else{
			$chkqry="select rolling_v_img, view_v_img from tbl_timesale_list where sno='".$sno."'";
			$chkres=pmysql_query($chkqry);
			$chkrow=pmysql_fetch_array($chkres);
			

			if($up_file['rolling_file'][0]['error']!='1'){
				 $where.="rolling_v_img='".$up_file['rolling_file'][0]['v_file']."', rolling_r_img='".$up_file['rolling_file'][0]['r_file']."', ";
				 $filedata->removeFile($chkrow['rolling_v_img']);
			}
			if($up_file['view_file'][0]['error']!='1'){
				 $where.="view_v_img='".$up_file['view_file'][0]['v_file']."', view_r_img='".$up_file['view_file'][0]['r_file']."', ";
				 $filedata->removeFile($chkrow['view_v_img']);
			}

			$qry="update tbl_timesale_list set
			title='".$_POST['title']."',
			productcode='".$_POST['pdt_code']."',
			price='".$_POST['price']."',
			s_price='".$_POST['s_price']."',
			ea='".$_POST['ea']."',
			add_ea='".$_POST['add_ea']."',
			view_type='".$_POST['view_type']."',
			rolling_type='".$_POST['rolling_type']."',
			".$where."
			sdate='".$_POST['sdate']."',
			edate='".$_POST['edate']."'
			where sno='".$sno."'";		

			if(pmysql_query($qry)){
				msg('수정되었습니다.',"timesale_reg.php?sno=$sno&mode=$mode");
			}else{	
				msg('수정실패',"timesale_reg.php?sno=$sno&mode=$mode");
			}
		}
	}else if($mode=='del'){
		
		$chkqry="select rolling_v_img, view_v_img from tbl_timesale_list where sno='".$sno."'";
		$chkres=pmysql_query($chkqry);
		$chkrow=pmysql_fetch_array($chkres);

		$filedata->removeFile($chkrow['rolling_v_img']);
		$filedata->removeFile($chkrow['view_v_img']);
		
		$qry="delete from tbl_timesale_list where sno='".$sno."'";
		if(pmysql_query($qry)){
			msg('삭제되었습니다.','timesale_list.php');
		}else{	
			msg('삭제되었습니다','timesale_list.php');
		}
	}

}else{
	msg($errmsg,-1);
}

?>