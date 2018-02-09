<?php
//exdebug($_COOKIE);
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
if(is_array($_layerdata)) {	//상단 이벤트 팝업에서 이미 쿼리를 하였다.
?>
	<script src='../js/jquery-ui.min.js'></script>
	<script language="javascript">
	function layer_open(num){ 
		if(localStorage.getItem("not_open_day"+num) == "Y" && (new Date().getTime() < localStorage.getItem("not_open_day_expire"+num))) 
		{ 
		
			$("#draggable"+num).hide(); 
		} 
		else
		{ 
			//팝업 레이어 움직이기 
			$("#draggable"+num).draggable({ cursor: "move" }); 

			//오늘 하루 열지 않기 
			$("#not_open_day"+num).click(function(){                         
				localStorage.setItem("not_open_day"+num,"Y"); 
				localStorage.setItem("not_open_day_expire"+num, new Date().getTime() + (24*60*60*1000)); 
				$("#draggable"+num).hide(); 
			}); 

			//닫기 
			$("#close_popup"+num).click(function(){ 
				$("#draggable"+num).hide(); 
			});  
		}  
	}



	// 쿠키 생성
	/*
	function setCookie(cName, cValue, cDay){
		var expire = new Date();
		expire.setDate(expire.getDate() + cDay);
		alert( expire.toGMTString());
		cookies = cName + '=' + escape(cValue) + '; path=/ '; 
		cookies += ';expires=' + expire.toGMTString() + ';';
		document.cookie = cookies;
	}
	*/
	function setCookie( name, value, expiredays )
	{
		var todayDateRemain = new Date();
		if(expiredays>0){
			var expiredays1 = parseInt(expiredays);
		}
		todayDateRemain.setDate( todayDateRemain.getDate() + expiredays1 );
		
		//alert( todayDateRemain.toGMTString());
		document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDateRemain.toGMTString() + ";"
	}
	function p_windowclose(id,num){
		$('.eventPopup'+id).remove();
	}

	$(document).ready(function(){
		$(".close_main_layer").click(function(){
			var closeLayerId = $(this).attr('idx');
			var closeLayerTimeCheck = $(this).attr('time');
			var closeLayerTime = 1;

			if(closeLayerTimeCheck == '1'){
				closeLayerTime = "1";
			}else if(closeLayerTimeCheck == '2'){
				closeLayerTime = "720";
			}else{
				closeLayerTime = "-1";
			}

			if($(this).prev().prop('checked')){
				setCookie("layerNotOpen"+closeLayerId, "1", closeLayerTime);
			}
			$('.eventPopup'+closeLayerId).remove();
		});
		// draggableMainLayers 없을때도 불러와서 있을때만 불러오게 수정 2016-03-08 03:22
		if($(".draggableMainLayers").length > 0) $(".draggableMainLayers").draggable({ cursor: "move" }); 

	})
	</script>
<?
	$layer_str="";
	for($i=0;$i<count($_layerdata);$i++) {
		if($_layerdata[$i]->frame_type=="2") {
			if(!$_COOKIE["layerNotOpen".$_layerdata[$i]->num]){
				$row=$_layerdata[$i];
				$layer="Y";
				$one=2;
				
				//IF($_SERVER[REMOTE_ADDR]=='121.126.44.129'){
					
?>				
				<style> 
		            #draggable<?=$_layerdata[$i]->num?> { 
					top: <?=$_layerdata[$i]->y_to?>px; left : <?=$_layerdata[$i]->x_to?>px; 
					/* width : <?=$_layerdata[$i]->x_size?>px; height:<?=$_layerdata[$i]->y_size?>px;  */
					BACKGROUND-COLOR: #FFFFFF;border:1px solid #515152;
					} 
		            span { font-size:12px; } 
		        </style> 
				<div id="draggable<?=$_layerdata[$i]->num?>" class="ui-widget-content draggableMainLayers eventPopup<?=$_layerdata[$i]->num?>" style="position:absolute;z-index:999;">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>				
							<?include($Dir.TempletDir."event/event".$_layerdata[$i]->design.".php");?>			
						</td>
					</tr>
					</table>
				</div>
<?		

			}
		}
	}

}
?>

