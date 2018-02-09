//인스타그램 Data를 저장해놓을 key, value 형태의 MAP 선언
Map = function() {
	this.map = new Object();
};
Map.prototype = {
	put : function(key, value) {
		this.map[key] = value;
	},
	get : function(key) {
		return this.map[key];
	},
	containsKey : function(key) {
		return key in this.map;
	},
	containsValue : function(value) {
		for ( var prop in this.map) {
			if (this.map[prop] == value)
				return true;
		}
		return false;
	},
	isEmpty : function(key) {
		return (this.size() == 0);
	},
	clear : function() {
		for ( var prop in this.map) {
			delete this.map[prop];
		}
	},
	remove : function(key) {
		delete this.map[key];
	},
	keys : function() {
		var keys = new Array();
		for ( var prop in this.map) {
			keys.push(prop);
		}
		return keys;
	},
	values : function() {
		var values = new Array();
		for ( var prop in this.map) {
			values.push(this.map[prop]);
		}
		return values;
	},
	size : function() {
		var count = 0;
		for ( var prop in this.map) {
			count++;
		}
		return count;
	}
};

// 인스타그램 Data를 가져온다.
// tagname 이 있을경우 해쉬태그를 가져옴.
// initCount 보여지는 갯수임.

function instagramAPI(tagname,userid,initCount){
	this.tagname=tagname;
	this.userid=userid;
	this.initCount=initCount;
	this.last = false;
	this.loaded = false;
	this.first = true;
	this.instargramCount = 0;
	this.max_id =0;
	this.instaList=[];


};
instagramAPI.prototype = {

	getInstaCount : function() {
		return this.instargramCount;
	},
	changeLast : function(status) {
		this.last=status;
	},
	isLast : function() {
		return this.last;
	},
	changeLoaded : function(status) {
		this.loaded=status;
	},
	isLoaded : function() {
		return this.loaded;
	},
	getInstagramList : function() {
		this.loaded = false;
		var me = this;

		$("#more_list").hide();


		var count = this.initCount;
		if (this.tagname)
		{
			if (this.first) {
				instagram_target = "https://api.instagram.com/v1/tags/" + this.tagname + "/media/recent?count=" + count;
				this.first = false;
			} else {
				instagram_target = "https://api.instagram.com/v1/tags/" + this.tagname + "/media/recent?count=" + count + "&max_tag_id=" + this.max_id;
			}
		} else if (this.userid)
		{
			if (this.first) {
				instagram_target = "https://api.instagram.com/v1/users/" + this.userid + "/media/recent?count=" + count;
				this.first = false;
			} else {
				instagram_target = "https://api.instagram.com/v1/users/" + this.userid + "/media/recent?count=" + count + "&max_id=" + this.max_id;
			}
		} else {
			if (this.first) {
				instagram_target = "https://api.instagram.com/v1/users/self/media/recent?access_token="+access_Token+"&count=" + count;
				this.first = false;
			} else {
				instagram_target = "https://api.instagram.com/v1/users/self/media/recent?access_token="+access_Token+"&count=" + count + "&max_id=" + this.max_id;
			}
		}

		var params = {
			client_id : 'f1ada909f0104410ae2f403f185c2565'
		};
		$.ajax({
			url : instagram_target,
			data : params,
			dataType : "jsonp",
			jsonp : "callback",
			success : function(data) {
				//$list_container = $('.pic_list li');
				$list_container = $("#hhw-wrapper .list-hhg-gallery");
				//$display_container = $("#layer-slide-wrap DIV.inner .photo-list");
				$display_container = $("#tempPotoList");
				
				$msg_container = $('.insta_pic_non');
				$("#more_list").show();

				$.each(data, function(index, entry) {
					//alert("index : "+index+"\n"+"meta : "+entry["meta"]+"\n"+"code : "+entry["code"]);

					if (index == "pagination") {
						//alert("next_min_id : "+entry["next_min_id"]+"\n"+"min_tag_id : "+entry["min_tag_id"]+"\n"+"deprecation_warning : "+entry["deprecation_warning"]);
						if (typeof (entry["next_url"]) == "undefined") {
							//alert("마지막 페이지 입니다");
							$("#more_list").hide();
							me.last = true;

						} else {
							//alert(entry["next_url"]);
						}
						if (me.tagname) {
							me.max_id = entry["next_max_tag_id"]; //인스타에서 넘겨준 next_max_tag_id 를 세팅한다.
						} else {
							me.max_id = entry["next_max_id"]; //인스타에서 넘겨준 next_max_id 를 세팅한다.
						}
						if (me.max_id == 0 && me.last == true) {
							$msg_container.append("사진이 없습니다.");
							//$(" #go_first").show();
							$(" #go_first").css("display","block");
						}
					}

					if (index == "meta") {
						//alert(entry["code"]);
						if ("200" != entry["code"]) {
							/*$list_container.append("<P> [" + entry["code"] + "] "
									+ entry["error_message"] + "</P>");*/
							$msg_container.append("[" + entry["code"] + "] " + entry["error_message"]);
							$msg_container.show();
							$("#more_list").hide();
							//$(" #go_first").show();
							$(" #go_first").css("display","block");
							return;
						}
					}

					if (index == "data") {
						var imageHtml = "";
						var cnt = 0;
						var reg_date = "";
						var cut_text	="";

						$.each(data['data'], function(dataList, arrdata) {

							var map = new Map();
							map.put("link", arrdata["link"]);
							map.put("id", arrdata["id"]);
							map.put("tags", arrdata["tags"]);
							reg_date = new Date(parseInt(arrdata["created_time"]) * 1000);									
							map.put("created_time", reg_date.getFullYear()+"."+(reg_date.getMonth()+1)+"."+reg_date.getDate());
							
							$.each(arrdata, function(key, obj) {
								//console.log(key);
								if (key == "created_time") {
								}

								if (key == "comments") {
									map.put("comments_count", obj["count"]);
								}


								if (key == "caption") {
									cut_text	= obj["text"];
									cut_text	= cut_text.substring(0,60)+"...";
									map.put("text", cut_text);
									
								}

								if (key == "likes") {
									//alert(obj["count"]);
									map.put("likes_count", obj["count"]);
								}

								if (key == "images") {
									$.each(obj, function(type, image) {

										if (type == "low_resolution") {//low_resolution 306*306
											img_low = image["url"];
											map.put("images_low_resolution", image["url"]);
										}
										if (type == "thumbnail") {//thumbnail 150*150
											img_thum = image["url"];
											map.put("images_thumbnail", image["url"]);
										}
										if (type == "standard_resolution") {//standard_resolution 612*612
											img_std = image["url"];
											map.put("images_standard_resolution", image["url"]);
										}

									});
								}

								if (key == "user") {
									map.put("user_id", obj["id"]);
									map.put("user_profile_picture", obj["profile_picture"]);
									map.put("user_full_name", obj["full_name"]);
									map.put("user_tagname", obj["username"]);

								}


							});

							var isHide = false;
							
							if(!isHide){
								if (me.tagname) {
									//var html = "<div class='insta_row'><a href='#ly_pop' onClick=\"javascript:instaDetailPop('"+ map.get("images_standard_resolution")	+ "','"+ map.get("user_full_name") + "','"+ map.get("user_profile_picture") + "','"+ map.get("likes_count") + "','"+ map.get("created_time") + "','"+ escape(map.get("text")) + "')\" class='insta_pic'><img src='"+ map.get("images_low_resolution")	+ "' alt='' /><span class='like_mark'>♥ "+  map.get("likes_count") + "</span></a></div>"; 
									var html = "<li><a href='#'><img src='"+ map.get("images_low_resolution")	+ "' alt='' ></a></li>";
									var html2 = "";
									html2 += "<li>";
									html2 += "<div class='layer-tags-contents'>";
									html2 += "	<div class='content-pic'><img src='" + map.get("images_standard_resolution") + "' alt=''></div>";
									html2 += "		<div class='content-info'>";
									html2 += "			<div class='title'><span>#</span>" +  me.tagname + "</div>";
									html2 += "				<p class='user-pic'><img src='" + map.get("user_profile_picture") + "' alt=''></p>";
									html2 += "					<span class='user-id'>" + map.get("user_full_name") + "</span>";
									html2 += "					<span class='reg-date'>(" +  map.get("created_time") +")</span>";
									html2 += "						<div class='ment-area'>";
									html2 += "							" + map.get("text");
									html2 += "						</div>";
									html2 += "						<span class='likes'>" + map.get("likes_count") + " Likes</span>";
									html2 += "					</div>";
									html2 += "				</div>";
									html2 += "			</li>";
									
									

								} else if (me.userid) {
									//var html = "<div class='insta_row'><a href='" + map.get("link")+ "' class='insta_pic' target='_blank'><img src='"+ map.get("images_standard_resolution")	+ "' alt='' /></a><a href='" + map.get("link")+ "' target='_blank'><div class='comment'>"+map.get("text")+"</div></a><a href='https://instagram.com/" + map.get("user_tagname")+ "/' target='_blank'><div class='info'><img alt='' src='"+ map.get("user_profile_picture")	+"' width=32 height=32><p><strong>"+ map.get("user_full_name") + "</strong><br>"+ map.get("created_time") + "</p></div></a></div>";
									var html = "<li><a href='" + map.get("link")+ "' target='_blank' ><img src='" + map.get("images_standard_resolution") + "' alt='" + map.get("text") + "'></a></li>";
									var html2 = "";
									html2 += "<li>";
									html2 += "<div class='layer-tags-contents'>";
									html2 += "	<div class='content-pic'><img src='" + map.get("images_standard_resolution") + "' alt=''></div>";
									html2 += "		<div class='content-info'>";
									html2 += "			<div class='title'><span>#</span>TAGS VIEW</div>";
									html2 += "				<p class='user-pic'><img src='" + map.get("user_profile_picture") + "' alt=''></p>";
									html2 += "					<span class='user-id'>" + map.get("user_full_name") + "</span>";
									html2 += "					<span class='reg-date'>(" +  map.get("created_time") +")</span>";
									html2 += "						<div class='ment-area'>";
									html2 += "							" + map.get("text");
									html2 += "						</div>";
									html2 += "						<span class='likes'>" + map.get("likes_count") + " Likes</span>";
									html2 += "					</div>";
									html2 += "				</div>";
									html2 += "			</li>";
									
								} else {
									//var html = "<div class='insta_row'><a href=\"javascript:insertPic('"+ map.get("images_thumbnail")	+ "','"+ map.get("images_standard_resolution")	+ "','" + map.get("link")+ "')\" class='insta_pic'><img src='"+ map.get("images_thumbnail")	+ "' alt='' /></a></div>";
									var html = "<li><a href='#'><img src='"+ map.get("images_thumbnail") + "' alt=''></a></li>";
									var html2 = "";
									html2 += "<li>";
									html2 += "<div class='layer-tags-contents'>";
									html2 += "	<div class='content-pic'><img src='../img/test/@insta_layer_big01.jpg' alt=''></div>";
									html2 += "		<div class='content-info'>";
									html2 += "			<div class='title'><span>#</span>TAGS VIEW</div>";
									html2 += "				<p class='user-pic'><img src='../img/test/@insta_user.jpg' alt=''></p>";
									html2 += "					<span class='user-id'>vogle girl</span>";
									html2 += "					<span class='reg-date'>(Sep 15)</span>";
									html2 += "						<div class='ment-area'>";
									html2 += "							<p>오늘 하루도 수고많으셨답니다.</p>";
									html2 += "							<p><a>#오야니</a><a>#ORYANY</a></p>";
									html2 += "						</div>";
									html2 += "						<span class='likes'>33 Likes</span>";
									html2 += "					</div>";
									html2 += "				</div>";
									html2 += "			</li>";
								}

								$list_container.append(html);
								$display_container.append(html2);
							}
							map.clear();
						});
					}
					me.loaded = true;
					$("#load_message").hide();

				});

				/*if($('.slides li a').length < 18 ){//제외된 이미지가 많으면 한번더 호출 해준다.
					accessInstagramAPI();
				}*/
			},
			error : function(data) {
				me.loaded = true;
				//alert("인스타그램과 통신 실패하였습니다.");
			}
		});
	}


};

//  DB에 저장된 인스타그램 Data를 팝업으로 상세보기한다.
// thumPic 작은사진임.
// bigPic 큰사진임.
// link 인스타그램 링크임.
function instaDetailPop(img, unm, uphoto, like,regdate, txt) {
	
	$('#detail_photo').attr('src', img);
	$('#profile_photo').attr('src', uphoto);
			
	$('#uname').html(unm);
	$('#regdate').html(regdate);
	$('#utext').html(unescape(txt));
	if(!like)
	{
		like=0;
	}
	$('#likes').html(like+" Likes");

	$(".dimmed").show();
	$(".ly_pop").show();
}

function removeModal(){
	
	$('#detail_photo').attr('src','');
	$('#profile_photo').attr('src', '');
			
	$('#uname').html('');
	$('#regdate').html('');
	$('#utext').html('');

	$('#likes').html("0 Likes");

	$(".dimmed").hide();
	$(".ly_pop").hide();
}