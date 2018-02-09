<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=member_excel_".date("Ymd").".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

//print_r($_POST);

$arrgroup=array();
$sql = "SELECT group_code, group_name FROM tblmembergroup ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$arrgroup[$row->group_code]=$row->group_name;
}
pmysql_free_result($result);

$joindate = $_shopdata->joindate;
$CurrentTime = time();
$period[0] = substr($joindate,0,4)."-".substr($joindate,4,2)."-".substr($joindate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m",$CurrentTime)."-01";
$period[4] = date("Y",$CurrentTime)."-01-01";

$sort=(int)$_POST["sort"];
$scheck=(int)$_POST["scheck"];
$group_code=$_POST["group_code"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
//$vperiod=(int)$_POST["vperiod"];
$referer1 = $_POST["referer1"];
$search=$_POST["search"];
$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);

//$ArrSort = array("date","name","id","age","reserve");
//$ArrScheck = array("id","name","email","resno","home_tel","mobile","rec_id","group_code","logindate");
$ArrScheck = array("all","id","nickname","a.name","email","logindate");

$date_start = str_replace("-","",$search_start)."000000";
$date_end = str_replace("-","",$search_end)."235959";

if($referer1) {
    //$searchsql = " AND a.mb_referrer1 = '{$referer1}' ";
    $searchsql = " AND a.mb_referrer2 = '{$referer1}' "; // 가입경로에서 적립경로로 변경 (2015.12.30 - 김재수)
}

if ($scheck=="0") {	//통합
    $searchsql .= "AND date >= '{$date_start}' AND date <= '{$date_end}' ";
	if ($search) {
        $searchsql .= " AND id||nickname||a.name||email like '%{$search}%'";
	}

    $sql = "SELECT  id, a.name, email, reserve, date, rec_id, b.group_name, nickname, mb_type, 
	                c.name as ref_name1, mb_referrer2, d.name as ref_name2 
            FROM    tblmember a 
            left join tblmembergroup b on a.group_code = b.group_code 
            left join tblaffiliatesinfo c on a.mb_referrer1 = c.idx::character varying 
            left join tblaffiliatesinfo d on a.mb_referrer2 = d.idx::character varying 
            WHERE 1=1 {$searchsql} 
        ";

}else if ($scheck=="5") {	//오늘 로그인 회원 검색
	if ($search) {
		//$searchsql = "AND id LIKE '{$search}%' ";
        $searchsql .= " AND id||nickname||a.name||email like '%{$search}%'";
	}
	//$sql = "SELECT * FROM tblmember WHERE logindate >= '".date("Ymd")."000000' {$searchsql} ";
    $sql = "SELECT  id, a.name, email, reserve, date, rec_id, b.group_name, nickname, mb_type, 
	                c.name as ref_name1, mb_referrer2, d.name as ref_name2 
            FROM    tblmember a 
            left join tblmembergroup b on a.group_code = b.group_code 
            left join tblaffiliatesinfo c on a.mb_referrer1 = c.idx::character varying
            left join tblaffiliatesinfo d on a.mb_referrer2 = d.idx::character varying 
            WHERE logindate >= '".date("Ymd")."000000' {$searchsql} 
        ";
} else {
	$searchsql .= "AND date >= '{$date_start}' AND date <= '{$date_end}' ";
	if ($search) {
		$searchsql.= "AND {$ArrScheck[$scheck]} LIKE '{$search}%' ";
	}
	//$sql = "SELECT * FROM tblmember WHERE 1=1 {$searchsql} ";
    $sql = "SELECT  id, a.name, email, reserve, date, rec_id, b.group_name, nickname, mb_type, 
	                c.name as ref_name1, mb_referrer2, d.name as ref_name2 
            FROM    tblmember a 
            left join tblmembergroup b on a.group_code = b.group_code 
            left join tblaffiliatesinfo c on a.mb_referrer1 = c.idx::character varying 
            left join tblaffiliatesinfo d on a.mb_referrer2 = d.idx::character varying 
            WHERE 1=1 {$searchsql} 
        ";
}

switch ($sort) {
    case "0":	//등록일
        $sql.= "ORDER BY date DESC ";
        break;
    case "1":	//회원명
        $sql.= "ORDER BY name ASC ";
        break;
    case "2":	//아이디
        $sql.= "ORDER BY id ASC ";
        break;
    case "5":	//최종 로그인 기준
        $sql.= "ORDER BY logindate DESC ";
        break;
    default :	//등록일
        $sql.= "ORDER BY date DESC ";
        break;
}
//echo $sql;
$result=pmysql_query($sql,get_db_conn());
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<table border="1">
    <tr align="center">
        <th>번호</th>
        <th>아이디</th>
        <th>E-mail</th>
        <th>성명</th>
        <th>닉네임</th>
        <th>추천인</th>
        <th>가입일</th>
        <th>적립경로</th>
        <th>가입구분</th>
        <th>적립금</th>
    </tr>
<?
$num = 0;
while($row=pmysql_fetch_object($result)) {
    
    $num++;
    $reg_date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." (".substr($row->date,8,2).":".substr($row->date,10,2).")";
	if ($row->mb_type == 'facebook') $row_mb_type	= "facebook";
	if ($row->mb_type == 'web') $row_mb_type	= "일반";
	if ($row->mb_type == 'adm') $row_mb_type	= "관리자";
?>
    <tr>
      <td align="center"><?=number_format($num)?></td>
      <td><?=$row->id?></td>
      <td><?=$row->email?></td>
      <td><?=$row->name?></td>
      <td><?=$row->nickname?></td>
      <td><?=$row->rec_id?></td>
      <td><?=$reg_date?></td>
      <td><?=$row->ref_name2?></td>
      <td><?=$row_mb_type?></td>
      <td align="right"><?=number_format($row->reserve)?>&nbsp;</td>
    </tr>
<?
}
?>
</table>
</body>
</html>
<?
pmysql_free_result($result);
?>
