<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$nowCode = $_POST["nowCode"];
$prcode = $_POST["prcode"];
$mode = $_POST["mode"];
$num = $_POST["num"];
$delIdx = $_POST["delIdx"];
$list_idx = $_POST["list_idx"];
$icon = $_POST["icon"];
$special = $_POST["special"];

//exdebug($inCateCode);
################### 상품 insert

if($mode == "insert"){
	
	$result = pmysql_query("SELECT pridx FROM tblproduct WHERE productcode='{$prcode}' ",get_db_conn());
	$row = pmysql_fetch_object($result);
	$prIdx = $row->pridx;
	pmysql_free_result($res);
	
	$num++;
	$sql = "INSERT INTO tblmainlist 
				(
					pridx
					,sort
				)VALUES(
					{$prIdx}
					,{$num}
				) 
	";
	pmysql_query($sql,get_db_conn());
}

if($mode == "delete"){
	for($i=0;$i<count($list_idx);$i++){
		$sortNum = $i+1;
		$sql = "UPDATE tblmainlist SET 
			sort = {$sortNum}
			WHERE list_idx = {$list_idx[$i]}
		";
		pmysql_query($sql,get_db_conn());
	}
	
	$sql = "DELETE FROM tblmainlist WHERE list_idx = {$delIdx} ";
	pmysql_query($sql,get_db_conn());
}

if($mode == "update"){
	for($i=0;$i<count($list_idx);$i++){
		$sortNum = $i+1;
		$sql = "UPDATE tblmainlist SET 
			sort = {$sortNum}
			,icon = '{$icon[$i]}'  
			WHERE list_idx = {$list_idx[$i]}
		";
		//exdebug($sql);
		pmysql_query($sql,get_db_conn());
	}
}
if($special=='1'){
	$mainList_sql = "
		SELECT 
		a.list_idx,a.icon,a.sort, 
		b.productcode,b.productname,b.sellprice,b.tinyimage,b.quantity,b.display  
		FROM tblmainlist a 
		JOIN tblproduct b ON a.pridx=b.pridx
		ORDER BY sort ASC 
		";
	$mainList_res = pmysql_query($mainList_sql,get_db_conn());
	$nowCateNum = pmysql_num_rows($mainList_res);
	while($mainList_row = pmysql_fetch_array($mainList_res)){
		$mainList[] = $mainList_row;
	}
	pmysql_free_result($mainList_res);
}

?>

<?php include("header.php"); ?>

<style>td {line-height:18pt;}</style>
<link rel="styleSheet" href="/css/admin.css" type="text/css"></link>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('MainPrdtFrame')");</script>
<script type="text/javascript">

function ChangeSpecial(val){
	$("#nowCode").val(val);
	$("#frm1").submit();
}

function InsertSpecial(){
	if($("input[name=prcode]").val().length == 0){
		alert("메인 진열상품에 추가할 상품을 선택하세요.");
		$("input[name=prcode]").focus();
		return;
	}
	
	if($("input[name=num]").val()>=50){
		alert('메인 진열상품은 최대 50개까지 등록가능합니다.');
		return;
	}
	
	if (confirm("해당 상품을 메인 진열상품으로 포함하시겠습니까?")){
		var returnValue = 0;
		$("input[name=productcode]").each(function(){
			if($(this).val()==$("input[name=prcode]").val()){
				alert('이미 등록된 상품입니다.');
				returnValue = 1;
				return;
			}
		});
		if(returnValue == 1) return;
		$("input[name=mode]").val("insert");
		$("#frm1").submit();
	}

}

function ProductInfo(prcode){
	var code=prcode.substring(0,12);
	var popup="YES";
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}
function iconUp(number){
	var openUrl = "product_recommendlist_icon.php?index="+number;
	window.open(openUrl,"iconPop","width=500,height=115");
}

function deleteItem(idx){
	$("tr[name=prList]").each(function(){
		if($(this).attr("alt")==idx){
			$(this).remove();
		}
	});
	$("input[name=delIdx]").val(idx);
	$("input[name=mode]").val("delete");
	$("#frm1").submit();
}

function move_save(){
	$("input[name=mode]").val("update");
	$("#frm1").submit();
}

function ChangeSpecial(val) {
	if (val!="<?=$special?>") {
		document.form1.submit();
	}
}

$(document).ready(function(){
	//위로 이동
	$("a[name=upChange]").click(function(e){
		//클릭된 TR
		var targetTR = $(e.target).parent().parent().parent();
		if($(targetTR).prev().attr("name") == "listTitle") return;
		$(targetTR).prev().before($(targetTR));
	});
	//아래로 이동
	$("a[name=downChange]").click(function(e){
		//클릭된 TR
		var targetTR = $(e.target).parent().parent().parent();
		if($(targetTR).next().length == 0) return;
		$(targetTR).next().after($(targetTR));
	});
})

</script>

<form name=form1 id="frm1" action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=prcode>
<input type="hidden" name="inCateCode" value="<?=$inCateCode?>">
<input type="hidden" name="mode" />
<input type=hidden name=selcode>
<input type=hidden name=Scrolltype value="<?=$Scrolltype?>">
<input type="hidden" name="num" value="<?=$nowCateNum?>"/>
<input type="hidden" name="delIdx" >
	<div class="main_view_setup_wrap" style="margin-top:15px;">
		<div class="group">
			<input type=radio id="idx_special1" name="special" <?if($special==1) echo "checked"?> onClick="ChangeSpecial(this.value)" name=special value="1" >
			<label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_special1 class="font_d12">메인진열</label>&nbsp;&nbsp;&nbsp;
			
			<input type=radio id="idx_special2" name="special" <?if($special==2) echo "checked"?> onClick="ChangeSpecial(this.value)" name=special value="2" >
			<label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_special2 class="font_d12">메인진열 2</label>&nbsp;&nbsp;&nbsp;
		</div>
		
		<div class="list">

			<div class="table_main_setup"  id="divscroll" style="height:400px;overflow-x:hidden;overflow-y:auto;">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<colgroup>
						<col width=30></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
					</colgroup>
					<tr name="listTitle" >
						<th>No</th>	
						<th colspan="3">상품명</th>	
						<th>판매가격</th>	
						<th>수량</th>	
						<th>상태</th>	
						<th>아이콘 등록</th>
						<th>수정</th>	
						<th>삭제</th>	
					</tr>
<?php
					foreach($mainList as $k=>$v){
?>
					<tr align="center" name="prList" alt="<?=$v[list_idx]?>">
						<td><?=$v[sort]?></td>
						<td>
							<a name="upChange" style="cursor: hand;">
								<img src="images/btn_plus.gif" border="0" style="margin-bottom: 3px;" />
							</a>
							<br>
							<a name="downChange" style="cursor: hand;">
								<img src="images/btn_minus.gif" border="0" style="margin-top: 3px;" />
							</a>
						</td>
						<td style="position:relative;">
							<img style="width: 40px; height:40px;" src="<?=$Dir.DataDir?>shopimages/product/<?=$v[tinyimage]?>" border="1"/>
							<input type="hidden" name="pridx" value="<?=$v[pridx]?>"/>
							<input type="hidden" name="productcode" value="<?=$v[productcode]?>"/>
						</td>
						<td align="left" style="word-break:break-all;">
							<img src="" border="0" align="absmiddle" hspace="2"/>
							<?=$v[productname]?> &nbsp;
						</td>
						<td align="right">
							<img src="" border="0" style="margin-right:2px;"/>
							<span class="font_orange"><?=$v[sellprice]?></span>
						</td>
						<td>
					<?if($v[quantity]==null){?>
							무제한
					<?}else if($v[quantity]==0) {?>
							<span class="font_orange"><b>품절</b></span>
					<?}else{?>
							<?=$v[quantity]?>
					<?}?>
						</td>
						<td>
					<?if($v[display]=='Y'){?>
							<font color="#0000FF">판매중</font>
					<?}else{?>
							<font color="#FF4C00">보류중</font>
					<?}?>
						</td>
						<td>
					<?if($v[icon]==''){?>
						<img src="img/btn/btn_cate_reg.gif" onclick="iconUp(<?=$k?>)" border="0" style="cursor: hand;" />
					<?}else{?>
						<?if(is_file($Dir."img/icon/".$v[icon])){?>
						<img src="<?=$Dir?>img/icon/<?=$v[icon]?>" style="max-height: 30px; max-width: 30px;"><br>
						<?}?>
						<img src="img/btn/btn_cate_modify.gif" onclick="iconUp(<?=$k?>)" border="0" style="cursor: hand;" />
					<?}?>
						</td>
						<td>
							<img src="images/icon_newwin1.gif" onclick="ProductInfo('<?=$v[productcode]?>');" border="0" style="cursor: hand;" />
						</td>
						<td>
							<img src="images/icon_del1.gif" onclick="deleteItem('<?=$v[list_idx]?>');" border="0" style="cursor: hand;" />
						</td>
						<input type="hidden" name="list_idx[]" value="<?=$v[list_idx]?>">
						<input type="hidden" name="icon[]" id="icon_<?=$k?>" value="<?=$v[icon]?>">
					</tr>
<?					}	?>
				</table>
			</div>
		</div>
	</div>
</form>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<TR>
	<TD colspan="<?=$colspan?>" align=center><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">* 순서변경은 변경을 원하는 상품을 선택 후 키보드 ↑(상)↓(하) 키로 이동해 주세요.</span></TD>
</TR>
<tr>
	<TD align=center><a href="javascript:move_save();"><img src="images/btn_mainarray.gif" border="0"></a></TD>
</tr>
</table>

<form name=form_reg action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>
