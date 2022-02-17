<?php
// watch.php for watching videos

include("includes/youtubei/createRequest.php");

if (!isset($_GET['v'])) {
    include('includes/html/novideo.php');
} else {
    $id = $_GET['v'];

    // request player :hsuk:
    $response_object = requestPlayer($id);
    $mainResponseObject = json_decode($response_object);

    // check if video exists
    if (!isset($mainResponseObject->videoDetails->title)) {
        include('includes/novideo.php');
    } else {
        $videoDetails = array(
            "videoTitle" => $mainResponseObject->videoDetails->title,
            "videoDescription" => '<span class="redtext"><i>No description</i></span>', // due for modification later
            "videoLengthInSeconds" => $mainResponseObject->videoDetails->lengthSeconds,
            "videoViews" => $mainResponseObject->videoDetails->viewCount,
            "videoAuthor" => $mainResponseObject->microformat->playerMicroformatRenderer->ownerChannelName,
            "videoUploadDate" => $mainResponseObject->microformat->playerMicroformatRenderer->uploadDate,
            "videoRuntime" => $mainResponseObject->microformat->playerMicroformatRenderer->lengthSeconds,
            "videoThumbnail" => $mainResponseObject->microformat->playerMicroformatRenderer->thumbnail->thumbnails[0]->url,
            "authorChannelId" => $mainResponseObject->microformat->playerMicroformatRenderer->externalChannelId,
        );

        // replace description text if description exists
        if (isset($mainResponseObject->microformat->playerMicroformatRenderer->description->simpleText)) {
            $videoDetails['videoDescription'] = $mainResponseObject->microformat->playerMicroformatRenderer->description->simpleText;
        }

        // get video tags(annoying)
        if (isset($mainResponseObject->videoDetails->keywords)) {
            $tagarr = $mainResponseObject->videoDetails->keywords;
            $tagcount = sizeof($tagarr);
            if ($tagcount >= 1) {
                $tags = $tagarr;
            } else {
                $tags = array("None");
            }
        } else {
            $tagcount = 0;
        }

        // video source file
        if (isset($mainResponseObject->streamingData->formats[0]->url)) {
            // generate video tag HTML
            $videoHtml = sprintf('<video controls src="%s" class="video-player googlevideo-player" style="width: 427px; margin:center;">', $mainResponseObject->streamingData->formats[0]->url);
        } else {
            // generate error text HTML
            $videoHtml = sprintf('Video unavailable for playback. <a href="https://youtube.com/watch?v=%s">Watch on YouTube</a>', $id);
        }


?>
        <!DOCTYPE html>
        <html lang="en" simplified-interface-enabled="true">

        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <title>PlainTube - a plain youtube interface</title>
            <script type="text/javascript" src="yts/jsbin/flashobject.js"></script>
            <link rel="icon" href="yts/imgbin/favicon.ico" type="image/x-icon">
            <link rel="shortcut icon" href="yts/imgbin/favicon.ico" type="image/x-icon">
            <link href="yts/cssbin/styles.css" rel="stylesheet" type="text/css">
            <link rel="alternate" type="application/rss+xml" title="YouTube " recently="" added="" videos="" [rss]="" href="http://www.youtube.com/rss/global/recently_added.rss">
        </head>


        <body class="year-2005 backend-api-youtubei">

        </body>

<?php }
} ?>