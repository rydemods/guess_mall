
<?php
/**
* 이벤트 리스트 페이지
*
*/
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging.php");
include ($Dir.MainDir.$_data->menu_type.".php");


#####당첨자 발표&이벤트 공지사항 탭 구분하기
$tab = ($_REQUEST['tab'])?$_REQUEST['tab']:"0";
$class_on_tab[$tab]='on';


##### 진행중인 이벤트&마감된 이벤트&내가 참여한 이벤트
if($_GET['category_code']){
	$category="마감";
	$category_code = 1;
}else{
	$category="진행중";
	$category_code=0;
};
$class_on_cate[$category_code]=' class="on"';
$searchtxt = $_REQUEST['searchtxt'];

?>
<SCRIPT LANGUAGE="JavaScript">
function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

function goEventCate(cate){
	if(cate==''||cate==null){
		cate=0;
	}
	document.form2.block.value='';
	document.form2.gotopage.value='';
	document.form2.category_code.value=cate;
	document.form2.submit();

}

function goTab2(tab){
	document.form2.tab.value=tab;
	document.form2.submit();
}

function goTab(tab){
	if(tab=="0"){
		$("#event_notice").removeClass("on");
		$("#event_result").addClass("on");
	}else{
		$("#event_result").removeClass("on");
		$("#event_notice").addClass("on");
	}
}

function goSearch(){
	document.form2.searchtxt.value = document.form1.searchtxt.value
	document.form2.submit();
}

function goView(num){
	document.form3.num.value=num;
	alert(document.form3.num.value=num);
	document.form3.submit();
}

function eventswitch(id){ //이벤트 항목 전환


	var objDiv = document.getElementById(id);

	if(id==('event_all'))
	{objDiv.style.display="block";
	event_now.style.display="none";
	event_end.style.display="none";
	document.getElementById('event1').className="on";
	document.getElementById('event2').className = "";
	document.getElementById('event3').className = "";
	}

	if(id==('event_now'))
	{objDiv.style.display="block";
	event_all.style.display="none";
	event_end.style.display="none";
	document.getElementById('event2').className="on";
	document.getElementById('event1').className = "";
	document.getElementById('event3').className = "";
	}

	if(id==('event_end'))
	{objDiv.style.display="block";
	event_all.style.display="none";
	event_now.style.display="none";
	document.getElementById('event3').className="on";
	document.getElementById('event1').className = "";
	document.getElementById('event2').className = "";
	}

};

</SCRIPT>

	<div class="index_rolling_wrap">
		<div class="index_rolling" id="main_slide">
<?php
		$eventbanner_sql="select * from tblmainbannerimg where banner_name='event_banner' and banner_hidden='1' ORDER BY banner_sort;";
		$eventbanner_res = pmysql_query($eventbanner_sql,get_db_conn());
?>
		<?
		while($eventbanner_row = pmysql_fetch_object($eventbanner_res)){
			$eventbanner[]=$eventbanner_row;
		}
		?>

			<ul>
				<?for($i=0 ; $i < count($eventbanner) ; $i++){?>
					<li>
						<img src="<?=$imgurl.$eventbanner[$i]->banner_img;?>"/>
					</li>
				<? } ?>
			</ul>

		</div>
		<span id="controls">
			<ol class="controls">
			<li><a class="sudoSlide-on onStop" style="cursor: pointer;" ></a></li>
			<li><a class="sudoSlide-on onPlay" style="cursor: pointer;display: none;" ></a></li>
			<? for($i=0 ; $i < count($eventbanner) ; $i++){ ?>
				<li class="onNumber <? if($i==0){echo "on";}?>" rel="<?=$i+1?>"><a class="sudoSlide-on" href="javascript:void(<?=$i+1?>);" data-target="<?=$i+1?>"><span><?=$i+1?></span></a></li>
			<? } ?>
			</ol>
		</span>
	 </div>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td>
	<form name=form1 method=get action="<?=$_SERVER['PHP_SELF']?>">
<?php

include($Dir.TempletDir."event/event_list_tem001.php");

?>
	</form>
	</td>
</tr>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=category_code value="<?=$category_code?>">
<input type=hidden name=searchtxt value="<?=$searchtxt?>">
<input type=hidden name=tab value="<?=$tab?>">
</form>

<form name=form3 method="POST" action="event_view.php">
<input type=hidden name=num value="">
</form>

</table>



<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>