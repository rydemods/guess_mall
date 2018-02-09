<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_POST);
//exdebug($_GET);

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=member_log_list_excel_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


$s_check        = $_POST["s_check"];
$search         = trim($_POST["search"]);
$s_date         = $_POST["s_date"];
$ord_flag       = $_POST["ord_flag"]; // 적립경로
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];

$search_start = $search_start?$search_start:"";
$search_end = $search_end?$search_end:"";
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

// 기본 검색 조건
$qry_from = "tblmemberlog ml ";
$qry_from.= "JOIN 	tblmember m on ml.id = m.id ";
$qry.= "WHERE 1=1 ";
$qry.= "AND ml.type = 'login' ";

// 기간선택 조건
if ($search_s != "" || $search_e != "") { 
	$qry.= "AND ml.date >= '{$search_s}' AND ml.date <= '{$search_e}' ";
}

// 검색어
if(ord($search)) {
	if($s_check=="id") $qry.= "AND ml.id = '{$search}' ";
    else if($s_check=="name") $qry.= "AND m.name like '%{$search}%' ";
}

// 전부 체크된 상태로 만들기 위해 기본값으로 넣자..2016-04-18 jhjeong
//exdebug("cnt = ".count($ord_flag));
if(count($ord_flag) == 0) {
    $ord_flag = array("web", "mobile", "app");
}

if(is_array($ord_flag)) $ord_flag = implode(",",$ord_flag);

$access_arr  = explode(",",$ord_flag);
//exdebug("ord_flag = ".$ord_flag);
//exdebug($access_arr);

// 유입경로
if( count($access_arr)  ) {
    $qry.= "AND ml.access_type in ('".implode("','", $access_arr)."') ";
}

$sql = "SELECT  ml.sno, ml.id, m.name, ml.type, ml.access_type, ml.date 
        FROM {$qry_from} {$qry} 
        ORDER BY ml.sno DESC 
        ";

$result=pmysql_query($sql,get_db_conn());
//echo "sql = ".$sql."<br>";
//exdebug($sql);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

				<table border=1 cellpadding=0 cellspacing=0 width=100%>			
				<TR >
					<th>번호</th>
					<th>로그인날짜</th>
					<th>회원ID</th>
					<th>회원명</th>
					<th>접속경로</th>
				</TR>
<?
		$colspan=12;

		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
        $access_type = "";
		while($row=pmysql_fetch_object($result)) {

			$regdt = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." (".substr($row->date,8,2).":".substr($row->date,10,2).")";
        
            if($row->access_type == 'web') $access_type = "PC";
            else if($row->access_type == 'mobile') $access_type = "MOBILE";
            else if($row->access_type == 'app') $access_type = "APP";
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td align="center"><?=$cnt+1?></td>
                    <td align="center"><?=$regdt?></td>
                    <td align="center"><?=$row->id?></td>
			        <td align="center"><?=$row->name?></td>
                    <td align="center"><?=$access_type?></td>
                </tr>
<?
            $cnt++;
        }
        pmysql_free_result($result);
?>
				</TABLE>
</body>
</html>