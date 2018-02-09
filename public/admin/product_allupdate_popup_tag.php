<?php 
/******************************************************
- 일괄 상품 태그 변경
- 추가 OR 수정
- tblproduct.keyword
- 2016-07-08 by JeongHo, Jeong
*******************************************************/
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

include_once("../lib/adminlib.php");
include_once("../conf/config.php");

//exdebug($_POST);

$mode               = $_POST['mode'];
$keyword            = pmysql_escape_string(trim($_POST['keyword']));
$arrProdList        = $_POST['prod_list'];
$category           = $_POST['category'];
$sabangnet_prop_option  = pmysql_escape_string(trim($_POST['sabangnet_prop_option']));
$sabangnet_prop_val     = pmysql_escape_string(trim($_POST["sabangnet_prop_val"]));

if ( $mode == "add" || $mode == "update" ) {

    if($mode == "add") {
        $keyword = ",".$keyword;
        $tag_sql = "keyword = concat(keyword, '".$keyword."') ";
    } else if($mode == "update") {
        $tag_sql = "keyword = '".$keyword."' ";
    }

    $productcode = "('".implode("','", $arrProdList)."')";

    $flagResult = true;
    BeginTrans();
    try {
        // keyword 업데이트
        $sql  = "UPDATE tblproduct ";
        $sql .= "SET ".$tag_sql;
        $sql .= "WHERE productcode in {$productcode}";
        $result = pmysql_query($sql, get_db_conn());
        //echo "sql = ".$sql."<br>";
        if ( empty($result) ) {
            throw new Exception('Insert Fail');
        }
    } catch (Exception $e) {
        $flagResult = false;
        RollbackTrans();
    }
    CommitTrans();

    msg("적용되었습니다.");
}

if ( $mode == "category" ) {
    
    foreach ( $arrProdList as $productcode) {

        #카테고리의 c_date 정보
        $c_date_sql = "SELECT c_category, c_date FROM tblproductlink WHERE c_productcode = '{$productcode}' ";
        $c_date_res = pmysql_query($c_date_sql,get_db_conn());
        //echo "sql = ".$c_date_sql."<br>";
        while($c_date_row = pmysql_fetch_object($c_date_res)){
            $c_date[$c_data_row->c_category] = $c_date_row->c_date;
        }

        #카테고리 삭제
        $sql = "DELETE FROM tblproductlink WHERE c_productcode = '{$productcode}'";
        pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";

        $in=0;
        foreach($category as $k){
            if($in==0){
                $maincate="1";
            }else{
                $maincate="0";
            }

            $date1=date("Ym");
            $date=date("dHis");

            //c_date 생성
            $c_date[$k] = ($c_date[$k])?$c_date[$k]:$date1.$date;

            $query="insert into tblproductlink (c_productcode,c_category,c_maincate,c_date,c_date_1,c_date_2,c_date_3,c_date_4) values ('".$productcode."','".$k."','".$maincate."','{$c_date[$k]}','{$c_date[$k]}','{$c_date[$k]}','{$c_date[$k]}','{$c_date[$k]}')";
            pmysql_query($query);
            //echo "sql = ".$query."<br>";
            $in++;
        }
    }
    msg("적용되었습니다.");
}

if ( $mode == "jungbo" ) {

    foreach ( $arrProdList as $productcode) {

        $sql = "Update tblproduct Set sabangnet_prop_val = '".$sabangnet_prop_val."', sabangnet_prop_option = '".$sabangnet_prop_option."' Where productcode = '".$productcode."' ";
        //echo "sql = ".$sql."<br>";
        pmysql_query($sql);
    }
    msg("적용되었습니다.");
}
?>

<link rel="stylesheet" href="style.css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">
    $(document).ready(function(){

        //개별 상세정보 별도표기
        $(document).on( 'click', 'input[name="option_chk"]', function( e ){
            var chk_index = $('input[name="option_chk"]').index( e.target );
            if( $(this).prop( 'checked' ) ){
                $('input[name="jungbo_prop_val"]').eq(chk_index).val( '상세정보 별도표기' );
                $('input[name="jungbo_prop_val"]').eq(chk_index).attr('readOnly','true');
            } else {
                $('input[name="jungbo_prop_val"]').eq(chk_index).val( '' );
                $('input[name="jungbo_prop_val"]').eq(chk_index).removeAttr('readOnly');
            }
        });
        //전체 상세정보 별도표기
        $(document).on( 'click', '#jungbo_allchk', function( e ){
            if( $(this).prop( 'checked' ) ){
                $('input[name="option_chk"]').each( function( i, obj ){
                    $(this).prop( 'checked', true );
                    $('input[name="jungbo_prop_val"]').eq(i).val( '상세정보 별도표기' );
                    $('input[name="jungbo_prop_val"]').eq(i).attr('readOnly','true');
                });
            } else {
                $('input[name="option_chk"]').each( function( i, obj ){
                    $(this).prop( 'checked', false );
                    $('input[name="jungbo_prop_val"]').eq(i).val( '' );
                    $('input[name="jungbo_prop_val"]').eq(i).removeAttr('readOnly');
                });
            }
        });
    });

    // 마진율 % 로 적용
    function btn_submit(mode) {

        if(mode == "add" || mode == "update") {
            var keyword = $("#keyword").val().trim();

            if ( keyword == "" ) {
                alert("TAG를 입력해주세요.");
                $("#keyword").val("").focus();
                return;
            }

            if(mode == "add") {
                if(!confirm("기존 태그에 일괄적으로 추가됩니다.진행하시겠습니까?")) {
                    return;
                }
            }

            if(mode == "update") {
                if(!confirm("기존 태그가 일괄적으로 수정됩니다.진행하시겠습니까?")) {
                    return;
                }
            }

            document.form1.keyword.value = keyword;
        }

        if(mode == "category") {
            //console.log(document.form1.code.value);
            if( document.form1.code.value == "" ) {
                alert("변경할 카테고리를 선택해 주십시오");
                return;
            }
        }

        if(mode == "jungbo") {

            console.log($("input[name='jungbo_prop_option']"));
            if( jQuery.type( $("input[name='jungbo_prop_option']") ) !== "undefined"  ){
                var prop_opt_val = $("#jungbo_option").val();
                $("input[name='jungbo_prop_option']").each(function( i, obj ){
                    prop_opt_val += "||" + $(this).val();
                });
                var prop_val = $("#jungbo_option").val();
                $("input[name='jungbo_prop_val']").each(function( i, obj ){
                    prop_val += "||" + $(this).val();
                });
                document.form1.sabangnet_prop_option.value = prop_opt_val;
                document.form1.sabangnet_prop_val.value = prop_val;
            }
        }
        document.form1.mode.value = mode;
        document.form1.submit();
    }

    function exec_add()
    {

        var ret;
        var str = new Array();
        var code_a=document.form1.code_a.value;
        var code_b=document.form1.code_b.value;
        var code_c=document.form1.code_c.value;
        var code_d=document.form1.code_d.value;

        if(!code_a) code_a="000";
        if(!code_b) code_b="000";
        if(!code_c) code_c="000";
        if(!code_d) code_d="000";
        sumcode=code_a+code_b+code_c+code_d;
        $.ajax({
            type: "POST",
            url: "product_register.ajax.php",
            data: "code_a="+code_a+"&code_b="+code_b+"&code_c="+code_c+"&code_d="+code_d
        }).done(function(msg) {
            if(msg=='nocate'){
                alert("상품카테고리 선택이 잘못되었습니다.");
        //		$("#catenm").html(msg);

            }else if(msg=='nolowcate'){
                alert("하위카테고리가 존재합니다.");
            //	$("#catenm").html("상품카테고리 선택이 잘못되었습니다.");
            }else{
                document.form1.code.value=sumcode;
                var code_a=document.getElementById("code_a");
                var code_b=document.getElementById("code_b");
                var code_c=document.getElementById("code_c");
                var code_d=document.getElementById("code_d");

                if(code_a.value){
                    str[0]=code_a.options[code_a.selectedIndex].text;
                }
                if(code_b.value){
                    str[1]=code_b.options[code_b.selectedIndex].text;
                }
                if(code_c.value){
                    str[2]=code_c.options[code_c.selectedIndex].text;
                }
                if(code_d.value){
                    str[3]=code_d.options[code_d.selectedIndex].text;
                }
                var obj = document.getElementById('Category_table');
                oTr = obj.insertRow();

                oTd = oTr.insertCell(0);
                oTd.id = "cate_name";
                oTd.innerHTML = str.join(" > ");
                oTd = oTr.insertCell(1);
                oTd.innerHTML = "\
                <input type=text name=category[] value='" + sumcode + "' style='display:none'>\
                ";
                oTd = oTr.insertCell(2);
                oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='img/btn/btn_cate_del01.gif' align=absmiddle></a>";
            }
        });
    }

    function cate_del(el) {
        console.log(el);
        idx = el.rowIndex;
        var obj = document.getElementById('Category_table');
        obj.deleteRow(idx);
    }
</script>
<!-- 라인맵 -->

    <form name="form1" id="form1" action="<?=$_SERVER['PHP_SELF']?>" method="POST"> 
	<table cellpadding="10" cellspacing="0" width="100%" style="table-layout:fixed" border=0>
    <tr><td height="20"></td></tr>
	<tr>
		<td>
			<div class="table_style02">
				<table width=98% cellpadding=1 cellspacing=1 border=0 style="border-collapse:collapse; border:1px gray solid;">
					<colgroup>
						<col width="20%" />
						<col width="auto" />
                        <col width="80" />
                        <col width="80" />
					</colgroup>
					<tr>
						<th>항목</th>
						<th>내용</th>
                        <th>추가</th>
                        <th>수정</th>
					</tr>
					<tr>
						<td style="border:1px gray solid;">TAG</td>
						<td style="border:1px gray solid; text-align:left; padding-left:5px;">
                            <input type="text" name="keyword" id="keyword" class="input_bd_st01" onkeydown="chkFieldMaxLen(100)" size="80" maxlength="100" /> 
                        </td>
						<td style="border:1px gray solid;">
                            <a href="javascript:btn_submit('add');"><img src="images/btn_add1.gif" border="0"></a>
                        </td>
						<td style="border:1px gray solid;">
                            <a href="javascript:btn_submit('update');"><img src="images/btn_edit.gif" border="0"></a>
                        </td>
					</tr>
				</table>
			</div>
		</td>
	</tr>

    <!-- 카테고리 영역 S -->
<?
#---------------------------------------------------------------
# 카테고리 리스트 script 작성
#---------------------------------------------------------------

$sql = "SELECT code_a, code_b, code_c, code_d, type, code_name FROM tblproductcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
$i=0;
$ii=0;
$iii=0;
$iiii=0;
$strcodelist = "";
$strcodelist.= "<script>\n";
$result = pmysql_query($sql,get_db_conn());
$selcode_name="";

while($row=pmysql_fetch_object($result)) {
	$strcodelist.= "var clist=new CodeList();\n";
	$strcodelist.= "clist.code_a='{$row->code_a}';\n";
	$strcodelist.= "clist.code_b='{$row->code_b}';\n";
	$strcodelist.= "clist.code_c='{$row->code_c}';\n";
	$strcodelist.= "clist.code_d='{$row->code_d}';\n";
	$strcodelist.= "clist.type='{$row->type}';\n";
	$strcodelist.= "clist.code_name='{$row->code_name}';\n";
	if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
		$strcodelist.= "lista[{$i}]=clist;\n";
		$i++;
	}
	if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
		if ($row->code_c=="000" && $row->code_d=="000") {
			$strcodelist.= "listb[{$ii}]=clist;\n";
			$ii++;
		} else if ($row->code_d=="000") {
			$strcodelist.= "listc[{$iii}]=clist;\n";
			$iii++;
		} else if ($row->code_d!="000") {
			$strcodelist.= "listd[{$iiii}]=clist;\n";
			$iiii++;
		}
	}
	$strcodelist.= "clist=null;\n\n";
}
pmysql_free_result($result);
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";


$codeA_list = "<select name=code_a id=code_a style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,1)\" {$disabled} Multiple>\n";
$codeA_list.= "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
$codeA_list.= "</select>\n";

$codeB_list = "<select name=code_b id=code_b style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,2)\" {$disabled} Multiple>\n";
$codeB_list.= "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
$codeB_list.= "</select>\n";

$codeC_list = "<select name=code_c id=code_c style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,3)\" {$disabled} Multiple>\n";
$codeC_list.= "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
$codeC_list.= "</select>\n";

$codeD_list = "<select name=code_d id=code_d style=\"width:150px; height:150px\" {$disabled} Multiple>\n";
$codeD_list.= "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
$codeD_list.= "</select>\n";

$codeSelect = "<span style=\"display:\" name=\"changebutton\"><input type=\"button\" value=\"선택\" style=\"height : 20px;\" onclick=\"javascript:exec_add()\"></span>";

// 스크립트 작성완료
?>
    <!-- <tr><td height=3></td></tr> -->
    <tr>
        <td>
        <?
            //if(!$code && !$changecode){
                 $classname="class=\"graybg_wrap\"";
                 $graybgdisplay="style=\"display:block\"";
            //}else{
                //$graybgdisplay="style=\"display:none\"";
            //}

        ?>
        <div <?=$classname?>>
        <div class="table_style01">

        <table cellSpacing=0 cellPadding=0 width="98%" border=0 style="table-layout:fixed">
        <tr><th colspan="4" height=25><font color='#FF0000' > * 기존 카테고리 정보가 선택하신 카테고리 정보로 일괄 변경됩니다.</font></th></tr>
        <tr>
            <th><span>카테고리 선택</span> <font color='#FF0000' > *필수 </font> </th>
            <td colspan="3">
<?php
//카테고리 SELECT BOX를 불러온다
echo $codeA_list;
echo $codeB_list;
echo $codeC_list;
echo $codeD_list;
//카테고리 SELECT 버튼을 불러온다
echo $codeSelect;
//카테고리 스크립트 실행
echo $strcodelist;
echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
?>
            </td>
        </tr>
        </table>
        </div>
        </div><!-- 그레이배경 div -->
        </td>
    </tr>

    <tr>
		<td>
			<div class="table_style01">

			<table width=98% cellpadding=0 cellspacing=1 border=1 style="border-collapse:collapse">
			<tr>
                <th height=30><span>카테고리</span></th>
                <td>
                <div class="table_none">
                <table width=100% cellpadding=0 cellspacing=1 id=Category_table>
                    <col><col width=50 style="padding-right:10"><col width=52 align=right>
<?
	//해당 상품의 카테고리 리스트 가져오기
	if($cate_array){
		foreach($cate_array as $v=>$k){
?>
                    <tr>
                        <td id=cate_name><?=$k[c_codename]?></td>
                        <td>
                            <input type=text name=category[] value="<?=$k[c_category]?>" style="display:none">
                        </td>
                        <td>
                            <!--<img src="../img/i_select.gif" border=0 onClick="cate_mod(document.forms[0]['cate[]'][0],this.parentNode.parentNode)" class=hand>-->
                            <a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="img/btn/btn_cate_del01.gif" border=0 align=absmiddle></a>
                        </td>
                    </tr>
<?
		}
	}
?>

                </table>
			    </div>
			    </td>
			</tr>
			</table>
			</div>
		</td>
	</tr>
    <!-- 카테고리 영역 E -->


    <tr>
        <td align=center>
            <a href="javascript:btn_submit('category');"><img src="images/btn_product_reg_all.gif" border="0"></a>
        </td>
    </tr>

<?
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다
?>
    <!-- 정보고시 페이지 수정 2016 01 18 유동혁 -->
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td>
                        <div class="title_depth3">정보 고시 등록/수정</div>		
                        <input type="hidden" name="sabangnet_prop_val">
                        <input type="hidden" name="sabangnet_prop_option">
                        <!-- <input type="hidden" name="prop_type" value="001" /> -->
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="table_style01" id="prop_type001">
                            <table cellSpacing=0 cellPadding=0 width="100%" border=0>
                                <tr>
                                    <th>
                                        <span>상품의 상품군</span> <font color='#0099BF' > 선택</font>
                                    </th>
                                    <td>
                                        <select id='jungbo_option' name='jungbo_option' >
<?php
echo "<option value='025' selected >INFO</option>";
/* deco는 info만 볼수엤게 한다
foreach( $jungbo_code as $codeKey=>$codeVal ){
$jungbo_selected = '';
if( $codeKey == $sabangnet_prop_option[0] ) $jungbo_selected = 'selected';
echo "<option value='".$codeKey."' ".$jungbo_selected." >".$codeVal['title']."</option>";
}
*/
?>
                                        </select>
                                        <input type='checkbox' id='jungbo_allchk' value='' > <span class='font_orange'>* 모든 상품정보 '상세정보 별도표기' 선택</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="table_style01" id="prop_type001">
                            <table cellSpacing=0 cellPadding=0 width="100%" border=0 id='jungbo_options'>
<?php
//$incode = $jungbo_code[$sabangnet_prop_option[0]];
# info 만 사용하도록 변경 2016-03-07
$incode = $jungbo_code["001"];
$optionKey = 1;
if( $incode ){
    foreach( $incode['option'] as $inKey=>$inVal ){

?>
                                <tr>
                                    <th>
                                        <span><?=$inVal?></span>
                                        <input type='hidden' name='jungbo_prop_option' id='' value='<?=$inVal?>' >
                                    </th>
                                    <td>
                                        <input type='text' name='jungbo_prop_val' id='' style="width:450px;" >
                                        <input type='checkbox' name='option_chk' > 상세정보 별도표기
                                        <br><span class='font_blue' ><?=$incode['comment'][$inKey]?></span>
                                    </td>
                                </tr>
<?php
        $optionKey++;
    }
}
?>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- //정보고시 페이지 수정 2016 01 18 유동혁 -->
    <tr>
        <td align=center>
            <a href="javascript:btn_submit('jungbo');"><img src="images/btn_product_reg_all.gif" border="0"></a>
        </td>
    </tr>


	</table>

<!-- <form name="form1" id="form1" action="<?=$_SERVER['PHP_SELF']?>" method="POST">  -->
    <input type="hidden" name="mode" >
    <!-- <input type="hidden" name="keyword" > -->

    <?php foreach ( $arrProdList as $prodCode ) { ?>
        <input type="hidden" name="prod_list[]" value="<?=$prodCode?>">
    <?php } ?>

    <input type=hidden name=code id=code value="<?=$code?>">

</form>

