<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

Header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=product_review_excel_".date("YmdHis").".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");

$excel_sql=$_REQUEST["excel_sql"];

$result=pmysql_query($excel_sql,get_db_conn());
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<table border="1">
	<tr align="center">
		<th>등록일</th>
		<th>이름</th>
		<th>아이디</th>
		<th>상품명</th>
		<th>리뷰제목</th>
		<th>리뷰내용</th>
		<th>별점</th>
		<th>리뷰 타입</th>
		<th>베스트</th>
		<th>포인트</th>
    </tr>
<?
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					//별점
					$marks="";
					for($i=0;$i<$row->marks;$i++){
						$marks.="<FONT color=#000000>★</FONT>";
					}
					for($i=$row->marks;$i<5;$i++){
						$marks.="<FONT color=#DEDEDE>★</FONT>";
					}
					
					echo "<tr align=center>\n";
					echo "<TD>".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."</td>\n";
					echo "<TD style=\"text-align:left;border-bottom:none;word-break:break-all;\">{$row->name}</td>\n";
					echo "<TD style=\"text-align:left;border-bottom:none;\">{$row->id}</td>\n";
					echo "<TD><font color=#3D3D3D>".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</font></td>";
					echo "<TD><b>".$row->subject."</b></td>";
					echo "<TD>".$row->content."</td>";
					echo "<TD>".$marks."</td>";
                    echo "  <TD>\n";
                    if ( $row->type == '1' ) {
                        echo "  포토리뷰";
                    } else {
                        echo "  텍스트리뷰";
                    }
                    echo "  </TD>\n";
					echo "	<TD align=center>";
					if($row->best_type){
						echo $row->best_type;
					}else{
						echo "No";
					}
					echo "	</td>";
					echo "	<TD align=center>";
					if(ord($row->id)==0) {
						echo "<font color=red><B>X</B></font>";
					} else if ($row->reserve==0) {
						echo "0";
					} else {
						echo number_format($row->reserve);
					}
					echo "	</td>\n";
					echo "</tr>\n";
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 리뷰 정보가 존재하지 않습니다.</td></tr>";
				}
?>
<!--
                    <tr>
                        <td colspan="<?=$colspan?>">
                            <div style="text-align:center;padding-bottom:40px;">
                                <img src="../admin/images/admin_button05.gif" onclick="javascript:changeBestReview(1);" alt="베스트리뷰 등록">
                                <img src="../admin/images/admin_button06.gif" onclick="javascript:changeBestReview(0);" alt="베스트리뷰 해제">
                            </div>
                        </td>
                    </tr>
-->
				</table>
				</div>
				</td>
			</tr>
</table>
</body>
</html>