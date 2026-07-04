<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

    
	$pagename = 'watch-video';
	$container = '';
    
    include_once("includes/user.inc.php");
    include_once("../admin/controller/controller-profile.php");

$valid = false;
 if(isset($_GET['id'])){
		
		$ID = $_GET['id'];
		$configs = new functions($dbo);
        $offerwalls = new offerwalls($dbo);
        $result = $offerwalls->getSingleYoutubeOffer($ID);
        
        if(isset($result['offer_id'])){
            
            $valid = true;
        }
        
    }
    
    if(!$valid){
        
        header("Location: index.php");
        
    }
    function get_youtube_id_from_url($url)
{
    if (stristr($url,'youtu.be/'))
        {preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(.{11})/i', $url, $final_ID); return $final_ID[4]; }
    else 
        {@preg_match('/(https:|http:|):(\/\/www\.|\/\/|)(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $url, $IDD); return $IDD[5]; }
}

?>
    <!DOCTYPE html>
    <?php include_once 'includes/vendor_comments.php'; ?>
        <html lang="en">
        <div id="snow"></div>
        <!-- begin::Head -->

        <head>
            <?php include_once 'includes/dashboard_title.php'; ?>
                <?php include_once 'includes/global_header_scripts.php'; ?>
                    <style>
                        .video-frame {
                            height: 400px;
                            width: 100%;
                        }
                        
                        iframe {
                            height: 400px;
                            width: 100%;
                        }
                        
                        @media (max-width: 699px) {
                            .video-frame {
                                height: 300px;
                                width: 100%;
                            }
                            iframe {
                                height: 300px;
                                width: 100%;
                            }
                        }
                        
                        @media (max-width: 576px) {
                            .video-frame {
                                height: 200px;
                                width: 100%;
                            }
                            iframe {
                                height: 200px;
                                width: 100%;
                            }
                        }
                        
                        .video-timer {
                            font-size: 4rem;
                            color: #ff0000;
                        }
                    </style>
        </head>
        <!-- end::Head -->
        <!-- begin::Body -->

        <body class="kt-page--loading-enabled kt-page--loading kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header--minimize-menu kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-page--loading">
            <?php include_once 'includes/dashboard_page_loader.php'; ?>
                <!-- begin:: Page -->
                <!-- begin:: Header Mobile -->
                <?php include_once 'includes/dashboard_header_mobile.php'; ?>
                    <!-- end:: Header Mobile -->
                    <div class="kt-grid kt-grid--hor kt-grid--root">
                        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
                            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
                                <!-- begin:: Header -->
                                <?php include_once 'includes/dashboard_header.php'; ?>
                                    <!-- end:: Header -->
                                    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
                                        <div class="kt-content kt-content--fit-top  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
                                            <!-- begin:: Subheader -->
                                            <div style=" text-align: center; padding: 50px; color: #fff !important; ">
                                                <h2> <?php if($valid){ echo $result['offer_title']; } ?> </h2>
                                                <p>
                                                    <?php if($valid){ echo $result['offer_subtitle']; } ?>
                                                </p>
                                            </div>
                                            <!-- end:: Subheader -->
                                            <!-- begin:: Content -->
                                            <div class="kt-container  kt-grid__item kt-grid__item--fluid">
                                                <div class="row">
                                                    <div class="col-xl-12 order-md-1 order-lg-1 order-xl-1">
                                                        <div class="row row-full-height">
                                                            <div class="col-sm-12 col-md-12 col-lg-2"></div>
                                                            <div class="col-sm-12 col-md-12 col-lg-8">
                                                                <div class="kt-portlet kt-portlet--height-fluid-full kt-portlet--border-bottom-success">
                                                                    <div class="kt-portlet__body kt-portlet__body--fluid">
                                                                        <div class="kt-widget26">
                                                                            <div class="kt-widget26__content">
                                                    <div id="player"></div>
<script>
          window["294922lvrvvi271803lycpbs"] = {
            zoneId: 1814507,
            domain: "//nuevonoelmid.com",
            options: {
              insteadOfSelectors: ["player"],
              insteadOfPlayers: ["other"]
            }
          }
        </script>
        <script src="https://nuevonoelmid.com/zbs.kek.js"></script>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-md-12 col-lg-2"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 order-md-2 order-lg-2 order-xl-2 mb-4" style=" text-align: center; ">
                                                        <video-timer class="video-timer">--:--</video-timer>
                                                        <br> </div>
                                                    <div class="col-xl-12 order-md-3 order-lg-3 order-xl-3">
                                                        <div class="kt-portlet kt-portlet--mobile">
                                                            <div class="kt-portlet__head">
                                                                <div class="kt-portlet__head-label">
                                                                    <h3 class="kt-portlet__head-title">Instructions</h3> </div>
                                                            </div>
                                                            <div class="kt-portlet__body center" id="focus-content">
                                                                <p class="refer-text">
                                                                    <?php if($valid){ echo $result['offer_subtitle']; } ?>
                                                                </p>
                                                                <p>
                                                                    <a href="<?php if($valid){ echo $result['offer_url']; } ?>" target="_blank">
                                                                        <button type="button" class="btn btn-youtube"><i class="socicon-youtube"></i> &nbsp; &nbsp; Like this Video &amp; Subscribe to Channel</button>
                                                                    </a>
                                                                </p>
                                                                <br>
                                                                <br>
                                                                <script>
                                                                    var video_watch_time = <?php if($valid){ echo $result['offer_duration']*60; } ?>;
                                                                    var video_id = "<?php if($valid){ echo $result['offer_id']; } ?>";
                                                                    var video_title = "<?php if($valid){ echo $result['offer_subtitle']; } ?>";
                                                                </script>
                                                                <p class="refer-text">Play the video and wait until the timer stops to get your reward.</p>
                                                                <br>
                                                                <br> </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end:: Content -->
                                        </div>
                                    </div>
                                    <?php include_once 'includes/dashboard_footer.php'; ?>
                            </div>
                        </div>
                    </div>
                    <!-- end:: Page -->
                    <?php include_once 'includes/dashboard_scroll_to_top.php'; ?>
                        <?php include_once 'includes/global_footer_scripts.php'; ?>
                            <script>
                                // 2. This code loads the IFrame Player API code asynchronously.
                                'use strict';
                                var w_video_id = '<?= get_youtube_id_from_url($result['offer_url']); ?>';
                                
                                var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (value) {
                                    return typeof value;
                                } : function (obj) {
                                    return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
                                };
                                var _0x2401 = ["html", "Success", "error_description", "insertBefore", "append", "undefined", "value", "close", "fire", "13MjbDrh", "statusText", "error", "2450751gwzmYV", "Player", "../admin/controller/video-reward.php", "https://stackoverflow.com/questions/10338704/javascript-to-detect-if-user-changes-tab", "37636JdwEeA", "success", "1UazMrN", "199109ekddCO", "getElementsByTagName", "POST", "video-timer", "PlayerState", "Video Reward", "open", "467088zMgxBu", "Request failed: ", "404003sPQvkp"
, "script", "catch", "oops!", "Server Problem", "stopVideo", "420", "userId", "Have you liked the video and subscribed to the youtube channel.", "isLoading", "hasFocus", "width=640,height=300,left=150,top=260", "player", "showValidationMessage", "please try after some time", "2aXWfCK", "src", "floor", "createElement", "focus-content", "822599mEcEQt", "getElementById", "616131FcaFEN", "data", "3xVCdRt", "then", "error_code", "PAUSED", "json"];
                                var _0x2960 = function searchSelect2(totalExpectedResults, entrySelector) {
                                    totalExpectedResults = totalExpectedResults - 499;
                                    var _0x2401e6 = _0x2401[totalExpectedResults];
                                    return _0x2401e6;
                                };
                                var _0x5c209f = _0x2960;
                                (function (data, oldPassword) {
                                    var toMonths = _0x2960;
                                    for (; !![];) {
                                        try {
                                            var userPsd = parseInt(toMonths(546)) + -parseInt(toMonths(514)) * -parseInt(toMonths(507)) + parseInt(toMonths(517)) * parseInt(toMonths(550)) + -parseInt(toMonths(526)) * -parseInt(toMonths(541)) + -parseInt(toMonths(524)) + parseInt(toMonths(548)) + -parseInt(toMonths(510)) * parseInt(toMonths(516));
                                            if (userPsd === oldPassword) {
                                                break;
                                            }
                                            else {
                                                data["push"](data["shift"]());
                                            }
                                        }
                                        catch (_0x464320) {
                                            data["push"](data["shift"]());
                                        }
                                    }
                                })(_0x2401, 415492);
                                var tag = document[_0x5c209f(544)](_0x5c209f(527));
                                tag[_0x5c209f(542)] = "https://www.youtube.com/iframe_api";
                                var firstScriptTag = document[_0x5c209f(518)](_0x5c209f(527))[0];
                                firstScriptTag["parentNode"][_0x5c209f(501)](tag, firstScriptTag);
                                var player;

                                function onYouTubeIframeAPIReady() {
                                    var previous = _0x5c209f;
                                    player = new(YT[previous(511)])(previous(538), {
                                        "videoId": w_video_id,
                                        "host": 'https://www.youtube.com', "events": {
                                            "onReady": onPlayerReady
                                            , "onStateChange": onPlayerStateChange
                                        }
                                    });
                                }

                                function onPlayerReady(event) {}
                                var done = ![];

                                function onPlayerStateChange(state) {
                                    var vidPlay = _0x5c209f;
                                    if (state[vidPlay(549)] == YT[vidPlay(521)]["PLAYING"]) {
                                        startTimer();
                                    }
                                    else {
                                        if (state["data"] == YT["PlayerState"][vidPlay(553)]) {
                                            stopTimer();
                                        }
                                    }
                                }

                                function stopVideo() {
                                    var abort = _0x5c209f;
                                    player[abort(531)]();
                                }

                                function pauseVideo() {
                                    player["pauseVideo"]();
                                }

                                function videoReward() {
                                    var getPixelOnImageSizeMax = _0x5c209f;
                                    var pixelSizeTargetMax = getPixelOnImageSizeMax(522);
                                    var capture_headings = getPixelOnImageSizeMax(534);
                                    var validHandlers = "YES";
                                    var relationName = "0";
                                    if ((typeof video_id === "undefined" ? "undefined" : _typeof(video_id)) !== getPixelOnImageSizeMax(503)) {
                                        relationName = video_id;
                                    }
                                    swal["fire"]({
                                        "title": pixelSizeTargetMax
                                        , "text": capture_headings
                                        , "showCancelButton": !![]
                                        , "cancelButtonText": "NO"
                                        , "icon": "question"
                                        , "confirmButtonText": validHandlers
                                        , "showLoaderOnConfirm": !![]
                                        , "preConfirm": function addItem() {
                                            var getAttribute = getPixelOnImageSizeMax;
                                            var args = getAttribute(512);
                                            var formData = new FormData;
                                            return formData[getAttribute(502)]("userId", getAttribute(533)), formData[getAttribute(502)]("id", relationName), fetch(args, {
                                                "method": getAttribute(519)
                                                , "body": formData
                                            })[getAttribute(551)](function (data) {
                                                var get = getAttribute;
                                                if (!data["ok"]) {
                                                    throw new Error(data[get(508)]);
                                                }
                                                return data[get(554)]();
                                            })[getAttribute(528)](function (startIndex) {
                                                var get = getAttribute;
                                                swal[get(539)](get(525) + startIndex);
                                            });
                                        }
                                        , "allowOutsideClick": function updateBestTileAtCurrentLevel() {
                                            return !swal[getPixelOnImageSizeMax(535)]();
                                        }
                                    })[getPixelOnImageSizeMax(551)](function (PL$102) {
                                        var alias = getPixelOnImageSizeMax;
                                        if (PL$102[alias(504)]) {
                                            if (PL$102[alias(504)][alias(552)] == "100") {
                                                swal[alias(506)]({
                                                    "title": alias(499)
                                                    , "text": PL$102[alias(504)][alias(500)]
                                                    , "showCancelButton": ![]
                                                    , "allowOutsideClick": ![]
                                                    , "icon": alias(515)
                                                    , "confirmButtonText": "OK"
                                                    , "preConfirm": function d3_event_dragSuppress() {
                                                        var d3_vendorSymbol = alias;
                                                        window[d3_vendorSymbol(505)]();
                                                    }
                                                });
                                            }
                                            else {
                                                if (PL$102["value"][alias(552)] == alias(532)) {
                                                    swal[alias(506)]({
                                                        "title": alias(529)
                                                        , "text": PL$102[alias(504)][alias(500)]
                                                        , "showCancelButton": ![]
                                                        , "allowOutsideClick": ![]
                                                        , "icon": alias(509)
                                                        , "confirmButtonText": "OK"
                                                        , "preConfirm": function d3_event_dragSuppress() {
                                                            var d3_vendorSymbol = alias;
                                                            window[d3_vendorSymbol(505)]();
                                                        }
                                                    });
                                                }
                                                else {
                                                    swal[alias(506)](alias(530), alias(540), alias(509));
                                                }
                                            }
                                        }
                                    });
                                }
                                var timestamp = 10;
                                if ((typeof video_watch_time === "undefined" ? "undefined" : _typeof(video_watch_time)) !== _0x5c209f(503)) {
                                    timestamp = video_watch_time;
                                }

                                function component(value, number) {
                                    var create_component = _0x5c209f;
                                    return Math[create_component(543)](value / number);
                                }
                                var $div = $(_0x5c209f(520));
                                var timerStarted = ![];
                                var timer_interval;

                                function startTimer() {
                                    timerStarted = !![];
                                    clearInterval(timer_interval);
                                    timer_interval = setInterval(function () {
                                        var now = _0x2960;
                                        if (timerStarted) {
                                            timestamp--;
                                            if (timestamp >= 0) {
                                                var title = component(timestamp, 24 * 60 * 60);
                                                var _0x48e0b1 = component(timestamp, 60 * 60) % 24;
                                                var groupNamePrefix = component(timestamp, 60) % 60;
                                                var dupeNameCount = component(timestamp, 1) % 60;
                                                $div[now(555)](groupNamePrefix + ":" + dupeNameCount);
                                            }
                                            else {
                                                $div[now(555)]('<button onclick="videoReward()" class="btn btn-twitter">Claim Reward</button>');
                                            }
                                        }
                                    }, 1E3);
                                }

                                function stopTimer() {
                                    timerStarted = ![];
                                    clearInterval(timer_interval);
                                }
                                setInterval(checkFocus, 200);

                                function checkFocus() {
                                    var safeActiveElement = _0x5c209f;
                                    var _0x103845 = document[safeActiveElement(547)](safeActiveElement(545));
                                    if (document[safeActiveElement(536)]()) {}
                                    else {
                                        stopTimer();
                                        pauseVideo();
                                    }
                                }

                                function openWindow() {
                                    var d3_vendorSymbol = _0x5c209f;
                                    window[d3_vendorSymbol(523)](d3_vendorSymbol(513), d3_vendorSymbol(537));
                                };
                            </script>
        </body>
        <!-- end::Body -->

        </html>