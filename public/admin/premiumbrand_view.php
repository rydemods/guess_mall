<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/premiumbrand.class.php");
include("access.php");
?>
<?

$menu = $_REQUEST['menu'] ? : 'cube';
$menu_on[$menu] ='class="on"';
$brand_no = $_REQUEST['brand_no'];
$pb = new PREMIUMBRAND('pb_info');
$pb_info = $pb->pb_info;

$imagepath_logo = $Dir.DataDir."shopimages/mainbanner/";
$imagepath = $Dir.DataDir."shopimages/premiumbrand/";
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>프리미엄 브랜드 디자인</title>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="static/css/crm.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script src="../js/jquery-1.10.1.min.js" type="text/javascript"></script>


<style type="text/css">

</style>
</head>

<!--body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();"-->

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow:hidden;" onLoad="PageResize();">

<table border=0 cellpadding=0 cellspacing=0 style="table-layout:fixed;" id=table_body>
<tr>
    <td width=100%>


		<div id="crm-wrap">
			<div class="crm-header">
			//////////	
			</div>
		</div>

		<div id="crm-container">
		
			<div class="lnb-wrap">
				<p class="name"><?=$pb_info->banner_title?> 브랜드 디자인</p>
				<div class="inner">
					<div class="quick-message hide">
						<p class="grade"><strong></strong></p>
						
					</div>
					<ul class="visit-date">
						<li><img src='<?=$imagepath_logo.$pb_info->banner_img?>' style='max-width : 70px;' ></li>
					</ul>
					<ul class="nav-menu">
						<li <?=$menu_on['cube']?>><a data-menu="cube" class="p_menu">큐브설정</a></li>
						<li <?=$menu_on['section']?>><a data-menu="section" class="p_menu">섹션설정</a></li>
					
					</ul>
                 
				</div>
			</div><!-- //.lnb-wrap -->

			<?
			if($menu == "cube"){
				include "./premiumbrand_cube.php";
			}else if($menu == "section"){
				include "./premiumbrand_section.php";
			}
			?>

		</div>


    </td>
</tr>
</table>

<?=$onload?>

<form name=form1>
<input type=hidden name='menu' value="<?=$menu?>">
<input type=hidden name='brand_no' value='<?=$brand_no?>'>
</form>

<SCRIPT LANGUAGE="javascript">

var chk_menu = "<?=$menu?>";

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 16;
	var oHeight = document.all.table_body.clientHeight + 66;
	
	window.resizeTo(oWidth,oHeight);
}

function change_menu()
{
	var menu = $(this).data('menu');
	if(chk_menu != menu){
		document.form1.menu.value=menu;
		document.form1.submit();
	}
}

function imgDel(img,file,v_file){
	var agent = navigator.userAgent.toLowerCase();

	$("#"+img).children().attr("src","");

	if (agent.indexOf("msie") != -1) {
		// ie 일때 
		$("#"+file).replaceWith( $("#"+file).clone(true) );
		$("#"+v_file).replaceWith( $("#"+v_file).clone(true) );
	} else {
		// other browser 일때 
		$("#"+file).val("");
		$("#"+v_file).val("");
	}
}

$(document).on("click",".p_menu",change_menu);

</SCRIPT>
</body>
</html>