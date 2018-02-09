<?
class WEATHER{
	
	
	# 지금 지역별 날씨
	function now_weather()
	{
		$array = array(); 

		$url1="www.kma.go.kr";
		$url2="GET /";
		$url2.="XML/weather/sfc_web_map.xml";
		$url2.=" HTTP/1.0\r\nHost:www.kma.go.kr\r\n\r\n";

		$fp2 = fsockopen ($url1, 80, $errno, $errstr,30 );
		if (!$fp2) echo "?   $errstr ($errno)<br />n";
		else
		{
			fputs ($fp2, $url2);
			while (!feof($fp2))
			{
				$line=fgets ($fp2,512);
				if(ereg("<local",$line))
				{
					$area=preg_split("/\>/",$line);
					$area=preg_split("/\</",$area[1]);
					$area =$area[0];

					$array[$area]=$this->$area[0];//지역설정

					$value=preg_split("/\"/",$line);
					//$array[$area]['value']=$value;
					$array[$area]['icon']= $value[3]; //아이콘 변수현재상태
					$array[$area]['desc']= $value[5]; //현재상태 한글 / 맑음 흐림 구름많음 박무 구름조금 
					$array[$area]['temp']= $value[7]; //현재온도
				}
			}
		}
		fclose($fp2);
		return $array;
	}

	# 3시간 단위별 날씨
	function day_weather($url){
		
		$result = simplexml_load_file($url);  
		$list = array();  
		$location= $result->channel->item->description; //예보지역  
		
		$results = $location->body;  
		$i=0;
		foreach($results->data as $item){  
			if($i=="6") break;
			$weather_array[]=$item;
			/*
			$temp=$item->temp; //현재온도  
			$sky=$item->wfKor; //날씨상태(맑음,구름조금,구름많음,흐림,비,눈/비,눈)  
			$tmx =$item->tmx; // 최고 온도  
			$tmn =$item->tmn; // 최저 온도  
			$icon =$item->sky; // 날씨 아이콘 숫자  
			$wind =$item->wdKor; // 풍향
			$reh = $item->reh; // 습도  
			$hour = $item->hour; // 시간  
			$seq = $item->day; // 번호  
			$pop = $item->pop; // 강수확률  
			*/
			$i++;
		}
		return $weather_array;
	}

	#주간 단위별 날씨 ( 오전 오후 구분)
	/*
	function ju_weather($code){

		$url = "http://www.kma.go.kr/weather/forecast/mid-term-rss3.jsp?stnId=".$code;
		$allresult = simplexml_load_file($url);
		$result=$allresult->channel->item->description;
		$list = array();

		$location= $result->header->title; //예보지역

		$results = $result->body->location;
		
		foreach($results as $accc){
			foreach($accc->data as $item){
				$weather_array[]=$item;
				$city =$accc->city; //지역
				$num = $item->numEf;  //n 일후 예보
				$wdate = $item->tmEf; // 날짜
				$wformat =$item->wf; //날씨, (맑음,구름조금,구름많음,흐림,비,눈/비,눈
				$tmin = $item->tmn; //최저온도
				$tmax = $item->tmx; //최고온도
				$rainrate =$item->reliability; //신뢰도
				$province =$accc->province; // 세부지역

				$tmparr = array($num, $wdate, $wformat, $tmin, $tmax, $rainrate);

			}
		}
		return $weather_array;
	}
*/
	#주간 단위별 날씨 ( 전일 )
	function ju_weather($code){

		$url = "http://www.kma.go.kr/weather/forecast/mid-term-xml.jsp?stnId=".$code;
		$result = simplexml_load_file($url);
		$list = array();
		$location= $result->header->title; //예보지역
		$results = $result->body->location;
		$i=0;
		foreach($results->data as $item){
			if($i=="6") break;
			$weather_array[]=$item;
			/*
			$city =$accc->city; //지역
			$num = $item->numEf;  //n 일후 예보
			$wdate = $item->tmEf; // 날짜
			$wformat =$item->wf; //날씨, (맑음,구름조금,구름많음,흐림,비,눈/비,눈
			$tmin = $item->tmn; //최저온도
			$tmax = $item->tmx; //최고온도
			$rainrate =$item->reliability; //신뢰도
			$province =$accc->province; // 세부지역

			$tmparr = array($num, $wdate, $wformat, $tmin, $tmax, $rainrate);
			*/
			$i++;
		}
		return $weather_array;
	}

}
?>