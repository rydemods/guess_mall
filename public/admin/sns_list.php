<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/instagram.php");
include("access.php");
#include("calendar.php");
####################### 페이지 접근권한 check ###############
$PageCode = "co-4";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode = $_REQUEST['mode'];
if ($mode=='') $mode = "reset";
//echo $mode;
if($mode == "remove_token" || $mode == "reset") {
	setcookie("insta_token", "", time()-3600);
	$_COOKIE['insta_token']	= "";
	if($mode == "remove_token") {
		echo "<script>location.href='{$_SERVER['PHP_SELF']}';</script>";
		exit;
	}
}
$token = $_COOKIE['insta_token'];
//echo $token;
$s_check = $_REQUEST['s_check'];
$s_check = $s_check?$s_check:"1";
setcookie("insta_brand", $s_check, time()+31536000);

switch($mode){
	case "add":
		#debug($_POST);
		$checked_idx = $_POST['checked_idx'];
		$media_id = $_POST['media_id'];
		$insta_id = $_POST['insta_id'];
		$link     = $_POST['link'];
		$img_low  = $_POST['img_low'];
		$img_thm  = $_POST['img_thm'];
		$img_std  = $_POST['img_std'];
		$txt      = $_POST['txt'];

		$hash_tag_arr	= array('SINWON', 'VIKI', 'SIEG','SIEG FAHRENHEIT','VANHART DI ALBAZAR',"BESTIBELLI","SI");
		$hash_tag	= $hash_tag_arr[$s_check];

		foreach($checked_idx as $i){
			$regdt	= date("YmdHis");
			$txt_val  = str_replace("'", "`", $txt[$i]);
			$title	= mb_substr($txt_val, 0, 150, 'utf-8');

			//$sql = "INSERT INTO tblsnsinstamedia (insta_id,media_id,link,image_low,image_thum,image_std,text,reg_date) VALUES ({$insta_id[$i]},{$media_id[$i]},'{$link[$i]}','{$img_low[$i]}','{$img_thm[$i]}','{$img_std[$i]}','{$txt_val}',now())";
			$sql = "
            WITH upsert as (
                update  tblinstagram 
                set 	
                        title = '{$title}',
                        content = '{$txt_val}', 
						link_url = '{$link[$i]}',
						link_m_url = '{$link[$i]}',
						img_file = '{$img_std[$i]}',
						img_rfile = '{$img_std[$i]}',
						img_m_file = '{$img_std[$i]}',
						img_m_rfile = '{$img_std[$i]}',
						hash_tags = '{$hash_tag}',
						display = 'Y',
						regdt = '{$regdt}'
                where insta_id = '{$insta_id}' AND media_id = '{$media_id}'
                RETURNING * 
            )
            insert into tblinstagram (
			title,
			content,
			link_url,
			link_m_url,
			img_file,
			img_rfile,
			img_m_file,
			img_m_rfile,
			hash_tags,
			insta_id,
			media_id,
			display,
			regdt
			) 
            Select  
			'{$title}',
			'{$txt_val}',
			'{$link[$i]}',
			'{$link[$i]}',
			'{$img_std[$i]}',
			'{$img_std[$i]}',
			'{$img_std[$i]}',
			'{$img_std[$i]}',
			'{$hash_tag}',
			'{$insta_id[$i]}',
			'{$media_id[$i]}',
			'Y',
			'{$regdt}'
            WHERE NOT EXISTS ( select * from upsert ) ";
			//exdebug($sql);
			$res = pmysql_query($sql);
			#debug($sql);
		}

	case "search" :
		$insta = new Instagram;
		if ($s_check == '1') {
			$insta->client_id	= $insta->vk_client_id;
			$insta->client_secret	= $insta->vk_client_secret;
		} else if ($s_check == '2') {
			$insta->client_id	= $insta->sg_client_id;
			$insta->client_secret	= $insta->sg_client_secret;
		} else if ($s_check == '3') {
			$insta->client_id	= $insta->sgf_client_id;
			$insta->client_secret	= $insta->sgf_client_secret;
		} else if ($s_check == '4') {
			$insta->client_id	= $insta->vh_client_id;
			$insta->client_secret	= $insta->vh_client_secret;
		} else if ($s_check == '5') {//20180123 bshan
			$insta->client_id	= $insta->bb_client_id;
			$insta->client_secret	= $insta->bb_client_secret;
		} else if ($s_check == '6') {//20180124 bshan
			$insta->client_id	= $insta->si_client_id;
			$insta->client_secret	= $insta->si_client_secret;
		}

		$data = array("access_token"=>$token);
		$insta->api = "v1/users/self/media/recent/";
		$insta->method = 0;
		$res = $insta->get_json($data);

		//image size : low_resolution(320x320),thumbnail(150x150),standard_resolution(640x640)
		if($res->data){
			foreach($res->data as $data){
				$media_id = explode("_", $data->id);
				$res1['media_id'] = $media_id[0];
				$res1['insta_id'] = $data->user->id;
				$res1['link']     = $data->link;
				$res1['img_low']  = $data->images->low_resolution->url;
				$res1['img_thm']  = $data->images->thumbnail->url;
				$res1['img_std']  = $data->images->standard_resolution->url;
				$res1['txt']      = $data->caption->text;
				$res2[] = $res1;
				$media_ids[] = $media_id[0];
			}
			$query = "SELECT media_id, insta_id FROM tblinstagram WHERE media_id in ('".implode("','",$media_ids)."')";
			$result = pmysql_query($query);
			while($row=pmysql_fetch_object($result)) {
				$media_saved[] = $row->media_id."_".$row->insta_id;
			}
			pmysql_free_result($result);
			if(count($media_saved)==20) $msg = "최근 20건 모두 저장되어있습니다.";
		}
}

//echo $s_check;
if ($s_check == '1') {
	$insta_client_id	= Instagram::$vk_client_id;
} else if ($s_check == '2') {
	$insta_client_id	= Instagram::$sg_client_id;
} else if ($s_check == '3') {
	$insta_client_id	= Instagram::$sgf_client_id;
} else if ($s_check == '4') {
	$insta_client_id	= Instagram::$vh_client_id;
} else if ($s_check == '5') {//20180123 bshan
	$insta_client_id	= Instagram::$bb_client_id;
} else if ($s_check == '6') {//20180124 bshan
	$insta_client_id	= Instagram::$si_client_id;
}

include("header.php");
?>
<style>
ul#media { line-hegiht:100px }
ul#media li { float:left }
ul#media li textarea { width:150px; height:150px; vertical-align:top; background-color:#eee; border:0; padding:3px }
ul#media li input[type=checkbox]{ vertical-align:top }
</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>
$( document ).ready( function() {
<?php if($msg) echo "\talert('{$msg}');"; ?>

	$('#chkall').click( function() {
		$('ul#media input[type=checkbox]:enabled').prop( 'checked', this.checked );
	});
});

function insta_search() {
<?php if(!$token){ ?>
	var url = "https://api.instagram.com/oauth/authorize/?client_id=<?=$insta_client_id?>&redirect_uri=<?=Instagram::$redirect_uri?>&response_type=code";
	window.open(url,"insta_pop","width=420,height=240,resizable=yes");
<?php }else{ ?>
	document.form1.mode.value = "search";
	document.form1.s_check.value = document.sForm.s_check.value;
	document.form1.submit();
<?php }?>
}

function insta_add(){
	document.form1.mode.value = "add";
	document.form1.s_check.value = document.sForm.s_check.value;
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";
	period[5] = "<?=$period[5]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function insta_del(id){
	if(confirm("정말 삭제하시겠습니까?")) {
		document.form1.mode.value = "delete";
		document.form1.sno.value = id;
		document.form1.submit();
	}
}
function remove_token(){
	document.form1.mode.value = "remove_token";
	document.form1.submit();
}
function reset_list(){
	document.sForm.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; SNS 관리 &gt; <span>인스타그램 연동 리스트</span></p></div></div>

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
			<?php include("menu_community.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">인스타그램 연동</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>인스타그램에 등록된 최근 20건의 미디어를 저장할 수 있습니다</span></div>
				</td>
			</tr>
			<form name="sForm" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<input type="hidden" name="mode" value="reset">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">브랜드 검색 선택</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>검색</span></th>
					<td>
					<select name="s_check" class="select" onChange="javascript:reset_list();">
					<option value="1" <?php if($s_check=="1")echo"selected";?>>VIKI</option>
					<option value="2" <?php if($s_check=="2")echo"selected";?>>SIEG</option>
					<option value="3" <?php if($s_check=="3")echo"selected";?>>SIEG FAHRENHEIT </option>
					<option value="4" <?php if($s_check=="4")echo"selected";?>>VanHart di Albazar</option>
					<option value="5" <?php if($s_check=="5")echo"selected";?>>BESTIBELLI</option>
					<option value="6" <?php if($s_check=="6")echo"selected";?>>SI</option>
					</select>
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			</form>
			<tr>
				<td style="padding-top:4pt;" align="center">
					<a href="javascript:insta_search();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;
					<a href="javascript:insta_add()"><img src="images/btn_badd2.gif" border="0"></a>&nbsp;
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td style="padding-bottom:3pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="372">&nbsp;</td>
						<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=count($res2)?></B>건</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
					<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
					<input type=hidden name=mode>
					<input type=hidden name=s_check>
<?php if($res2){ ?>
					<input type="checkbox" id="chkall" style="padding-bottom:10px" /><label for="chkall">모두선택</label>
					<ul id="media">
<?php
		$idx = 0;
		foreach($res2 as $data) {
			$media = $data['media_id']."_".$data['insta_id'];
			$disabled = in_array($media,$media_saved) ? " disabled" : "";
?>
						<li>
							<input type="checkbox" name="checked_idx[]" value="<?=$idx?>"<?=$disabled?> />
							<input type="hidden" name="media_id[<?=$idx?>]" value="<?=$data['media_id']?>" />
							<input type="hidden" name="insta_id[<?=$idx?>]" value="<?=$data['insta_id']?>" />
							<input type="hidden" name="link[<?=$idx?>]" value="<?=$data['link']?>" />
							<input type="hidden" name="img_low[<?=$idx?>]" value="<?=$data['img_low']?>" />
							<input type="hidden" name="img_thm[<?=$idx?>]" value="<?=$data['img_thm']?>" />
							<input type="hidden" name="img_std[<?=$idx?>]" value="<?=$data['img_std']?>" />
							<a href="<?=$data['link']?>" target="_blank"><img src='<?=$data['img_thm']?>'/></a>
							<textarea name="txt[<?=$idx?>]"><?=$data['txt']?></textarea>
						</li>
<?php
			$idx++;
		}
?>
					</ul>
<?php } ?>
					</form>
				</div>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>인스타그램에 등록된 미디어 연동</span></dt>
							<dd>- 인스타그램에 로그인하지 않았을 경우 로그인 창이 나옵니다.</dd>
							<dd>- 최근에 등록된 20건의 미디어가 조회됩니다.</dd>
							<dd>- 추가할 미디어를 선택한 후, 추가하기 버튼을 누르면 저장됩니다.</dd>
							<dd>- 이미 추가된 미디어는 체크박스가 비활성화되며, 상품 연계 리스트에서 조회할 수 있습니다.</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<?if($mode=="reset"){?>
<div style="display:none"><iframe src="https://instagram.com/accounts/logout/" width="0" height="0"></iframe></div>
<?}?>
<?=$onload?>
<?php
include("copyright.php");
?>
