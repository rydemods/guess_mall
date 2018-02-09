<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Pragma" content="no-cache"> 
    <meta http-equiv="Cache-Control" content="No-Cache"> 
    <title>결제 암호화 프로그램 설치</title>
    <script type="text/javascript" src="../static/js/jquery-1.12.0.min.js"></script>
    <script>
        $(document).ready( function(){
            paygate_down();
        });

        function paygate_down(){
            // IE 일경우 기존 로직을 타게끔
            if ((navigator.userAgent.indexOf('MSIE') > 0) || (navigator.userAgent.indexOf('Trident/7.0') > 0))
            {
               location.href = 'https://pay.kcp.co.kr/plugin_new/file/KCPPayUXSetup.exe';
            } 
            // 그 외 브라우져에서는 체크로직이 변경됩니다.
            else
            {
                location.href = 'https://pay.kcp.co.kr/plugin_new/file/k5/KCPPaymentPluginSetup.exe';
            }
        }

        function paygate_close(){
            window.opener.location.reload();
            window.close();
        }

    </script>
    <style>
        /*기본스타일*/
        body {margin:0px; padding:0px; overflow:auto; font-family:ng; font-size:12px; color:#aaa;}
        img {border:none; padding:0px; margin:0px; font-size:0px; line-height:0px; vertical-align:top}
        td	{color:#939292; font-size:12px;line-height:1.2}
        ul,li {list-style:none; margin:0px; padding:0px; }
        fieldset, form, label, legend {margin:0px; padding:0px; outline:0px; border:0px; vertical-align:baseline; background:transparent; }
        legend  {visibility:hidden;height:0px;font-size:0px;}
        h1,h2,h3,h4,h5,h6,div,p,span,em,dl,dd,dt,figure {padding:0px; margin:0px; line-height:1.2; text-decoration:none}
        em {font-style:normal}
        button {margin:0; padding:0; outline:0; border:none; background-color:transparent;font-size:12px; color:#aaa; font-family:ng; cursor:pointer}
        button > * {position:relative;}


        input,textarea {font-family:ng; font-size:12px; color:#aaa;}

        input:-webkit-autofill {-webkit-box-shadow: 0 0 0px 1000px white inset;}

        A:link,   
        A:hover,
        A:visited
        {color:#4b4b4b; text-decoration:none; }

        *::selection {background:#808080; color:#fff;}

        div:after {content:""; clear:both; display:block}

        h3 {
            display: block;
            font-size: 1.17em;
            /* -webkit-margin-before: 1em; */
            -webkit-margin-after: 1em;
            -webkit-margin-start: 0px;
            -webkit-margin-end: 0px;
            font-weight: bold;
        }

        button {
            margin: 0;
            padding: 0;
            outline: 0;
            border: none;
            background-color: transparent;
            font-size: 12px;
            color: #aaa;
            font-family: ng;
            cursor: pointer;
        }

        .layer-dimm-wrap {
            position:fixed; width:100%; height:100%; top:0; left:0;
        }
        .layer-dimm-wrap .dimm-bg {
            position:absolute; width:100%; height:100%; 
        }
        .layer-dimm-wrap .layer-inner {
            position:absolute; border:1px solid #515151; background:#fff; box-sizing:border-box;
        }
        .layer-dimm-wrap .layer-inner .layer-title {
            height:38px; background:#515151 url(../static/img/common/layer_pop_title.png) center no-repeat; 
        }
        .layer-dimm-wrap .layer-inner .btn-close {
            position:absolute; top:8px; right:8px; width:23px; 
            height:23px; background:url(../static/img/btn/btn_layer_close.png) center no-repeat; text-indent:-9999px; cursor:pointer
        }

        .layer-content {color:#868686;}
        .layer-content .btn-place { text-align: center;  margin: 5px; }
        .layer-content .btn-place .btn-dib {margin:0 3px; padding:0; width:110px;}
        .layer-content h5{font-family:"lsb","ngb"; color:#4b4b4b;}

        .btn-dib-function {width:117px; margin:0 3px; display:inline-block; border:1px solid #424242; background:#424242;  vertical-align:middle; cursor:pointer; }
        .btn-dib-function span {
            display:block; _padding:0 15px; height:22px; border:1px solid #666; color:#fff !important; 
            text-align:center;   font:13px/22px "ls","ng";  box-sizing:border-box;
        }
        .btn-dib-function.line {background:#fff; border-color:#aaa;}
        .btn-dib-function.line span {border-color:#fff; color:#424242 !important;}
        .CLS_MSG { padding: 10px 20px; }
    </style>
</head>
<body>
    <div class="layer-dimm-wrap">
        <div class="dimm-bg"></div>
            <div class="layer-inner"> <!-- layer-class 부분은 width,height, - margin 값으로 구성되며 클래스명은 자유 -->
                <h3 class="layer-title"></h3>
                <button type="button" class="btn-close" onClick='javascript:window.close();'>창 닫기 버튼</button>
                <div class="layer-content">
                    <div class="CLS_MSG">
                        고객님의 안전한 결제를 위해 결제 정보를 암호화하는 프로그램을 확인(설치)하는 중입니다.<br>
                        1. 페이지 상단의 알림표시줄이 나타났을 경우 알림표시줄에서 마우스 오른쪽 버튼을 눌러 <br />
                        <span class="red bold">"ActiveX 컨트롤 설치"</span>를 선택하여 주십시오. <br />
                        2. <u>보안경고 창이 나타나면 <span class="red bold">"설치"</span> 또는 <span class="red bold">"예"</span> 버튼을 눌러
                        설치를 진행하여 주십시오.</u> <br />
                        &nbsp;&nbsp;&nbsp;통신 환경에 따라 2-3초에서 수분이 걸릴수도 있습니다. <br />
                        &nbsp;<span class="bold">프로그램이 자동으로 설치가 되지 않는 경우</span><br>
                        1. 다운로드를 눌러 설치 파일을 다운로드하여 주십시오.<br />
                        2. 다운받은 프로그램을 실행하시면 설치가 진행됩니다.<br />
                        3. 설치가 완료된  새로고침 버튼을 눌러 새로고침을 하면<br />
                        &nbsp;&nbsp;&nbsp;다음 결제 페이지로 이동합니다.
                    </div>
                    <div class="btn-place">
                        <button class="btn-dib-function" type="button" onclick="javascript:paygate_down();">
                            <span>다운로드</span>
                        </button>
                        <button class="btn-dib-function" type="button" onclick="javascript:paygate_close();">
                            <span>새로고침</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
