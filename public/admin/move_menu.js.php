<?php // hspark
if(stristr($_SERVER["HTTP_REFERER"],$_SERVER["HTTP_HOST"])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
if ( typeof( scrollLayer ) == 'undefined' ){
	/*-------------------------------------
	 Scroll Layer Original
	-------------------------------------*/
	var scrollLayer = function ( MaxtopQuantity, MaxleftQuantity ){

		this.MaxtopQuantity = MaxtopQuantity;		// max Top
		this.MaxleftQuantity = MaxleftQuantity ;	// maxLeft( 902 )

		this.isDOM = ( document.getElementById ? 1 : 0 );
		this.isIE4 = ( ( document.all && !this.isDOM ) ? 1 : 0 );
		this.isNS4 = ( document.layers ? 1 : 0 );
		this.isNS = navigator.appName == "Netscape";

		this.timerID;
	}



	/*-------------------------------------
	 Scroll Layer Property - get Document Obj
	-------------------------------------*/
	scrollLayer.prototype.getRef = function ( id ){

		if ( this.isDOM ) return document.getElementById( id );
		if ( this.isIE4 ) return document.all[id];
		if ( this.isNS4 ) return document.layers[id];
	}



	/*-------------------------------------
	 Scroll Layer Property - action start
	-------------------------------------*/
	scrollLayer.prototype.start = function ( divid, parentDivid, Objnm ){

		this.Objnm = Objnm;

		if ( parentDivid != '' ){

			if ( this.isNS4 ) {
				this.parentDiv = document[parentDivid];
			}
			else if ( this.isDOM ) {
				this.parentDiv = this.getRef( parentDivid );
			}
		}

		if ( this.isNS4 ) {

			this.scrollingBanner = document[divid];
			this.scrollingBanner.top = window.pageYOffset + this.MaxtopQuantity;
			this.scrollingBanner.style.left = this.MaxleftQuantity;
			this.scrollingBanner.visibility = "visible";
			this.movingSlide();
		}
		else if ( this.isDOM ) {

			this.scrollingBanner = this.getRef( divid );
			this.scrollingBanner.style.top = ( this.isNS ? window.pageYOffset : document.body.scrollTop ) + this.MaxtopQuantity;
			this.scrollingBanner.style.left = this.MaxleftQuantity;
			this.scrollingBanner.style.visibility = "visible";
			this.movingSlide();
		}
	}



	/*-------------------------------------
	 Scroll Layer Property - action stop
	-------------------------------------*/
	scrollLayer.prototype.stop = function (){

		clearTimeout( this.timerID );

		if ( this.scrollingBanner != null ){

			if ( this.isNS4 ) {

				this.scrollingBanner.top = this.MaxtopQuantity;
				this.scrollingBanner.style.left = this.MaxleftQuantity;
			}
			else if ( this.isDOM ) {

				this.scrollingBanner.style.top = this.MaxtopQuantity;
				this.scrollingBanner.style.left = this.MaxleftQuantity;
			}
		}
	}



	/*-------------------------------------
	 Scroll Layer Property - moving
	-------------------------------------*/
	scrollLayer.prototype.movingSlide = function (){

		var yMenuFrom, yMenuTo, yOffset, timeoutNextCheck;

		this.parentDiv_top = 0;

		if ( this.parentDiv ){

			if ( this.isNS4 ) {
				this.parentDiv_top = this.parentDiv.top;
			}
			else if ( this.isDOM ) {
				this.parentDiv_top = this.parentDiv.offsetTop;
			}
		}

		if ( this.isNS4 ) {

			yMenuFrom	 = this.scrollingBanner.top;
			yMenuTo		 = window.pageYOffset;
		}
		else if ( this.isDOM ) {

			yMenuFrom	 = parseInt ( this.scrollingBanner.style.top, 10 );
			yMenuTo		 = ( this.isNS ? window.pageYOffset : document.body.scrollTop );
		}

		if ( yMenuTo < ( this.MaxtopQuantity + this.parentDiv_top ) ) yMenuTo = this.MaxtopQuantity;
		else yMenuTo -= this.parentDiv_top;

		timeoutNextCheck = 500;

		if ( yMenuFrom != yMenuTo ) {

			yOffset = Math.ceil( Math.abs( yMenuTo - yMenuFrom ) / 10 );
			if ( yMenuTo < yMenuFrom ) yOffset = -yOffset;
			if ( this.isNS4 ) this.scrollingBanner.top += yOffset;
			else if ( this.isDOM ) this.scrollingBanner.style.top = parseInt ( this.scrollingBanner.style.top, 10 ) + yOffset;
			timeoutNextCheck = 10;
		}

		this.timerID = setTimeout ( this.Objnm + ".movingSlide();", timeoutNextCheck );
	}



}

var scrLayer = new scrollLayer( 0 , 0 );
function scrollMove( act ){
	if ( act ){
		scrLayer.start( 'scrollingLeft', 'scrollingLeftParent', 'scrLayer' );
		{ // 버튼 이미지 변경
			document.all['menu_scroll'].src='images/leftmenu_trans.gif';
			document.all['menu_pix'].src='images/leftmenu_stop.gif';
		}
	}
	else {
		scrLayer.stop();
		{ // 버튼 이미지 변경
			document.all['menu_scroll'].src='images/leftmenu_trans.gif';
			document.all['menu_pix'].src='images/leftmenu_stop.gif';
		}
	}
}

scrollMove( 0 );
