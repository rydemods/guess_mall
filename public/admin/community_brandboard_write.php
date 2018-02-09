<?php // hspark

if($board_num && $brandBoardMode=='modify'){
	$thisBoard = brandBoardView($board_num);
	//$thisBoard = $thisBoardArray[0];
	$thisBosrdItem = brandBoardItem($board_num);
}

?>

<style type="text/css">
	.layer {display:none; position:fixed; _position:absolute; top:0; left:0; width:100%; height:100%; z-index:100;}
	.layer .bg {position:absolute; top:0; left:0; width:100%; height:100%; background:#000; opacity:.5; filter:alpha(opacity=50);}
	.layer .pop-layer {display:block;}

	.pop-layer {display:none; position: absolute; top: 50%; left: 50%; width: 900px; height:500px;  background-color:#fff; border: 5px solid #3571B5; z-index: 10; overflow-y: scroll;}	
	.pop-layer .pop-container {padding: 20px 25px;}
	.pop-layer p.ctxt {color: #666; line-height: 25px;}
	.pop-layer .btn-r {
			/*width: 100%; margin:10px 0 20px; padding-top: 10px; border-top: 1px solid #DDD; text-align:right;*/
			position: fixed; margin-left: 843px; margin-top: -35;
	}

	a.cbtn {display:inline-block; height:25px; padding:0 14px 0; border:1px solid #304a8a; background-color:#3f5a9d; font-size:13px; color:#fff; line-height:25px;}	
	a.cbtn:hover {border: 1px solid #091940; background-color:#1f326a; color:#fff;}
	
	
	li.prListOn { position:relative; float:left; margin-right:15px; margin-bottom:5px; width:100px; height: 150px;}
	li.prListOn:before {display:block; width:1px; height:100%; content:""; background:#dbdbdb; position:absolute; top:0px; left:105px;}
</style>
<form name='writeForm' id='writeForm' method='post' action='community_brandboard_indb.php' enctype='multipart/form-data'>
<input type="hidden" name="brandBoardMode" value="<?=$brandBoardMode?>">
<input type="hidden" name="this_boardCode" value="<?=$thisBoard[board_code]?>">
<input type="hidden" name="board_num" value="<?=$board_num?>"/>
<input type="hidden" name="mode" id="mode" value="N"/>
<input type="hidden" name="listMode" id="listMode" value=""/>
<input type="hidden" id="this_pageCode" value="<?=$thisBoard[page_code]?>" >
<div class="layer">
	<div class="bg"></div>
	<div id="layer2" class="pop-layer">
		<div class="btn-r">
			<a href="#" class="cbtn">Close</a>
		</div>
		<div class="pop-container">
			<div class="pop-conts">
				<!--content //-->
				<p class="ctxt mb20" style="font-size:15px; font-weight: 700;">상품 선택<br>
					<?=codeListScript()?><br>
					<div>
						<input type="text" name="s_keyword" id="s_keyword" value="" style="width: 250px;"/>
						<a href="javascript:productListSearch();"><img src="images/btn_search.gif" style="position: absolute; padding-left: 5px;"/></a>
					</div>
				</p>
				<div id="productList">
					
				</div>
				<!--// content-->
			</div>
		</div>
	</div>
</div>
<div class="table_style01">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR <? if ( $thisBoard[board_code] && ( $thisBoard[board_code] != '2' && $thisBoard[board_code] != '4' ) ) { echo "style='display:none;'"; } ?> >
			<TH> <span>게시판 목록 :</span></TH>
            <TD class="font_size">
                <select name="board_code" id="board_code" class="select" >
                <?foreach($brandBoardCategory as $bKey=>$bVal){?>
                	<? if ( $thisBoard[board_code] == '2' || $thisBoard[board_code] == '4' ) { ?>
                		<? if ( ( $bVal->board_code == '2' || $bVal->board_code == '4' ) ) { ?>
                	<option value="<?=$bVal->board_code?>" <?if($thisBoard[board_code] == $bVal->board_code) {echo "SELECTED";}?>><?=$bVal->board_name?></option>
                		<? } ?>
                	<? } else { ?>
                    <option value="<?=$bVal->board_code?>" <?if($thisBoard[board_code] == $bVal->board_code) {echo "SELECTED";}?>><?=$bVal->board_name?></option>
                    <? } ?>
                <?}?>
                </select>
            </TD>
        </TR>
        <TR id="ID_PageCategory">
        	<th><span>카테고리</span></th>
        	<TD class="font_size">
        		<select name="page_code" id="page_code" class="select">
        			<option value="">======</option>
        		</select>
        	</TD>
        </TR>
		<TR>
			<th><span>글제목</span></th>
			<TD class="td_con1" align="center">
				<p align="left">
					<INPUT maxLength=200 size=70 name=board_title value="<?=$thisBoard['board_title']?>" style="width:100%" class="input">
				</p>
			</TD>
		</TR>
		<TR id='ID_RepProductChange'>
			<th><span>대표상품</span></th>
			<td>
				<p align="left">
					<div style="margin-top:10px; margin-bottom: 10px;">
						<ul id="checkRepProduct" style="">
						<?if($thisBoard[productcode]){?>
							<li class='prListOn' id='RepProduct'>
								<img src='<?=$Dir.DataDir."shopimages/product/".$thisBoard['tinyimage']?>' style='width:100px' ><br>
								<a href='javascript:rapPrDel("<?=$thisBoard['productcode']?>");'><?=$thisBoard[productname]?></a>
							</li>
						<?}?>
						</ul>
					</div>
					<INPUT type='hidden' name='productcode' id="productcode" value="<?=$thisBoard['productcode']?>">
					<a href="javascript:layer_open('layer2','repProduct');"><img src="./images/btn_search2.gif"/></a>
				</p>
			</td>
		</TR>
		<TR id='ID_RelationProduct'>
			<th><span>관련상품</span></th>
			<td>
				<p align="left">
					<div style="margin-top:10px; margin-bottom: 10px;">
						<ul id="checkProduct" style="">
						<?if($thisBosrdItem){?>
							<?foreach($thisBosrdItem as $bosrdItemKey=>$bosrdItem){?>
								<li class='prListOn'>
								<input type='hidden' name='relationProduct[]' value='<?=$bosrdItem[productcode]?>'>
								<img src='<?=$Dir.DataDir."shopimages/product/".$bosrdItem['tinyimage']?>' style='width:100px' ><br>
								<a href='javascript:relationPrDel("<?=$bosrdItem[productcode]?>");'><?=$bosrdItem[productname]?></a>
								</li>
							<?}?>
						<?}?>
						</ul>
					</div>
					<a href="javascript:layer_open('layer2','relationProduct');"><img src="./images/btn_search2.gif"/></a>
				</p>
			</td>
		</TR>
		<TR>
			<th><span>글내용</span></th>
			<TD class="td_con1" width="627">
				<TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" id="ir1" name=content ><?=$thisBoard['board_content']?></TEXTAREA>
				<input type="hidden" name="board_content" id="board_content" >
			</TD>
		</TR>
		<tr id="ID_bigImage">
			<th><span>대표이미지</span></th>
			<td>
				<? if ($thisBoard[big_image]) { ?>
				<img src="<?=$Dir.DataDir."shopimages/brandboard/".$thisBoard[big_image]?>" style="width: 100px;">
				<br><font color="#008C5C" style="font-size:11px;letter-spacing:-0.5pt;">* <?=$thisBoard[big_image]?></font>
				<? } ?>
				<input type=file name="big_image[0]" style="width:80%" class=linebg onChange="preview(this.value,0)">
				<p style="color: orange; " id="ID_BigSize" ></p>
			</td>
		</tr>
		<tr id="ID_ThumbnailImage">
			<th><span>썸네일 이미지</span></th>
			<td>
				<? if ($thisBoard[thumbnail_image]) { ?>
				<img src="<?=$Dir.DataDir."shopimages/brandboard/".$thisBoard[thumbnail_image]?>" style="width: 100px;">
				<br><font color="#008C5C" style="font-size:11px;letter-spacing:-0.5pt;">* <?=$thisBoard[thumbnail_image]?></font>
				<? } ?>
				<input type=file name="thumbnail_image[0]" style="width:80%" class=linebg onChange="preview(this.value,0)">
				<p style="color: orange; " id="ID_ThumbnailSize" ></p>
			</td>
		</tr>
	</TABLE>
</div>

<div align=center>
	<?if($brandBoardMode == "write"){?>
	<img src="<?=$imgdir?>/butt-ok.gif" border=0 style="cursor:hand;" onclick="formCheck();"> &nbsp;&nbsp;
	<?}else if($brandBoardMode == "modify"){?>
	<img src="images/board/butt-modify.gif" border="0" style="cursor:hand;" onclick="modifyFormCheck();">
	<?}?>
	<IMG SRC="<?=$imgdir?>/butt-cancel.gif" border=0 style="CURSOR:hand" onClick="history.back();">
</div>
</form>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript">
var oEditors = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "ir1",
	sSkinURI: "../SE2/SmartEditor2Skin.html",
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	},
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

function formCheck(){
	$("#board_content").val(oEditors.getById["ir1"].getIR());
	$("#mode").val('insert');
	$("#writeForm").submit();
}
function modifyFormCheck(){
	$("#board_content").val(oEditors.getById["ir1"].getIR());
	$("#mode").val('modify');
	$("#writeForm").submit();
}

function layer_open(el,onMode){

	var temp = $('#' + el);
	var bg = temp.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수
	switch(onMode){
		case 'repProduct' :
			$('#listMode').val('repProduct');
			break;
		case 'relationProduct' :
			$('#listMode').val('relationProduct');
			break;
		default :
			$('#listMode').val('');
			break;
	}
	
	if(bg){
		$('.layer').fadeIn();	//'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
	}else{
		temp.fadeIn();
	}

	layerResize(el);

	temp.find('a.cbtn').click(function(e){
		if(bg){
			$('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
			outLayer();
		}else{
			temp.fadeOut();
			outLayer();
		}
		e.preventDefault();
	});

	$('.layer .bg').click(function(e){	//배경을 클릭하면 레이어를 사라지게 하는 이벤트 핸들러
		$('.layer').fadeOut();
		outLayer();
		e.preventDefault();
	});

}

function layerResize(el){
	var temp = $('#' + el);
	// 화면의 중앙에 레이어를 띄운다.
	if (temp.outerHeight() < $(document).height() ) temp.css('margin-top', '-'+temp.outerHeight()/2+'px');
	else temp.css('top', '0px');
	if (temp.outerWidth() < $(document).width() ) temp.css('margin-left', '-'+temp.outerWidth()/2+'px');
	else temp.css('left', '0px');
	
	//console.log(temp.outerHeight());
}

function outLayer(){
	$("#s_keyword").val("");
	$("#productList").html("");
	$('#listMode').val("");
	//$("#checkProduct").html("");
}

function productListSearch(){
	var code_a = $("#code_a").val();
	var code_b = $("#code_b").val();
	var code_c = $("#code_c").val();
	var code_d = $("#code_d").val();
	var s_keyword = $("#s_keyword").val();
	var listMode = $("#listMode").val();
	$.post(
		"community_brandboard_prlistPost.php",
		{
			code_a:code_a,
			code_b:code_b,
			code_c:code_c,
			code_d:code_d,
			s_keyword:s_keyword,
			listMode:listMode
		},
		function(data){
			$("#productList").html(data);
			layerResize('layer2');
		}
	);
}

function GoPage(block,gotopage){
	var code_a = $("#code_a").val();
	var code_b = $("#code_b").val();
	var code_c = $("#code_c").val();
	var code_d = $("#code_d").val();
	var s_keyword = $("#s_keyword").val();
	var listMode = $("#listMode").val();
	$.post(
		"community_brandboard_prlistPost.php",
		{
			code_a:code_a,
			code_b:code_b,
			code_c:code_c,
			code_d:code_d,
			listMode:listMode,
			s_keyword:s_keyword,
			block:block,
			gotopage:gotopage
		},
		function(data){
			$("#productList").html(data);
			layerResize('layer2');
		}
	);
}

function onProductcode(prname,prcode,primg){
	var appHtml = "";
	if(confirm('해당 상품을 대표상품으로 입력하시겠습니까?')){
		$("#productcode").val(prcode);
		appHtml = "<li class='prListOn' id='RepProduct'>";
		appHtml+= "<img src='"+primg+"' style='width:100px' ><br>";
		appHtml+= "<a href='javascript:rapPrDel(\""+prcode+"\");'>"+prname+"</a>";
		appHtml+= "</li> ";
		$("#checkRepProduct").html(appHtml);
		$('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
		outLayer();
	}
}

function relationProducts(prname,prcode,primg){
	var upList = true;
	var appHtml = "";
	if($("input[name='relationProduct[]']").length > 4){
		alert('관련상품은 5개까지 등록이 가능합니다.');
		upList = false;
		//return upList;
	}
	$("input[name='relationProduct[]']").each(function(){
		if($(this).val() == prcode){
			alert('상품이 중복되었습니다.');
			upList = false;
			return upList;
		}
	});
	if(upList){
		appHtml = "<li class='prListOn'>";
		appHtml+= "<input type='hidden' name='relationProduct[]' value='"+prcode+"'> ";
		appHtml+= "<img src='"+primg+"' style='width:100px' ><br>";
		appHtml+= "<a href='javascript:relationPrDel(\""+prcode+"\");'>"+prname+"</a>";
		appHtml+= "</li> ";
		$("#checkProduct").append(appHtml);
	}
}

function relationPrDel(prcode){
	if(confirm('관련상품을 삭제 하시겠습니까?')){
		$("input[name='relationProduct[]']").each(function(){
			if($(this).val() == prcode){
				$(this).parent().remove();
			}
		});
	}
}
function rapPrDel(prcode){
	if(confirm('대표상품을 삭제 하시겠습니까?')){
		$("#RepProduct").remove();
		$("#productcode").val("");
	}
}
function changeItem(){
	switch($("#board_code").val()){
			case '1' :
				$("#ID_RepProductChange").hide();
				$("#ID_RelationProduct").hide();
				$("#ID_bigImage").hide();
				$("#ID_ThumbnailImage").hide();
				$("#ID_PageCategory").hide();
				$("#ID_BigSize").html("");
				$("#ID_ThumbnailSize").html("");
				break;
			case '2' :
				$("#ID_RepProductChange").hide();
				$("#ID_RelationProduct").show();
				$("#ID_bigImage").hide();
				$("#ID_ThumbnailImage").show();
				$("#ID_PageCategory").hide();
				$("#ID_BigSize").html("");
				$("#ID_ThumbnailSize").html(" ( SIZE : 264 px X 264 px ) ");
				break;
			case '3' :
				$("#ID_RepProductChange").hide();
				$("#ID_RelationProduct").show();
				$("#ID_bigImage").show();
				$("#ID_ThumbnailImage").show();
				$("#ID_PageCategory").show();
				$("#ID_BigSize").html(" ( SIZE : 805 px X 521 px ) ");
				$("#ID_ThumbnailSize").html(" ( SIZE : 225 px X 317 px ) ");
				break;
			case '4' :
				$("#ID_RepProductChange").hide();
				$("#ID_RelationProduct").show();
				$("#ID_bigImage").hide();
				$("#ID_ThumbnailImage").show();
				$("#ID_PageCategory").hide();
				$("#ID_BigSize").html("");
				$("#ID_ThumbnailSize").html(" ( SIZE : 264 px X 264 px ) ");
				break;
			case '5' :
				$("#ID_RepProductChange").hide();
				$("#ID_RelationProduct").hide();
				$("#ID_bigImage").hide();
				$("#ID_ThumbnailImage").hide();
				$("#ID_PageCategory").show();
				$("#ID_BigSize").html("");
				$("#ID_ThumbnailSize").html("");
				break;
		}
}

function inPageCode(){
	$("#page_code").html("");
	$.post(
		"ajax_brandboard_catesort.php",
		{
			board_code:$("#board_code").val(),
			page_code:page_code
		},
		function(data){
			var inOption = "";
			if( data != null){
				$.each( data, function( index, item ) {
					inOption += "<option value='"+item.page_code+"'>"+item.page_name+"</option>";
				});
			}
			$("#page_code").html(inOption);
		},
		"json"
	);
}
$(document).ready(function(){
	var page_code = $("#this_pageCode").val();
	changeItem();
	$("#page_code").html("");
	$.post(
		"ajax_brandboard_catesort.php",
		{
			board_code:$("#board_code").val(),
			page_code:page_code
		},
		function(data){
			var inOption = "";
			if( data != null){
				$.each( data, function( index, item ) {
					if( index == "on" ) {
						inOption += "<option value='"+item.page_code+"' SELECTED >"+item.page_name+"</option>";
					} else {
						inOption += "<option value='"+item.page_code+"' >"+item.page_name+"</option>";
					}
				});
			}
			$("#page_code").html(inOption);
		},
		"json"
	);
	$("#board_code").on("change",function(){
		changeItem();
		$("#page_code").html("");
		$.post(
			"ajax_brandboard_catesort.php",
			{
				board_code:$("#board_code").val(),
				page_code:page_code
			},
			function(data){
				var inOption = "";
				if( data != null){
					$.each( data, function( index, item ) {
						if( index == "on" ) {
							inOption += "<option value='"+item.page_code+"' SELECTED >"+item.page_name+"</option>";
						} else {
							inOption += "<option value='"+item.page_code+"' >"+item.page_name+"</option>";
						}
					});
				}
				$("#page_code").html(inOption);
			},
			"json"
		);
	});
});
</script>