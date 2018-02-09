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
//exdebug($_POST);
//exdebug($_GET);

$nowCode = $_POST["nowCode"];   // 0:Best Sellers, 1:MD's Pick, 2:New Arrivals
$prcode = $_POST["prcode"];
$mode = $_POST["mode"];
$num = $_POST["num"];
$delIdx = $_POST["delIdx"];
$list_idx = $_POST["list_idx"];
$icon = $_POST["icon"];
################### 상품 insert

if($mode == "insert"){
	$codeIdx = $nowCode;

	$result = pmysql_query("SELECT pridx FROM tblproduct WHERE productcode='{$prcode}' ",get_db_conn());
	$row = pmysql_fetch_object($result);
	$prIdx = $row->pridx;
	pmysql_free_result($res);

	$num++;
	$sql = "INSERT INTO tblproduct_mainitem_list
				(
					pridx
					,category_type
					,sort
                    ,section 
				)VALUES(
					{$prIdx}
					,{$codeIdx}
					,{$num}
                    ,'C'
				)
	";
	pmysql_query($sql,get_db_conn());
}

if($mode == "chk_insert"){//체크리스트로 여러 상품 입력시 쿼리 2015 06 04 원재
	$prcode_chk=explode(",",$prcode);
	//exdebug($prcode_chk);
	$codeIdx = $nowCode;

	for($i=0;$i<count($prcode_chk);$i++){//상품 개수 만큼 반복해서 insert

        $result = pmysql_query("SELECT pridx FROM tblproduct WHERE productcode='{$prcode_chk[$i]}' ",get_db_conn());
        $row = pmysql_fetch_object($result);
        $prIdx = $row->pridx;
        pmysql_free_result($res);

        $num++;
        $sql = "INSERT INTO tblproduct_mainitem_list
                    (
                        pridx
                        ,category_type
                        ,sort
                        ,section 
                    )VALUES(
                        {$prIdx}
                        ,{$codeIdx}
                        ,{$num}
                        ,'C'
                    )
        ";
        pmysql_query($sql,get_db_conn());
	}
}

if($mode == "delete"){
	for($i=0;$i<count($list_idx);$i++){
		$sortNum = $i+1;
		$sql = "UPDATE tblproduct_mainitem_list SET
			sort = {$sortNum}
			WHERE list_idx = {$list_idx[$i]}
		";
		pmysql_query($sql,get_db_conn());
	}

	$sql = "DELETE FROM tblproduct_mainitem_list WHERE list_idx = {$delIdx} ";
	pmysql_query($sql,get_db_conn());
}

if($mode == "update"){
	for($i=0;$i<count($list_idx);$i++){
		$sortNum = $i+1;
		$sql = "UPDATE tblproduct_mainitem_list SET
			sort = {$sortNum}
			WHERE list_idx = {$list_idx[$i]}
		";
		//exdebug($sql);
		pmysql_query($sql,get_db_conn());
	}
}


if(!$nowCode) $nowCode = 0;


############## 카테고리별 리스트 불러오기
$nowCateList_sql = "
	SELECT
	a.list_idx,a.sort,
	c.productcode,c.productname,c.sellprice ,c.tinyimage,c.quantity,c.display
	FROM tblproduct_mainitem_list a
	JOIN tblproduct c ON a.pridx=c.pridx
	WHERE a.category_type = {$nowCode} 
    AND section = 'C'
	ORDER BY sort ASC
";
//echo $nowCateList_sql;
$nowCateList_res = pmysql_query($nowCateList_sql,get_db_conn());
$nowCateNum = pmysql_num_rows($nowCateList_res);
while($nowCateList_row = pmysql_fetch_array($nowCateList_res)){
	$nowCateList[] = $nowCateList_row;
}
pmysql_free_result($nowCateList_res);

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
	$('input[name=inCateCode]',window.parent.document).val(val);
	$("#frm1").submit();
}

function InsertSpecial(chk){
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
		if(chk!=1){//진열버튼으로 입력 받았을때
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

		if(chk==1){//2015 06 04 체크리스트로 여러 상품 동시 입력조건 원재
			var List;
			var chkList=$("input[name=prcode]").val().split(",");
			$("input[name=productcode]").each(function(index,item){
				for(i=0;i<chkList.length;i++){
					if($(item).val()==chkList[i]){
						alert('이미 등록된 상품이 존재합니다. 이 상품을 제외하고 나머지 상품을 추가합니다');
						chkList.splice(i,1);
						i--;
					}
				}
			});
			$("input[name=mode]").val("chk_insert");
			$("input[name=prcode]").val(chkList);
			$("#frm1").submit();
		}
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
<input type="hidden" name="nowCode" id="nowCode" value="<?=$nowCode?>" >
<input type="hidden" name="num" value="<?=$nowCateNum?>"/>
<input type="hidden" name="delIdx" >
	<div class="main_view_setup_wrap" style="margin-top:15px;">
		<div class="group">
			<input type=radio id="idx_special1" name=special value="0" checked onClick="ChangeSpecial(this.value);">
			<label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_special1 class="font_d12">하단 영역</label>&nbsp;&nbsp;&nbsp;
		</div>

		<div class="list">

			<div class="table_main_setup"  id="divscroll" style="height:800px;width:100%;overflow-x:hidden;overflow-y:auto;">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<colgroup>
						<col width=30></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<!-- <col width=></col> -->
						<col width=></col>
					</colgroup>
					<tr name="listTitle" >
						<th>No</th>
						<th colspan="3">상품명</th>
						<th>판매가</th>
						<th>수량</th>
						<th>상태</th>
						<!-- <th>수정</th> -->
						<th>삭제</th>
					</tr>
<?php
					foreach($nowCateList as $k=>$v){
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
						<td style="word-break:break-all;text-align:left;">
							<img src="" border="0" align="absmiddle" hspace="2"/>
							<?=$v[productname]?> &nbsp;
						</td>
						<td align="right">
							<span class="font_orange"><?=number_format($v[sellprice])?></span>
						</td>
						<td>
					<?if($v[quantity]==null){?>
							무제한
					<?}else if($v[quantity]==0) {?>
							<span class="font_orange"><b>품절</b></span>
					<?}else{?>
							<?=number_format($v[quantity])?>
					<?}?>
						</td>
						<td>
					<?if($v[display]=='Y'){?>
							<font color="#0000FF">판매중</font>
					<?}else{?>
							<font color="#FF4C00">보류중</font>
					<?}?>
						</td>
						<!-- <td>
							<img src="images/icon_newwin1.gif" onclick="ProductInfo('<?=$v[productcode]?>');" border="0" style="cursor: hand;" />
						</td> -->
						<td>
							<img src="images/icon_del1.gif" onclick="deleteItem('<?=$v[list_idx]?>');" border="0" style="cursor: hand;" />
						</td>
						<input type="hidden" name="list_idx[]" value="<?=$v[list_idx]?>">
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
