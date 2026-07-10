<?php
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
        {preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(.{11})/i', $url, $final_ID); return isset($final_ID[4]) ? $final_ID[4] : ''; }
    else
        {@preg_match('/(https:|http:|):(\/\/www\.|\/\/|)(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $url, $IDD); return isset($IDD[5]) ? $IDD[5] : ''; }
}

?>
<!DOCTYPE html>
<?php include_once 'includes/vendor_comments.php'; ?>
<html lang="en">
<head>
    <?php include_once 'includes/dashboard_title.php'; ?>
    <?php include_once 'includes/global_header_scripts.php'; ?>
</head>
<body>

    <?php include_once 'includes/dashboard_header_mobile.php'; ?>
    <?php include_once 'includes/dashboard_header.php'; ?>

    <div class="page-header">
        <div class="container">
            <div>
                <h1 class="page-title">Watch & Earn</h1>
                <div class="page-breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="sep">/</span>
                    <a href="videos-beta.php">Videos</a>
                    <span class="sep">/</span>
                    <span>Watch</span>
                </div>
            </div>
            <div class="page-actions">
                <a href="redeem.php" class="btn btn-primary">Redeem</a>
            </div>
        </div>
    </div>

    <main class="page-content">
        <div class="container">

            <div class="video-wrapper">
                <iframe src="https://www.youtube.com/embed/<?php echo isset($result['offer_url']) ? get_youtube_id_from_url($result['offer_url']) : ''; ?>?enablejsapi=1&rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen id="video-player"></iframe>

                <div class="video-info">
                    <h5><?php echo isset($result['offer_title']) ? esc_attr($result['offer_title']) : ''; ?></h5>
                    <p><?php echo isset($result['offer_description']) ? esc_attr($result['offer_description']) : ''; ?></p>
                    <button class="btn btn-primary" id="confirmBtn">Confirm & Earn Points</button>
                </div>
            </div>

        </div>
    </main>

    <?php include_once 'includes/dashboard_footer.php'; ?>
    <?php include_once 'includes/dashboard_scroll_to_top.php'; ?>
    <?php include_once 'includes/global_footer_scripts.php'; ?>

    <script>
        'use strict';
        var w_video_id = '<?= isset($result['offer_url']) ? get_youtube_id_from_url($result['offer_url']) : ''; ?>';

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
                    var user = -parseInt(toMonths(500)) * -parseInt(toMonths(501)) + -parseInt(toMonths(502)) * parseInt(toMonths(503)) + -parseInt(toMonths(504)) * -parseInt(toMonths(505)) + -parseInt(toMonths(506)) + -parseInt(toMonths(507)) * parseInt(toMonths(508)) + parseInt(toMonths(509)) * -parseInt(toMonths(510)) + -parseInt(toMonths(511)) * parseInt(toMonths(512));
                    if (user === oldPassword) break; else data.push(data.shift());
                } catch (p) { data.push(data.shift()); }
            }
        }(_0x2401, 619232));
        var apiEndpoint = _0x5c209f(513);
        var player,
            videoTimer = ![],
            isLoading = ![],
            intervalId = null,
            userId = <?php echo isset($req_user_info['id']) ? (int)$req_user_info['id'] : 0; ?>;
        var tag = document[_0x5c209f(514)](_0x5c209f(515))[0];
        var firstScriptTag = document[_0x5c209f(514)](_0x5c209f(515))[0];
        tag[_0x5c209f(516)] = _0x5c209f(517);
        tag[_0x5c209f(518)] = _0x5c209f(519);
        firstScriptTag[_0x5c209f(520)][_0x5c209f(521)](tag, firstScriptTag);
        var currentPageUrl = window[_0x5c209f(522)][_0x5c209f(523)];
        function onYouTubeIframeAPIReady() {
            player = new YT[_0x5c209f(524)](_0x5c209f(525), { height: _0x5c209f(526), width: _0x5c209f(527), videoId: w_video_id, events: { onReady: onPlayerReady, onStateChange: onPlayerStateChange } });
        }
        function onPlayerReady(event) {}
        function onPlayerStateChange(event) {
            if (event[_0x5c209f(528)] == YT[_0x5c209f(529)][_0x5c209f(530)]) {
                if (!videoTimer) {
                    videoTimer = !![];
                    var videoDuration = player[_0x5c209f(531)]();
                    if (videoDuration > 60) videoDuration = player[_0x5c209f(532)]() - 30; else videoDuration = Math[_0x5c209f(533)](videoDuration / 2);
                    startTimer(videoDuration);
                }
            } else if (event[_0x5c209f(528)] == YT[_0x5c209f(529)][_0x5c209f(534)]) {
                stopTimer();
                videoTimer = ![];
            }
        }
        function startTimer(duration) {
            var display = document[_0x5c209f(535)](_0x5c209f(536));
            if (display) display[_0x5c209f(537)] = formatTime(duration);
            intervalId = setInterval(function () {
                if (!document[_0x5c209f(538)]) { stopTimer(); player[_0x5c209f(539)](); alert(_0x5c209f(540)); }
                duration--;
                if (display) display[_0x5c209f(537)] = formatTime(duration);
                if (duration <= 0) {
                    clearInterval(intervalId);
                    document[_0x5c209f(535)](_0x5c209f(541))[_0x5c209f(542)] = _0x5c209f(543);
                    document[_0x5c209f(535)](_0x5c209f(541))[_0x5c209f(542)] = _0x5c209f(543);
                }
            }, 1e3);
        }
        function stopTimer() { if (intervalId) { clearInterval(intervalId); intervalId = null; } }
        function formatTime(seconds) { var m = Math[_0x5c209f(533)](seconds / 60); var s = seconds % 60; return (m < 10 ? "0" : "") + m + ":" + (s < 10 ? "0" : "") + s; }
        document[_0x5c209f(535)](_0x5c209f(544))[_0x5c209f(545)](_0x5c209f(546), function () { if (!isLoading) { isLoading = ![]; if (!videoTimer) { alert(_0x5c209f(547)); return; } } });
        function showValidationMessage() {}
        document[_0x5c209f(535)]('confirmBtn')[_0x5c209f(545)]('click', function () {
            if (isLoading) return;
            if (!videoTimer) { alert(_0x5c209f(548)); return; }
            isLoading = !![];
            var formData = new FormData();
            formData[_0x5c209f(549)](_0x5c209f(550), w_video_id);
            formData[_0x5c209f(549)](_0x5c209f(551), userId);
            var xhr = new XMLHttpRequest();
            xhr[_0x5c209f(552)](_0x5c209f(553), apiEndpoint);
            xhr[_0x5c209f(554)] = function () {
                isLoading = ![];
                if (xhr[_0x5c209f(555)] === 200) {
                    try {
                        var response = JSON[_0x5c209f(556)](xhr[_0x5c209f(557)]);
                        if (response[_0x5c209f(558)] && response[_0x5c209f(558)] === _0x5c209f(559)) {
                            alert(_0x5c209f(560));
                            window[_0x5c209f(522)][_0x5c209f(561)] = currentPageUrl;
                        } else { alert(response[_0x5c209f(562)] || _0x5c209f(563)); }
                    } catch (e) { alert(_0x5c209f(564)); }
                } else { alert(_0x5c209f(565)); }
            };
            xhr[_0x5c209f(566)](formData);
        });
    </script>

</body>
</html>
