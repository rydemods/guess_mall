<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$board=$_POST["board"];

include($Dir.BoardDir."file.inc.php");

//게시판 삭제
if($mode=="delete" && ord($board)) {
	if($board=="qna") {
		$onload="<script>window.onload=function(){alert(\"기본적으로 제공되는 게시판은 삭제하실 수 없습니다.\");}</script>";
	}

	$prqnaboard=getEtcfield($_shopdata->etcfield,"PRQNA");

	$sql = "DELETE FROM tblboardadmin WHERE board='{$board}' ";
	if(pmysql_query($sql,get_db_conn())) {
		pmysql_query("DELETE FROm tblboard WHERE board='{$board}'",get_db_conn());
		pmysql_query("DELETE FROM tblboardcomment WHERE board='{$board}'",get_db_conn());
		ProcessBoardDir($board,"delete");

		$sql = "DELETE FROM tbldesignnewpage WHERE type='board' AND filename='{$board}' ";
		pmysql_query($sql,get_db_conn());

		if($prqnaboard==$board) {
			$_shopdata->etcfield=setEtcfield($_shopdata->etcfield,"PRQNA","");
		}

		$onload="<script>window.onload=function(){alert(\"해당 게시판 및 게시물을 삭제하였습니다.\");}</script>";
	} else {
		$onload="<script>window.onload=function(){alert(\"게시판 삭제중 오류가 발생하였습니다.\");}</script>";
	}
}
include"header.php"; 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}

function ModifyBasicInfo(board) {
	window.open("","basicinfo","height=600,width=620,scrollbars=yes,resizable=no");
	document.form2.mode.value="";
	document.form2.board.value=board;
	document.form2.action="community_basicinfo_pop.php";
	document.form2.target="basicinfo";
	document.form2.submit();
	document.form2.board.value="";
	document.form2.action="";
	document.form2.target="";
}

function ModifySpecialInfo(board) {
	window.open("","specialinfo","height=600,width=860,scrollbars=yes,resizable=no");
	document.form2.mode.value="";
	document.form2.board.value=board;
	document.form2.action="community_specialinfo_pop.php";
	document.form2.target="specialinfo";
	document.form2.submit();
	document.form2.board.value="";
	document.form2.action="";
	document.form2.target="";
}

function BoardDesignInfo(board) {
	window.open("","designinfo","height=260,width=470,scrollbars=no,resizable=no");
	document.form2.mode.value="";
	document.form2.board.value=board;
	document.form2.action="community_designinfo_pop.php";
	document.form2.target="designinfo";
	document.form2.submit();
	document.form2.board.value="";
	document.form2.action="";
	document.form2.target="";
}

function BoardDelete(board) {
	msg="게시판을 삭제하시겠습니까?\n\n해당 게시판의 게시물도 모두 삭제됩니다.";
<?php if(ord($prqnaboard)){?>
	if(board=="<?=$prqnaboard?>") {
		msg="본 게시판은 상품QNA로 사용중입니다.\n\n게시판을 삭제하시겠습니까?\n\n해당 게시판의 게시물도 모두 삭제됩니다.";
	}
<?php }?>
	if(confirm(msg)) {
		document.form2.mode.value="delete";
		document.form2.board.value=board;
		document.form2.submit();
	}
}

function BoardOrder() {
	window.open("community_order_pop.php","boardorder","height=350,width=400,scrollbars=no,resizable=no");
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>게시판 리스트 관리</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_cscenter.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">게시판 리스트 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>등록된 게시판의 기능/디자인 변경 및 삭제처리를 할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
					<div class="title_depth3_sub">등록된 게시판 목록 및 관리</div>
                </td>
            </tr>
            <tr>
            	<td style="padding-top:3pt; padding-bottom:3pt;">                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 기본기능 : 게시판의 기본기능을 설정 할 수 있습니다.</li>
                            <li>2) 특수기능 : 게시판의 특수한 기능을 설정 할 수 있습니다.</li>
                            <li>3) 게시판 디자인 관리 : 게시판의 디자인을 변경할 수 있습니다.</li>
                        </ul>
                    </div>                    
            	</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=30></col>
				<col width=></col>
				<col width=110></col>
				<col width=70></col>
				<col width=110></col>
				<col width=120></col>
				<col width=60></col>
				<TR align=center>
					<th>No</th>
					<th>게시판 제목</th>
					<th>게시판 형태</th>
					<th>비밀번호</th>
					<th>접근권한</th>
					<th>기능설정</th>
					<th>삭제</TD>
				</TR>
<?php
				$arr_skin=array();
				$sql = "SELECT SUBSTR(board_skin,1,1) as skin_code, COUNT(SUBSTR(board_skin,1,1)) as skin_cnt 
				FROM tblboardskin GROUP BY skin_code ";
				$result=pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					$arr_skin[$row->skin_code]=$row->skin_cnt;
				}
				pmysql_free_result($result);

				$colspan=7;
				$arr_write=array("N"=>"회원/비회원","Y"=>"회원전용","A"=>"관리자전용");
				$arr_view=array("N"=>"회원/비회원","U"=>"비회원(목록)","Y"=>"회원전용");

				$sql = "SELECT * FROM tblboardadmin ORDER BY date DESC ";
				$result=pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$cnt++;
					$btypename='';
					if(strpos($row->board_skin,'L')!==FALSE) $btypename="일반형 게시판";
					if(strpos($row->board_skin,'W')!==FALSE) $btypename="웹진형 게시판";
					if(strpos($row->board_skin,'I')!==FALSE) $btypename="앨범형 게시판";
					if(strpos($row->board_skin,'B')!==FALSE) $btypename="블로그형 게시판";
					
					$bwrite='';
					$bview='';
					if($row->grant_write!="A" && ord($row->group_code)) {
						$bwrite="등급회원";
					} else {
						$bwrite=$arr_write[$row->grant_write];
					}
					if(ord($row->group_code)) {
						$bview="등급회원";
					} else {
						$bview=$arr_view[$row->grant_view];
					}

					echo "<TR>\n";
					echo "	<TD>{$cnt}</TD>\n";
					echo "	<TD><div class=\"ta_l\">&nbsp;{$row->board_name}&nbsp;</div></TD>\n";
					echo "	<TD>&nbsp;{$btypename}</TD>\n";
					echo "	<TD>&nbsp;<B>{$row->passwd}</B>&nbsp;</td>\n";
					echo "	<TD>\n";
					echo "	<div class=\"table_none\">\n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"102\">\n";
					echo "	<col width=31></col><col width=></col>\n";
					echo "	<tr>\n";
					echo "		<td><img src=\"images/icon_write.gif\" border=\"0\"></td>\n";
					echo "		<td align=center>{$bwrite}</td>\n";
					echo "	</tr>\n";
					echo "	<tr>\n";
					echo "		<td><img src=\"images/icon_read.gif\" border=\"0\"></td>\n";
					echo "		<td align=center>{$bview}</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</div>\n";
					echo "	<TD >\n";
					echo "	<div class=\"table_none\">\n";
					echo "	<TABLE WIDTH=\"100%\" BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
					echo "	<col width=33%></col><col width=33%></col><col width=33%></col>\n";
					echo "	<TR>\n";							
					echo "		<TD><a href=\"javascript:ModifyBasicInfo('{$row->board}');\"><IMG SRC=\"images/icon_function1.gif\" ALT=\"기본기능\" border=\"0\"></a></TD>\n";
					echo "		<TD><a href=\"javascript:ModifySpecialInfo('{$row->board}');\"><IMG SRC=\"images/icon_function2.gif\" ALT=\"특수기능\" hspace=\"2\" border=\"0\"></a></TD>\n";
					if($arr_skin[$row->board_skin[0]]>1) {
						echo "	<TD><a href=\"javascript:BoardDesignInfo('{$row->board}');\"><IMG SRC=\"images/icon_function3.gif\" ALT=\"게시판 디자인 관리\" border=\"0\"></a></TD>\n";
					} else {
						echo "	<TD><IMG SRC=\"images/icon_function3r.gif\" ALT=\"게시판 디자인 관리\" border=\"0\"></a></TD>\n";
					}							
					echo "	</TR>\n";
					echo "	</TABLE>\n";
					echo "	</div>\n";

					echo "	</TD>\n";
					if($row->board=="qna") {
						echo "<TD  align=center><img src=\"images/btn_del1.gif\" border=\"0\"></TD>\n";
					} else {
						echo "<TD  align=center><a href=\"javascript:BoardDelete('{$row->board}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></TD>\n";
					}
					echo "</TR>\n";
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<TR><TD colspan={$colspan} align=center>등록된 게시판이 존재하지 않습니다.</TD></TR>";
				}
?>				

				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan=8 align=right><a href="javascript:BoardOrder();"><img src="images/icon_sort.gif"  border="0" vspace="3"></a></td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>게시판 리스트 관리</span></dt>
							<dd>- 게시판 순서 변경시 쇼핑몰에 출력되는 게시판의 순서가 변경됩니다.<br>
							- 상품 QNA 게시판으로 설정된 게시판은 상품상세설명 페이지의 상품QNA 게시판으로 사용되며, 한개만 지정 가능합니다.<br>
							- 게시판을 삭제할 경우 해당 게시물도 모두 삭제 됩니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
			<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
			<input type=hidden name=mode>
			<input type=hidden name=board>
			</form>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php 
include("copyright.php");
