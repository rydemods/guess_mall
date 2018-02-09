<?php

header("Content-Type:text/html;charset=EUC-KR");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");


$toyear = ($_POST['toyear'])?$_POST['toyear']:date("Y");
$tomonth = $_POST['tomonth']?$_POST['tomonth']:date("m");
$tomonth = str_pad($tomonth,2,"0",STR_PAD_LEFT);
$dateori = mktime(0,0,0,$tomonth,1,$toyear);

$todayStr = date("Y-m-01",$dateori);

##### �̴޿� �ִ� ������ Ư�� ����

$toyearmonth = $toyear."-".$tomonth;
$sql_onedayM = "SELECT a.*, b.maximage, b.minimage, b.tinyimage FROM tblproductoneday as a ";
$sql_onedayM.= "LEFT JOIN tblproduct as b on a.productcode=b.productcode ";
$sql_onedayM.= "WHERE applydate like '{$toyearmonth}%' ";
$sql_onedayM.= "ORDER BY applydate ";
$res_onedayM = pmysql_query($sql_onedayM);
while($row_onedayM = pmysql_fetch_array($res_onedayM)){
	$app_date_arr = explode("-",$row_onedayM['applydate']);
	$app_date_arr[2] += 0;	//���ڷ� ����� �ֱ� ����
	$_oMdata[$app_date_arr[2]] = $row_onedayM;
}

##### //�̴޿� �ִ� ������ Ư�� ����

?>

	<div class="title">
		<h3>TIME SALE<span>���� �������� �������踸�� Ư��!</span></h3>
		<a href="javascript:closeCal();" class="close"></a>
	</div>
	<div class="celender_layer">
		<div class="year_month">
			<span class="arrow"><a href="javascript:showCal('<?=date("Y",$dateori)?>','<?=date("m",$dateori)-1?>');"><</a></span>
			<span class="year"><?=$toyear?></span><span class="num"><?=date("n",$dateori)?></span><span class="month"><?=date("F",$dateori)?></span>
			<span class="arrow"><a href="javascript:showCal('<?=date("Y",$dateori)?>','<?=date("m",$dateori)+1?>');">></a></span>
		</div>
		<ul class="day_eng">
			<li class="dahong">sun</li>
			<li>mon</li>
			<li>tue</li>
			<li>wed</li>
			<li>thu</li>
			<li>fri</li>
			<li class="blue">sat</li>
		</ul>
		<ul class="calender_day">
		<?php
			$rtoday = date("Ymd");	// ���� ��¥
			$last_day = date("t",strtotime($todayStr));	//�� �ϼ�
			$start_week = date("w",strtotime($toyearmonth."-01")); //���ۿ���
			$total_week = ceil(($last_day + $start_week)/7);  //�� row  ��
			$day = 1;
			$dayclass[0]=" dahong";	//�Ͽ��� ��¥ Ŭ����
			$dayclass[6]=" blue";	//����� ��¥ Ŭ����
			##### �޷� �� ��ĭ
			for($si=0;$si<$start_week;$si++){
		?>
			<li><a><a><img src="img/white_bg.gif" alt="" /></a></a></li>
		<?php
			}
			##### �޷� ��¥��
			$imagepath = $Dir.ImageDir."product/";
			for($di=1;$di<$last_day+1;$di++){
				$vtoday = $toyear.$tomonth.str_pad($di,2,"0",STR_PAD_LEFT);
				$vtoday_str = $toyear."-".$tomonth."-".str_pad($di,2,"0",STR_PAD_LEFT);
				$classnum = ($di+$start_week-1)%7;
				if($_oMdata[$di]){
					if($rtoday>$vtoday){
					#### ���� �� ��¥��
		?>
			<li>
				<p class="day_num<?=$dayclass[$classnum]?>">20</p>
				<a href="javascript:goReserve('<?=$vtoday_str?>','encore');">
					<img src="<?=$imagepath.$_oMdata[$di]['tinyimage']?>" alt="" />
					<p class="price02"><?=number_format($_oMdata[$di]['dcprice'])?></p>
				</a>
			</li>
		<?	
						
					}else if($rtoday==$vtoday){
					#### ����
		?>
			<li>
				<p class="day_num<?=$dayclass[$classnum]?>">20</p>
				<a href="#">
					<img src="<?=$imagepath.$_oMdata[$di]['tinyimage']?>" alt="" />
					<p class="price02"><?=number_format($_oMdata[$di]['dcprice'])?></p>
				</a>
			</li>

		<?	
					}else{
					#### ���� ���� ��¥
		?>
			<li>
				<p class="day_num">21</p>
				<a class="next" href="javascript:goReserve('<?=$vtoday_str?>','reserve');">
					<img src="<?=$imagepath.$_oMdata[$di]['tinyimage']?>" alt="" />
				</a>
		<?	
					}
				}else{
				#### �����Ͱ� ���� ���ΰ��
		?>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num<?=$dayclass[$classnum]?>"><?=$di?></p></li>
		<?php
				}
			}
			##### �޷� �� ��ĭ
			$last_blank_num = 7-(($last_day-(7-$start_week))%7);
			for($lasti=0;$lasti<$last_blank_num;$lasti++){
		?>
			<li><a><a><img src="img/white_bg.gif" alt="" /></a></a></li>

		<?php
			}
		?>
<!--
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num blue">2</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num dahong">3</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">4</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">5</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">6</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">7</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">8</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num blue">9</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num dahong">10</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">11</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">12</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">13</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">14</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num dahong">15</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num blue">16</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num dahong">17</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">18</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">19</p></li>
			<li>
				<p class="day_num">20</p>
				<a href="#">
					<img src="img/test/test_goods.jpg" alt="" />
					<p class="price02">110,000</p>
				</a>
			</li>
			<li>
				<p class="day_num">21</p>
				<a class="next" href="#">
					<img src="img/test/test_goods.jpg" alt="" />
				</a>
			</li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">22</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num blue">23</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num dahong">24</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">25</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">26</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">27</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">28</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num">29</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num blue">30</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a><p class="day_num dahong">31</p></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a></li>
			<li><a><img src="img/white_bg.gif" alt="" /></a></li>
-->
		</ul>
	</div>