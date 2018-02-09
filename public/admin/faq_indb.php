<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/file.class.php");
include("access.php");

$mode=$_POST[mode];
$no=$_POST[no];
$faq_type=$_POST[faq_type];
$faq_title=pg_escape_string($_POST[faq_title]);
$faq_content=pg_escape_string($_POST[faq_content]);

$date=date("YmdHis");

if($_GET[mode]=="faq_restore"){
	$sqlfile = file_get_contents("http://soapschool.co.kr/shop/admin/member/faq_dump.php?mode=faq");
	$sqlarray = explode("insert into", $sqlfile);
	for($i=1; $i< count($sqlarray); $i++){
		$sql = "insert into ".$sqlarray[$i];
		echo $i." : ";
		$status = pmysql_query($sql, get_db_conn());
		echo ": ";
		echo $status?$status:$sql;
		echo "<br>";
	}
	exit;
}

if($mode=="faq_add"){

	list($next_sort)=pmysql_fetch_array(pmysql_query("select (sort+1) as next_sort from tblfaq order by sort DESC limit 1"));
	if(!$next_sort) $next_sort = 0;

	$query="insert into tblfaq (
	faq_type,
	faq_title,
	faq_content,
	date,
	sort
	)values(
	{$faq_type},
	'{$faq_title}',
	'{$faq_content}',
	'{$date}',
	{$next_sort}
	)";
	pmysql_query($query);
	
	$msg="등록이 완료되었습니다.";
	msg($msg,"faq.php");
	
}else if($mode=="faq_mod"){
	$query="update tblfaq set faq_type={$faq_type},	faq_title='{$faq_title}', faq_content='{$faq_content}' where no={$no}";
	pmysql_query($query);
	
	$msg="수정이 완료되었습니다.";
	msg($msg,"faq_register.php?no={$no}&mode=faq_mod");
}else if($mode=="faq_del"){
	
	pmysql_query("delete from tblfaq where no={$no}");
	$msg="삭제가 완료되었습니다.";
	msg($msg,"faq.php");
}else if($mode=="faq_apply"){  // 리스트상의 베스트, 순서 적용
	//exdebug($_POST);
	$best_check=$_POST[best_check];
	$n_best=$_POST[n_best];
	$n_sort=$_POST[n_sort];
	$check_out="in";
	$ex_best=implode("','",$best_check);

	$query="select * from tblfaq where no in ('{$ex_best}')";
	$result=pmysql_query($query);
	
	while($data=pmysql_fetch_object($result)){
		if($data->faq_best=="Y") $in_no[]=$data->no;
		
	}
	foreach($n_best as $no){
		$n_qry="update tblfaq set faq_best='N' where no={$no}";		
		pmysql_query($n_qry);
	}
	
	$s_query="select * from tblfaq where no in ('{$ex_best}')";
	$s_result=pmysql_query($s_query);
	while($s_data=pmysql_fetch_object($s_result)){
		$u_qry="update tblfaq set faq_best='Y' where no={$s_data->no}";		
		pmysql_query($u_qry);
		
	}

	$check_qry="select count(*) t_count from tblfaq where faq_best='Y'";
	$check_result=pmysql_query($check_qry);
	while($check_data=pmysql_fetch_object($check_result)){
		if($check_data->t_count>10){
			$check_out="over";
		}
	}
	
	if($check_out=="over"){
		
		if($in_no)$ex_not_no=implode("','",$in_no);
		$query="select * from tblfaq where no in ('{$ex_best}') ";
		if($ex_not_no) $query.="and no not in ('{$ex_not_no}')";
		
		$result=pmysql_query($query);
		
		while($data=pmysql_fetch_object($result)){
			$u_qry="update tblfaq set faq_best='N' where no={$data->no}";		
			pmysql_query($u_qry);
		}
		$msg="5개이상 선택이 불가능합니다.";
		msg($msg,"faq.php");	
	}else{
		//순서를 적용한다.
		
		for($k=0;$k < count($n_best);$k++){
			$ns_qry="update tblfaq set sort='".$n_sort[$k]."' where no='".$n_best[$k]."'";		
			//echo $ns_qry."<br>";
			pmysql_query($ns_qry);
		}

		$msg="적용이 완료되었습니다.";
		msg($msg,"faq.php");	
	}
}else if($mode=='faq_cate_ins'){
	$faq_category_name=$_POST[faq_category_name];
	$max_sort=pmysql_fetch_object(pmysql_query("select max(sort_num) as maxnum from tblfaqcategory"));
	$sortnum=$max_sort->maxnum+1;
	$qry="insert into tblfaqcategory (faq_category_name,date,sort_num)values('{$faq_category_name}', '{$date}', '{$sortnum}')";
	
	pmysql_query($qry);
	$msg="카테고리가 등록 되었습니다.";
	msg($msg,"faq_category.php");	
	
}else if($mode=='faq_cate_mod'){
	$faq_category_name=$_POST[faq_category_name];
	
	$qry="update tblfaqcategory set faq_category_name='{$faq_category_name}' where num='{$no}'";
	
	pmysql_query($qry);
	$msg="카테고리가 수정 되었습니다.";
	msg($msg,"faq_category.php");	
}else if($mode=='faq_cate_del'){
	$qry="delete from tblfaqcategory where num='{$no}'";
	pmysql_query($qry);
	
	$del_qry="delete from tblfaq where faq_type='{$no}'";
	pmysql_query($del_qry);
	
	$msg="카테고리가 삭제 되었습니다.";
	msg($msg,"faq_category.php");	
	
}else if($mode=="faq_cate_secret"){
	$num=$_POST[num];
	$secret=$_POST[secret];
	$sort_num=$_POST[sort_num];
	
	foreach($num as $k){
		$up_qry="update tblfaqcategory set sort_num='{$sort_num[$k]}' where num='{$k}'";
		pmysql_query($up_qry);
		
		$up_qry="update tblfaqcategory set secret='0' where num='{$k}'";
		pmysql_query($up_qry);
	}
	
	foreach($secret as $v){
		$up_qry="update tblfaqcategory set secret='1' where num='{$v}'";
		pmysql_query($up_qry);
	}
	$msg="변경되었습니다.";
	msg($msg,"faq_category.php");	
}
?>
