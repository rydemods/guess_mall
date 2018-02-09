<?php
/********************************************************************* 
// 파 일 명		: design_studio_write.php
// 설     명	: STUDIO 생성, 수정, 삭제
// 상세설명	    : STUDIO 생성, 수정, 삭제
// 작 성 자		: 2016.01.22 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");
	# 파일 클래스 추가
	include_once($Dir."lib/file.class.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "de-5";
	$MenuCode = "member";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

	include("header.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------

    $tb = $_GET["tb"] ?: $_POST["tb"];
	$mode=$_POST["mode"];
	if(!$mode) $mode=$_GET["mode"];

    if ( $tb == "press" ) {
        $view_checkbox = true;
    }

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/{$tb}/";
	// 이미지 파일
	$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

	$lbno	            = $_POST["lbno"];

	if($mode=="delete") {
		$qry = "DELETE FROM tbl{$tb} WHERE no ='".trim($lbno)."'";
		pmysql_query( $qry, get_db_conn() );
		echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.goBackList();\"></body></html>";exit;

	} else if($mode=="insert" || $mode=="modify") {				// DB를 수정한다.
		
		$title	            = pg_escape_string($_POST["title"]);
		$subtitle	        = pg_escape_string($_POST["subtitle"]);
		$hidden	            = $_POST["hidden"];
		$is_gnb             = $_POST["is_gnb"];
		if (!$hidden) $hidden = 0;
		$v_up_imagefile	    = $_POST["v_up_imagefile"];
		
		$sort	            = $_POST["sort"];
		$lbcno	            = $_POST["lbcno"];
		$v_up_imagefile2	= $_POST["v_up_imagefile2"];
		$places	            = $_POST["places"];
        $productcodes       = $_POST['relationProduct'];

        $v_sort             = $_POST['v_sort'] ?: 0;

		$up_imagefile=$imagefile->upFiles();

		//exdebug((count($_POST[productcodes])/5));
		//exdebug($_POST);
		//exit;

		$s_cnt	= 0;
		foreach($v_up_imagefile2 as $key=>$value){ 
			if (($key%2) == 0 && $key != 0) $s_cnt++;
			$s_v_up_imagefile2[$s_cnt][] = $value;
			$s_up_imagefile2[$s_cnt][] = $up_imagefile["up_imagefile2"][$key]["v_file"];
			//exdebug($up_imagefile["up_imagefile2"][$key]["v_file"]);
		}

        $arrProductCode = array();
		foreach($productcodes as $key=>$value){ 
            array_push($arrProductCode, $value);
		}
        $productcodes = implode("||", $arrProductCode);

		//exdebug($places);
		//exdebug($s_lbcno);
		//exit;
		//exdebug($productcodes);

		$regdate = date("YmdHis");

		if($mode=="insert") {
			$sql = "INSERT INTO tbl{$tb} (
			title		,
			subtitle		,
			img	,
			img_m	,
			hidden		,
			is_gnb      ,
			regdate,
            sort,
            productcodes ) VALUES (
			'{$title}', 
			'{$subtitle}', 
			'".$up_imagefile["up_imagefile"][0]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][1]["v_file"]."', 
			'{$hidden}', 
			'{$is_gnb}', 
			'{$regdate}',
			{$v_sort},
            '{$productcodes}') RETURNING no";
			$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
			$lbno = $row2[0];

//            $sql = str_replace(array("\r", "\n", "\t"), "", $sql);
//            trigger_error($sql, E_USER_ERROR);

			echo "<html></head><body onload=\"alert('등록이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;

		}else if($mode=="modify") {

			$img_where="";
			$img_where[] = "title='{$title}' ";
			$img_where[] = "subtitle='{$subtitle}' ";
			$img_where[] = "hidden='{$hidden}' ";
			$img_where[] = "is_gnb='{$is_gnb}' ";
			$img_where[] = "productcodes='{$productcodes}' ";
            if ( $tb == "press" ) {
    			$img_where[] = "sort={$v_sort} ";
            }

			for($u=0;$u<2;$u++) {
				if( strlen( $up_imagefile["up_imagefile"][$u]["v_file"] ) > 0 ){
					if( is_file( $imagepath.$v_up_imagefile[$u] ) > 0 ){
						$imagefile->removeFile( $v_up_imagefile[$u] );
					}
					if ($u == 0) $img_where[] = "img='".$up_imagefile["up_imagefile"][0]["v_file"]."'";
					if ($u == 1) $img_where[] = "img_m='".$up_imagefile["up_imagefile"][1]["v_file"]."'";
				}
			}				

			$sql = "UPDATE tbl{$tb} SET ";
			$sql.= implode(", ",$img_where);
			$sql.= "WHERE no='{$lbno}' ";	

			pmysql_query($sql,get_db_conn());
			
			echo "<html></head><body onload=\"alert('수정이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;
		}
	}

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	
# 수정할 배너 불러오기
if( $mode == 'modfiy_select' ){
	$lbno = $_POST['lbno'];
	if(!$lbno) $lbno = $_GET['lbno'];
	$lbSelectSql = "SELECT * FROM tbl{$tb} WHERE no ='".trim($lbno)."' ";
	$lbSelectRes = pmysql_query( $lbSelectSql, get_db_conn() );
	$lbSelectRow = pmysql_fetch_array( $lbSelectRes );
	$mSelect = $lbSelectRow;
	pmysql_free_result( $lbSelectRes );

    $arrProductCodes = explode("||", $mSelect['productcodes']);

    $order_idx = 0;
    $arrProductOrder = array();
    $arrWhereProductCode = array();
    foreach($arrProductCodes as $key=>$value){
        $arrProductOrder[$value] = $order_idx;              // 상품별 순서
        array_push($arrWhereProductCode, "'{$value}'");     // 상품정보 조회시 필요한 where절용
        $order_idx++;
    }

    // 상품 정보를 조회한다.
    $sql    = "SELECT * FROM tblProduct WHERE productcode in ( " . implode(",", $arrWhereProductCode) . " ) ";
    $result = pmysql_query($sql);

    $arrProduct = array();
    while ($row = pmysql_fetch_array($result)) {
        if ( isset($arrProductOrder[$row['productcode']]) ) {
            $arrProduct[$arrProductOrder[$row['productcode']]] = $row;
        }
    }

	//수정
	$qType = '1';
	$qType_text = '수정';
}

# 등록 mode 
if( is_null( $qType ) ){
	$qType = '0';
	$qType_text = '등록';
}

#노출 기본 세팅
$display['0'] = '비노출';
$display['1'] = '노출';
?>

<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(mode, lbno) {

	$(".procode").val("");
	$(".prList").each(function(){
		var num	= $(this).attr("alt");
		$(this).find(".relationProduct"+num).each(function(){
			$(".productcodes" + num).val($(this).val()); 
		});
	});

	if( mode == '0' ){

		if( document.form1.title.value == '' ){
			alert('제목을 입력해야 합니다.');
			return;
		}			
		if( confirm('등록하시겠습니까?') ){
			document.form1.mode.value="insert";
			document.form1.target="processFrame";
			document.form1.submit();
		} else {
			return;
		}
	} else if ( mode == '1' ) {
		if( document.form1.title.value == '' ){
			alert('제목을 입력해야 합니다.');
			return;
		}

		if( confirm('수정하시겠습니까?') ){
            <?php if ( $tb == "press" ) { ?>
			if( $('#v_sort').val().trim() == '' || $('#v_sort').val() < 0 ){
				alert('노출순서를 입력해야 합니다.');
                $('#v_sort').val('').focus();
				return;
			}
            <?php } ?>

			document.form1.mode.value="modify";
			document.form1.target="processFrame";
			document.form1.submit();
		} else {
			return;
		}
	} else if ( mode == '2' ) {
		document.form1.lbno.value=lbno;
		document.form1.mode.value="modfiy_select";
		document.form1.submit();
	} else if ( mode == '3' ) {
		if( confirm('삭제하시겠습니까?') ){
			document.form1.lbno.value=lbno;
			document.form1.mode.value="delete";
			document.form1.target="processFrame";
			document.form1.submit();
		} else {
			return;
		}
	} else {
		alert('잘못된 입력입니다.');
		return;
	}
}

function goBackList(){
	location.href="design_studio_list.php?tb=<?=$tb?>";
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; <?=strtoupper($tb)?>정보관리 &gt;<span><?=strtoupper($tb)?> <?=$qType_text?></span></p></div></div>
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
			<?php include("menu_design.php"); ?>
			</td>
			<td></td>
			<td valign="top">	
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=tb value="<?=$tb?>">
			<input type=hidden name=mode>
			<input type="hidden" name="itemCount">
			<input type=hidden name=lbno value="<?=$lbno?>">		
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3"><?=strtoupper($tb)?> <?=$qType_text?></div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span><?=strtoupper($tb)?>을 <?=$qType_text?>할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><?=strtoupper($tb)?> 기본정보</div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<?include("layer_prlistPop.php");?>
				<div class="table_style01">					
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>제목</span></th>
					<TD><INPUT maxLength=80 size=80 id='title' name='title' value="<?=$mSelect['title']?>"></TD>
				</tr>
				<tr>
					<th><span>설명 텍스트</span></th>
					<TD><INPUT maxLength=80 size=80 id='subtitle' name='subtitle' value="<?=$mSelect['subtitle']?>" ></TD>
				</tr>
				<tr>
					<th><span>이미지(PC)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile[0]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[0]" value="<?=$mSelect['img']?>" >
<?	if( is_file($imagepath.$mSelect['img']) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$mSelect['img']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>이미지(MOBILE)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile[1]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[1]" value="<?=$mSelect['img_m']?>" >
<?	if( is_file($imagepath.$mSelect['img_m']) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$mSelect['img_m']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
                <tr>
					<th><span>관련상품</span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','relationProduct');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
                    <td align="left">
                            <div style="margin-top:0px; margin-bottom: 0px;">                           
                                <table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_relationProduct">    
                                <input type="hidden" name="limit_relationProduct" id="limit_relationProduct" value=""/>                             
                                    <colgroup>
                                        <col width=20></col>
                                        <col width=50></col>
                                        <col width=></col>
                                    </colgroup>

                                        <? for($i = 0; $i < count($arrProduct); $i++ ) { ?>
                                            <tr align="center">
                                                <td style='border:0px'>
                                                    <a name="pro_upChange" style="cursor: hand;">
                                                        <img src="images/btn_plus.gif" border="0" style="margin-bottom: 3px;" />
                                                    </a>
                                                    <br>
                                                    <a name="pro_downChange" style="cursor: hand;">
                                                        <img src="images/btn_minus.gif" border="0" style="margin-top: 3px;" />
                                                    </a>
                                                </td>
                                                <td style='border:0px'>
                                                    <img style="width: 40px; height:40px;" src="<?=getProductImage($Dir.DataDir."shopimages/product/", $arrProduct[$i]['tinyimage'])?>" border="1"/>
                                                    <input type='hidden' name='relationProduct[]' value='<?=$arrProduct[$i][productcode]?>'>
                                                </td>
                                                <td style='border:0px' align="left"><?=$arrProduct[$i][productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$arrProduct[$i][productcode]?>','relationProduct');" border="0" style="cursor: hand;vertical-align:middle;" />
                                                </td>
                                            </tr>
                                        <?}?>


                                </table>
                            </div>
                    </td>

                </tr>

                <? if ( $view_checkbox ) { ?>
				<tr>
					<th><span>GNB 노출</span></th>
					<TD><INPUT type='checkbox' id='is_gnb' name='is_gnb' value="1" <? if( $mSelect['is_gnb'] == '1' ) { echo "CHECKED"; } ?> > * 체크시 노출됩니다. </TD>
				</tr>
                <? } ?>

                <?php if ( $tb == "press" ) { ?>
                <tr>
                    <th><span>노출순서</span></th>
                    <TD><INPUT maxLength=10 size=10 id='v_sort' name='v_sort' value="<?=$mSelect['sort']?>" ></TD>
                </tr>
                <?php } ?>

				<tr>
					<th><span>노출</span></th>
					<TD><INPUT type='checkbox' id='hidden' name='hidden' value="1" <? if( $mSelect['hidden'] == '1' ) { echo "CHECKED"; } ?> > * 체크시 노출됩니다. </TD>
				</tr>
				</table>
				</div>
				</td>
			</tr>

			<tr>
				<td>
				<div class="table_style01 lookbooktable" style='padding-bottom:0px'>	
<?php
	if( $qType == '0' ){
?>
<?php
	} else {
?>			
<?php
		//exdebug($mConSelect);
		$sj	= 0;
		foreach( $mConSelect as $mKey=>$mVal ){
			//echo $mKey."=>".$mVal."<br>";
			//echo $mVal['sort'];
			$mVal_place			= explode("|",$mVal['places']);
			$mVal_productcode	= explode("|",$mVal['productcodes']);
?>	
				<table name="lookbooktable" cellpadding=0 cellspacing=0 border=0 width=100% class="item<?=($mKey+1)?>">
				<col width=140></col>
				<col width=400></col>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>영역 우선순위</span></th>
					<td colspan="<?if ($mKey>0){echo '2';} else {echo '3';} ?>"><INPUT maxLength=20 size=20 name='sort[<?=$mKey?>]' value="<?=$mVal['sort']?>"><INPUT type='hidden' name='lbcno[<?=$mKey?>]' value="<?=$mVal['no']?>"></td>
					<?if ($mKey>0){?><td style='border-left:1px solid #fff;text-align:right;'><img src="images/btn_del6.gif"  id="tr_del" border="0" style="cursor:pointer;"></td><?}?>
				</tr> 
				<tr>
					<th><span>이미지1(PC)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile2[]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile2[]" value="<?=$mVal['img']?>" >
						<div style='margin-top:5px' >
						<?	if( is_file($imagepath.$mVal['img']) ){ ?>
							<img src='<?=$imagepath.$mVal['img']?>' style='max-width: 125px;' />
						<?	} ?>
						</div>
					</td>
					<th><span>이미지1(MOBILE)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile2[]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile2[]" value="<?=$mVal['img_m']?>" >
						<div style='margin-top:5px' >
						<?	if( is_file($imagepath.$mVal['img_m']) ){ ?>
							<img src='<?=$imagepath.$mVal['img_m']?>' style='max-width: 125px;' />
						<?	} ?>
						</div>
					</td>
				</tr>
		<?
			for($j=0;$j<5;$j++){	
				$bProductSql = "SELECT * FROM tblproduct WHERE productcode= '".trim($mVal_productcode[$j])."'";
				$bProductResult = pmysql_query($bProductSql,get_db_conn());
				$bProductRow = pmysql_fetch_array($bProductResult);
		?>
				<TR>
					<th><span>좌표(PC)</span></th>
					<TD class="td_con1"><INPUT maxLength=60 size=60 name='places[<?=$sj?>]' value="<?=$mVal_place[$j]?>"><input type="hidden" name="productcodes[<?=$sj?>]" class='procode productcodes<?=$sj?>' value="<?=$mVal_productcode[$j]?>"></TD>
					<th><span>관련상품</span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','relationProduct<?=$sj?>');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;height:50px">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" class="prList" id="check_relationProduct<?=$sj?>" alt='<?=$sj?>'>	
								<input type="hidden" name="limit_relationProduct<?=$sj?>" id="limit_relationProduct<?=$sj?>" value="1"/>								
									<colgroup>
										<col width=50></col>
										<col width=></col>
									</colgroup>
								<?if($bProductRow){?>	
									<tr align="center">
										<td style='border:0px'>
											<img style="width: 40px; height:40px;" src="<?=$Dir.DataDir."shopimages/product/".$bProductRow['tinyimage']?>" border="1"/>
											<input type='hidden' name='relationProduct<?=$sj?>[]' class='relationProduct<?=$sj?>' value='<?=$bProductRow[productcode]?>'>
										</td>
										<td style='border:0px' align="left"><?=$bProductRow[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bProductRow[productcode]?>','relationProduct<?=$sj?>');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								<?}?>
								</table>
							</div>
					</td>
				</TR>
		<?
				pmysql_free_result( $bProductResult );
				$sj++;
			}
		?>
				</table>
<?php
		}
	}
?>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan=8 align=center>
<?php
	if( $qType == '0' ){
?>
					<a href="javascript:CheckForm('<?=$qType?>', '<?=$mSelect['no']?>' );"><img src="img/btn/btn_input02.gif" alt="등록하기"></a>
<?php
	} else {
?>
					<a href="javascript:CheckForm('<?=$qType?>', '<?=$mSelect['no']?>' );"><img src="images/btn_edit2.gif" alt="수정하기"></a>
					<a href="javascript:CheckForm('3', '<?=$mSelect['no']?>' );"><img src="images/botteon_del.gif" alt="삭제하기"></a>
<?php
	}
?>
					<a href="javascript:goBackList();"><img src="img/btn/btn_list.gif" alt="목록보기"></a>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span><?=strtoupper($tb)?> <?=$qType_text?></span></dt>
							<dd>- <?=strtoupper($tb)?>을 <?=$qType_text?>할 수 있습니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<script language="javascript">
$(document).ready(function(){
	$("#tr_add").click(function(){	
		var lastItemNo = $(".lookbooktable [name=lookbooktable]:last").attr("class").replace("item", "");
		document.form1.itemCount.value = lastItemNo;
		if(lastItemNo <=20){
			//var newItem = $(".lookbooktable [name=lookbooktable]:last").clone();
					
			var newItem = "<table name=\"lookbooktable\" cellpadding=0 cellspacing=0 border=0 width=100% class=\""+"item"+(parseInt(lastItemNo)+1)+"\"><col width=140></col><col width=400></col><col width=140></col><col width=></col><tr><th><span>영역 우선순위</span></th><td colspan=2><INPUT maxLength=20 size=20 name='sort["+lastItemNo+"]' value=\""+(parseInt(lastItemNo)+1)+"\"><INPUT type='hidden' name='lbcno["+lastItemNo+"]' value=''></td><td style='border-left:1px solid #fff;text-align:right;'><img src=\"images/btn_del6.gif\"  id=\"tr_del\" border=\"0\" style=\"cursor:pointer;\"></td></tr> <tr><th><span>이미지1(PC)</span></th><td class=\"td_con1\" style=\"position:relative\"><input type=file name=\"up_imagefile2[]\" style=\"WIDTH: 400px\"><br><input type=hidden name=\"v_up_imagefile2[]\" value=\"\" ></td><th><span>이미지1(MOBILE)</span></th><td class=\"td_con1\" style=\"position:relative\"><input type=file name=\"up_imagefile2[]\" style=\"WIDTH: 400px\"><br><input type=hidden name=\"v_up_imagefile2[]\" value=\"\" ></td></tr>";
			var sj= parseInt(lastItemNo) * 5;
			for(var j=sj;j<(sj+5);j++){
				newItem += "<TR>";
				newItem += "	<th><span>좌표(PC)</span></th>";
				newItem += "	<TD class=\"td_con1\"><INPUT maxLength=60 size=60 name='places["+j+"]' value=\"\"><input type=\"hidden\" name=\"productcodes["+j+"]\" class='procode productcodes"+j+"'></TD>";
				newItem += "	<th><span>관련상품</span>&nbsp;&nbsp;<a href=\"javascript:T_layer_open('layer_product_sel','relationProduct"+j+"');\"><img src=\"./images/btn_search2.gif\" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>";
				newItem += "	<td align=\"left\">";
				newItem += "			<div style=\"margin-top:0px; margin-bottom: 0px;height:50px\">		";					
				newItem += "				<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name=\"prList\" class=\"prList\" id=\"check_relationProduct"+j+"\" alt=\""+j+"\">	";
				newItem += "				<input type=\"hidden\" name=\"limit_relationProduct"+j+"\" id=\"limit_relationProduct"+j+"\" value=\"1\"/>		";						
				newItem += "					<colgroup>";
				newItem += "						<col width=50></col>";
				newItem += "						<col width=></col>";
				newItem += "					</colgroup>";
				newItem += "				</table>";
				newItem += "			</div>";
				newItem += "	</td>";
				newItem += "</TR>";
			}
			$('.lookbooktable').append(newItem); 					
			
		}else{ 
			alert("20개까지 등록할 수 있습니다.");
			return;   
		}
	}); 
});
$(document).on('click', '#tr_del',function(e){
	if(confirm('삭제하시겠습니까?')){
		$(this).parent().parent().parent().parent().remove();
	}
});
</script>
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
