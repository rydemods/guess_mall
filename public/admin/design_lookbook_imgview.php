<?php
/********************************************************************* 
// 파 일 명		: design_lookbook_write.php
// 설     명		: LOOKBOOK 생성, 수정, 삭제
// 상세설명	: LOOKBOOK 생성, 수정, 삭제
// 작 성 자		: 2016.01.22 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 

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
	$PageCode = "me-5";
	$MenuCode = "member";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################



#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------

$num=$_POST["num"];
if(!$num) $num=$_GET["num"];

// 이미지 경로
$imagepath = $Dir.DataDir."shopimages/lookbook/";
// 이미지 파일
$imagefile = new FILE($imagepath);

$query="select * from tbllookbook_content where no='".$num."'";
$result=pmysql_query($query, get_db_conn() );
$data=pmysql_fetch_array($result);

$ex_productcodes=explode("|",$data['productcodes']);
$ex_lr_coordinates=explode("|",$data['lr_coordinates']);
$ex_ud_coordinates=explode("|",$data['ud_coordinates']);
$ex_view_lr=explode("|",$data['view_lr']);
$ex_view_ud=explode("|",$data['view_ud']);
$ex_view_img=explode("|",$data['view_img']);
$t_count=$data['total_num']?$data['total_num']:"0";

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------


?>
<script type="text/javascript" src="../lib/DropDown2.admin.js.php"></script>

<script>
function popupAutoResize() {
    var thisX = parseInt(document.getElementById('imgw').scrollWidth)+245;
    var thisY = parseInt(document.getElementById('imgw').scrollHeight)+77;
    var maxThisX = screen.width - 50;
    var maxThisY = screen.height - 77;
	var marginY = 0;
	
    
	
	if (navigator.userAgent.indexOf("MSIE 6") > 0) marginY = 45;        // IE 6.x
    else if(navigator.userAgent.indexOf("MSIE 7") > 0) marginY = 75;    // IE 7.x
    else if(navigator.userAgent.indexOf("Firefox") > 0) marginY = 50;   // FF
    else if(navigator.userAgent.indexOf("Opera") > 0) marginY = 30;     // Opera
    else if(navigator.userAgent.indexOf("Netscape") > 0) marginY = -2;  // Netscape

    if (thisX > maxThisX) {
		
        window.document.body.scroll = "yes";
        thisX = maxThisX;
    }else{
		window.document.body.scroll = "no";
	}
	
    if (thisY > maxThisY - marginY || thisY < $(".poslist").height()+77) {
        window.document.body.scroll = "yes";
        thisX += 19;
        //thisY = maxThisY - marginY;
    }else{
		window.document.body.scroll = "no";
	}
	
    window.resizeTo(thisX, thisY+marginY);

}

function indb_go(){
	$("#insertForm").submit();
}
</script>

<link rel="styleSheet" href="../css/admin.css" type="text/css">
<link rel="stylesheet" href="../css/common_look.css">
<style>
	.imgwrap{display:inline-block;position:relative;margin:10px;border:5px solid #000;}
	.imgwrap li{position:absolute;margin:-10px 0 0 -10px;width:20px;height:20px;background:#ff0000;color:#fff;font-weight:bold;font-size:12px;line-height:20px;text-align:center;}
	.imgwrap li:first-child{display:none;}
	
	.poslist{position:absolute;top:10px;right:10px;}
	.poslist li{box-sizing:border-box;position:relative;margin-bottom:10px;padding:20px 10px 5px;width:200px;border:1px solid #999;background:#fff;}
	.poslist li:first-child{display:none;}
	.poslist li span.num{display:block;position:absolute;top:0;left:0;width:20px;height:20px;background:#ff0000;color:#fff;font-weight:bold;line-height:20px;text-align:center;}
	.poslist li label{display:block;margin-top:5px;font-size:12px;}
	.poslist li label:first-child{margin-top:0;}
	.poslist li label span{display:block;margin-bottom:2px;}
	.poslist li label input{padding:0 5px;width:100%;height:20px;border-color:#999;}
	.poslist li button{position:absolute;top:0;right:0;width:30px;height:20px;background:#000;color:#fff;}
	.poslist li .local { margin-top:5px; vertical-align:middle; }
	.poslist li .local span {font-size:12px; line-height:24px;}
	.poslist li .local label {display:inline-block; vertical-align:middle; padding:0; margin:0; line-height:24px; padding-right:10px;}
	.poslist li .local label input {width:auto; vertical-align:middle; padding:0; margin:0;}
	.poslist .find-goods {position:relative; margin-top:3px;}
	.poslist .find-goods a {position:absolute; bottom:-1px; right:0;}
	.poslist .find-goods a img {padding:0 !important;}
	.button_right {text-align:center; padding-bottom:10px; width:200px;}
</style>

<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<script src="../js/jquery-1.12.1.min.js"></script>
<script>
	$(function() {
		
		var $imgwrap = $(".imgwrap");
		var $img = $imgwrap.find("img");
		var $numList = $imgwrap.find("ol");
		var $numListClone = $numList.find("li").eq(0);
		var $posList = $(".poslist");
		var $posListClone = $posList.find("li").eq(0);
		console.log($posListClone);
		var count = <?=$t_count?>;
		
		$(".imgwrap img").on("click", function(_e) {
			
			_e.preventDefault();
			count++;
			
			$numListClone.clone().appendTo($numList)
			.text(count)
			.css({ top:_e.offsetY, left:_e.offsetX });
			
			$posListClone.clone().appendTo($posList)
			.find(".num").text(count)
			.end().find(".s_postX").html("<input class=\"posX\" name=\"postX"+count+"\" type=\"text\">")
			.end().find(".s_postY").html("<input class=\"posY\" name=\"postY"+count+"\" type=\"text\">")
			.end().find(".posX").val(_e.offsetX)
			.end().find(".posY").val(_e.offsetY)
			.end().find(".search").html("<input type=\"hidden\" name=\"total_check[]\" value="+count+"><input type=\"text\" name=\"p_number"+count+"\" style=\"width:131px;\" readonly><a href=\"javascript:T_layer_open('layer_product_sel','relationProduct','"+count+"');\"><img src=\"./images/btn_search2.gif\" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a>")
			.end().find(".left_right").html("<label><input type=\"radio\" name=\"left_right"+count+"\" class=\"left\" value=\"L\" checked> 좌</label><label><input type=\"radio\" name=\"left_right"+count+"\" class=\"right\" value=\"R\"> 우</label>")
			.end().find(".up_down").html("<label><input type=\"radio\" name=\"up_down"+count+"\" class=\"left\" value=\"U\"> 상</label><label><input type=\"radio\" name=\"up_down"+count+"\" class=\"right\" value=\"D\" checked> 하</label>")
			.end().find(".img_color").html("<label><input type=\"radio\" name=\"img_color"+count+"\" class=\"left\" value=\"W\"> 흰색</label><label><input type=\"radio\" name=\"img_color"+count+"\" class=\"right\" value=\"B\" checked> 검정색</label>");
			popupAutoResize();
			
			
		});

		if(count){
			var j_productcodes="<?=$data['productcodes']?>";
			var j_ud_coordinates="<?=$data['lr_coordinates']?>";
			var j_lr_coordinates="<?=$data['ud_coordinates']?>";
			var p_split=j_productcodes.split("|");
			var ud_split=j_ud_coordinates.split("|");
			var lr_split=j_lr_coordinates.split("|");

			var xyname="[";
			for(var io=0;io<count;io++){
				xyname+='{"x":"'+lr_split[io]+'", "y":"'+ud_split[io]+'", "name":"'+p_split[io]+'"}, ';
			}
			xyname=xyname.slice(0,-2);
			xyname+="]";
			
			
			var obj = $.parseJSON(xyname);
			
			for(var i in obj) {
				
				$numListClone.clone().appendTo($numList)
				.css({ top:parseFloat(obj[i].x), left:parseFloat(obj[i].y) })
				.find(".num").text(parseFloat(i) + 1);
			}
		}
		
		$posList.on("click", ".btn-delete", function(_e) {
			
			_e.preventDefault();
			count--;
			
			var $this = $(this).closest("li");
			var index = $posList.find("li").index($this);
			
			$this.remove();
			$numList.find("li").eq(index).remove();
			
			$posList.find("li").each(function(_i) {
				
				$(this).find(".num").text(_i);
				$numList.find("li").eq(_i).text(_i);
				
			});
			popupAutoResize();
			
		});
		
	});
</script>
<form name='insertForm' id='insertForm' action='design_lookbook_indb.php' method='POST' enctype="multipart/form-data">
<input type="hidden" name="mode" value="insert">
<input type="hidden" name="num" value="<?=$num?>">
<?include("layer_prlistPop.php");?>
<div class="imgwrap" style="margin:0px;">
	<img src="<?=$imagepath.$data[img]?>" alt="" id="imgw" name='imgw'>
	<ol>
		<li><span class="num">0</span></li><!-- 첫번째 li는 복제용입니다. -->
	</ol>
</div>

<div class="poslist">
	<DIV class="button_right"><a href="javascript:indb_go()"><img src="img/btn/btn_input02.gif" alt="수정하기"></a></DIV>
	<ol>
		<li><!-- 첫번째 li는 복제용입니다. -->
			<span class="num">0</span>
			
			<label><span>X : </span><span class="s_postX"><input class="posX" name="postX0" type="text"></span></label>
			<label><span>Y : </span><span class="s_postY"><input class="posY" name="postY0" type="text"></span></label>
			<div class="find-goods">
				<label>
					<span>품번 : </span>
					<span class="search">
					
					<input type="text" name="p_number0" style="width:131px;" readonly>
					<a href="javascript:T_layer_open('layer_product_sel','relationProduct','0');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></span>
					
				</label>
			</div>
			<div class="local">
				<span>좌우 : </span>
				<span class="left_right">
				<label><input type="radio" name="left_right0" class="left" value="L" checked> 좌</label>
				<label><input type="radio" name="left_right0" class="right" value="R"> 우</label>
				</span>
			</div>
			<div class="local">
				<span>상하 : </span>
				<span class="up_down">
				<label><input type="radio" name="up_down0" class="left" value="U"> 상</label>
				<label><input type="radio" name="up_down0" class="right" value="D" checked> 하</label>
				</span>
			</div>
			<div class="local">
				<span>아이콘 : </span>
				<span class="img_color">
				<label><input type="radio" name="img_color0" class="left" value="W"> 흰색</label>
				<label><input type="radio" name="img_color0" class="right" value="B" checked> 검정색</label>
				</span>
			</div>
			<button class="btn-delete" type="button">삭제</button>
		</li>
		<?if($t_count>0){
			$cnt=1;
			for($i=0;$i<$t_count;$i++){
			
			$checked["left_right"][$i][$ex_view_lr[$i]]="checked";
			$checked["up_down"][$i][$ex_view_ud[$i]]="checked";
			$checked["img_color"][$i][$ex_view_img[$i]]="checked";
			?>
		<li>
			<span class="num"><?=$cnt?></span>			
			<label><span>X : </span><span class="s_postX"><input class="posX" name="postX<?=$cnt?>" value="<?=$ex_lr_coordinates[$i]?>" type="text"></span></label>
			<label><span>Y : </span><span class="s_postY"><input class="posY" name="postY<?=$cnt?>" value="<?=$ex_ud_coordinates[$i]?>" type="text"></span></label>
			<div class="find-goods">
				<label>
					<span>품번 : </span>
					<span class="search">
					<input type="hidden" name="total_check[]" value="<?=$cnt?>">
					<input type="text" name="p_number<?=$cnt?>" value="<?=$ex_productcodes[$i]?>" style="width:131px;" readonly>
					<a href="javascript:T_layer_open('layer_product_sel','relationProduct','<?=$cnt?>');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></span>
					
				</label>
			</div>
			<div class="local">
				<span>좌우 : </span>
				<span class="left_right">
				<label><input type="radio" name="left_right<?=$cnt?>" class="left" value="L" <?=$checked["left_right"][$i]["L"]?>> 좌</label>
				<label><input type="radio" name="left_right<?=$cnt?>" class="right" value="R" <?=$checked["left_right"][$i]["R"]?>> 우</label>
				</span>
			</div>
			<div class="local">
				<span>상하 : </span>
				<span class="up_down">
				<label><input type="radio" name="up_down<?=$cnt?>" class="left" value="U" <?=$checked["up_down"][$i]["U"]?>> 상</label>
				<label><input type="radio" name="up_down<?=$cnt?>" class="right" value="D" <?=$checked["up_down"][$i]["D"]?>> 하</label>
				</span>
			</div>
			<div class="local">
				<span>아이콘 : </span>
				<span class="img_color">
				<label><input type="radio" name="img_color<?=$cnt?>" class="left" value="W" <?=$checked["img_color"][$i]["W"]?>> 흰색</label>
				<label><input type="radio" name="img_color<?=$cnt?>" class="right" value="B" <?=$checked["img_color"][$i]["B"]?>> 검정색</label>
				</span>
			</div>
			<button class="btn-delete" type="button">삭제</button>
		</li>
		<?$cnt++;
			}
		}?>
		
	</ol>
	
</div>


</form>


<script>
window.onload = function(){popupAutoResize();}
</script>