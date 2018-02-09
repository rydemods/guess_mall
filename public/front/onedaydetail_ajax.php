<?php 

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


$toyear = ($_POST['toyear'])?$_POST['toyear']:date("Y");
$tomonth = $_POST['tomonth'];
$tomonth = str_pad($tomonth,2,"0",STR_PAD_LEFT);
$dateori = mktime(0,0,0,$tomonth,1,$toyear);

$todayStr = date("Y-m-01",$dateori);

##### 이달에 있는 오늘의 특가 정보

$toyearmonth = $toyear."-".$tomonth;
$sql_onedayM = "SELECT a.*, b.maximage, b.minimage, b.tinyimage FROM tblproductoneday as a ";
$sql_onedayM.= "LEFT JOIN tblproduct as b on a.productcode=b.productcode ";
$sql_onedayM.= "WHERE applydate like '{$toyearmonth}%' ";
$sql_onedayM.= "ORDER BY applydate ";
$res_onedayM = pmysql_query($sql_onedayM);
while($row_onedayM = pmysql_fetch_array($res_onedayM)){
	$app_date_arr = explode("-",$row_onedayM['applydate']);
	$app_date_arr[2] += 0;	//숫자로 만들어 주기 위해
	$_oMdata[$app_date_arr[2]] = $row_onedayM;
}

##### //이달에 있는 오늘의 특가 정보

?>
			<div class="year_month">
					<span class="year">
						<a href="javascript:goCalendar('<?=date("Y",$dateori)?>','<?=date("m",$dateori)-1?>')">
							&lt;
						</a>
						&nbsp;&nbsp;&nbsp;<?=date("Y",$dateori)?>
					</span>
					<span class="num">
						<?=date("n",$dateori)?>
					</span>
					<span class="month">
						<?=date("F",$dateori)?>
						&nbsp;&nbsp;&nbsp;
						<a href="javascript:goCalendar('<?=date("Y",$dateori)?>','<?=date("m",$dateori)+1?>')">&gt;</a>
					</span>
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
					$rtoday = date("Ymd");	// 오늘 날짜
					$last_day = date("t",strtotime($todayStr));	//총 일수
					$start_week = date("w",strtotime($toyearmonth."-01")); //시작요일
					$total_week = ceil(($last_day + $start_week)/7);  //총 row  수
					$day = 1;
					$dayclass[0]=" dahong";	//일요일 날짜 클래스
					$dayclass[6]=" blue";	//토요일 날짜 클래스
					##### 달력 앞 빈칸
					for($si=0;$si<$start_week;$si++){
				?>
					<li></li>
				<?php
					}
					##### 달력 날짜들
					$imagepath = $Dir.ImageDir."product/";
					for($di=1;$di<$last_day+1;$di++){
						$vtoday = $toyear.$tomonth.str_pad($di,2,"0",STR_PAD_LEFT);
						$vtoday_str = $toyear."-".$tomonth."-".str_pad($di,2,"0",STR_PAD_LEFT);
						$classnum = ($di+$start_week-1)%7;
						if($_oMdata[$di]){
							if($rtoday>$vtoday){
							#### 오늘 전 날짜들
				?>
					<!--앵콜요청-->
					<li>
						<img src="<?=$imagepath.$_oMdata[$di]['tinyimage']?>" alt="" style="width: 106px;"/>
						<p class="day_num<?=$dayclass[$classnum]?>"><?=$di?></p>
						<p class="icon">
							<a href="javascript:goReserve('<?=$vtoday_str?>','encore');"><img src="../img/icon/icon_ancore.png" alt="" /></a>
						</p>
						
					</li>
					<!--//앵콜요청-->
				<?	
								
							}else if($rtoday==$vtoday){
							#### 오늘
				?>
					<!--오늘의 특가-->
					<li>
						<a href="#"><img src="<?=$imagepath.$_oMdata[$di]['tinyimage']?>" id="todayprd" alt="" style="width: 106px;"/></a>
						<p class="day_num<?=$dayclass[$classnum]?>"><?=$di?></p>
						<p class="price">
							<span><?=number_format($_oMdata[$di]['sellprice'])?>원</span>
							<?=number_format($_oMdata[$di]['dcprice'])?>원
							<a href="#" class="btn_more"></a>
						</p>
					</li>
					<!--//오늘의 특가-->
				<?	
							}else{
							#### 오늘 이후 날짜
				?>
					<!--예약요청-->
					<li>
						<img src="<?=$imagepath.$_oMdata[$di]['tinyimage']?>" alt="" style="width: 106px;"/>
						<p class="day_num<?=$dayclass[$classnum]?>"><?=$di?></p>
						<p class="next_goods"></p>
						<p class="icon">
						<?php if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted"){ ?>
							<a href="javascript:goReserve('<?=$vtoday_str?>','reserve')">
								<img src="../img/icon/icon_alram.png" alt="" />
							</a>
						<?php }else{ ?>
							<a href="javascript:check_login()">
								<img src="../img/icon/icon_alram.png" alt="" />
							</a>
						<?php } ?>
						</p>
					</li>
					<!--//예약요청-->
				<?	
							}
						}else{
				?>
					<li><p class="day_num<?=$dayclass[$classnum]?>"><?=$di?></p></li>
				<?php
						}
					}
					##### 달력 뒤 빈칸
					$last_blank_num = 7-(($last_day-(7-$start_week))%7);
					for($lasti=0;$lasti<$last_blank_num;$lasti++){
				?>
					<li></li>
				<?php
					}
				?>
				</ul>