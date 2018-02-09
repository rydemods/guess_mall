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
Header("Content-Disposition: attachment; filename=member_staff_point_list_excel_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


$s_check    = $_POST["s_check"];
$search     = trim($_POST["search"]);
$s_date     = $_POST["s_date"];

$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];

$search_start = $search_start?$search_start:"";
$search_end = $search_end?$search_end:"";
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

// 기본 검색 조건
$qry_from = "tblpoint_staff a ";
$qry_from.= "JOIN 	tblmember b on a.mem_id = b.id ";
$qry.= "WHERE 1=1 ";

// 기간선택 조건
if ($search_s != "" || $search_e != "") { 
	$qry.= "AND a.regdt >= '{$search_s}' AND a.regdt <= '{$search_e}' ";
}

// 검색어
if(ord($search)) {
	if($s_check=="id") $qry.= "AND a.mem_id = '{$search}' ";
    else if($s_check=="name") $qry.= "AND b.name like '%{$search}%' ";
}

$sql = "SELECT  a.pid, a.mem_id, b.name, a.regdt, a.body, a.point, a.expire_date, a.tot_point 
        FROM {$qry_from} {$qry} 
        ORDER BY a.pid DESC 
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
					<th>처리일자</th>
					<th>회원ID</th>
					<th>회원명</th>
					<th>상세내역</th>
                    <th>적립마일리지</th>
					<th>사용마일리지</th>
					<th>잔여마일리지</th>
					<th>만료예정일</th>
				</TR>
<?
		$colspan=12;

		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {

			$regdt = substr($row->regdt,0,4)."/".substr($row->regdt,4,2)."/".substr($row->regdt,6,2)." (".substr($row->regdt,8,2).":".substr($row->regdt,10,2).")";
			$expiredt = substr($row->expire_date,0,4)."/".substr($row->expire_date,4,2)."/".substr($row->expire_date,6,2);

            $reserve_point = 0;
            $use_point = 0;
            $tot_point = $row->tot_point;
            if($row->point < 0) $use_point = $row->point;
            else $reserve_point = $row->point;
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td align="center"><?=$cnt+1?></td>
                    <td align="center"><?=$regdt?></td>
                    <td align="center"><?=$row->mem_id?></td>
			        <td align="center"><?=$row->name?></td>
                    <td align="center"><?=$row->body?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($reserve_point)?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($use_point)?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($tot_point)?></td>
                    <td align="center"><?=$expiredt?></td>
                </tr>
<?
            $cnt++;
        }
        pmysql_free_result($result);
?>
				</TABLE>
</body>
</html>