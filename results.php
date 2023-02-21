<?php

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

include_once("includes/youtubei/createRequest.php");
include_once("includes/config.inc.php");

if (!isset($_GET['q'])) {
    include("includes/html/noquery.php");
} else {
    $query = $_GET['q'];
    $mainResponseObject = json_decode(requestSearch($query));

    // start parsing search results (creepy)
    if (isset($mainResponseObject->contents->twoColumnSearchResultsRenderer->primaryContents->sectionListRenderer->contents[0]->itemSectionRenderer->contents)) {
        $items = $mainResponseObject->contents->twoColumnSearchResultsRenderer->primaryContents->sectionListRenderer->contents[0]->itemSectionRenderer->contents;
        // check if this is "did you mean"
        $arr = 0;
        if (isset($items[0]->didYouMeanRenderer)) {
            // start from element 1
            $arr = 1;
        }
        // also skip channels
        elseif (isset($items[0]->channelRenderer)) {
            $arr = 1;
        }
        // we wanna find out how many results are there
        // so that we can prevent looping too much if there's
        // less than desired amount of results
        if (sizeof($items) <= $resultsCount) {
            $loopRounds = sizeof($items);
        } else {
            $loopRounds = $resultsCount;
        }
        // ok we done here now go to the HTML section below...

    } else {
        print_r("annoting");
    }

    // create the result array
    $resarr = [];

    for ($i = $arr; $i < $loopRounds; $i++) {
        // declare vars
        // the array format used in watch.php is too annoying
        // so everything is it's own variable now! yay!
        // print_r($items);
        if (isset($items[$i]->videoRenderer)) {
            $videoId = $items[$i]->videoRenderer->videoId;
            $videoTitle = $items[$i]->videoRenderer->title->runs[0]->text;
            //$videoDescription = $items[$]->videoRenderer->
            $videoThumbnail = $items[$i]->videoRenderer->thumbnail->thumbnails[0]->url;
            $videoAuthor = $items[$i]->videoRenderer->longBylineText->runs[0]->text;
            $videoViews = $items[$i]->videoRenderer->viewCountText->simpleText;
            $videoRuntime = $items[$i]->videoRenderer->lengthText->simpleText;
            $videoUploadTime = "N/A";
            $authorChannelId = $items[$i]->videoRenderer->longBylineText->runs[0]->navigationEndpoint->browseEndpoint->browseId;

            if (isset($items[$i]->videoRenderer->publishedTimeText->simpleText)) {
                $videoRuntime = $items[$i]->videoRenderer->publishedTimeText->simpleText;
            }
            // write results into array
            $temp =
                array(
                    "videoTitle" => $videoTitle,
                    "videoId" => $videoId,
                    "videoThumbnail" => $videoThumbnail,
                    "videoAuthor" => $videoAuthor,
                    "videoViewCount" => $videoViews,
                    "videoUploadTime" => $videoUploadTime,
                    "authorChannelId" => $authorChannelId,
                    "videoRuntime" => $videoRuntime
                );
            array_push($resarr, $temp);
        } elseif (isset($items[$i]->channelRenderer)) {
            $i += 1;
        }
    }
    echo $twig->render(
        "search.html.twig",
        [
            "searchResults" => $resarr,
            "query" => $query
        ]
    );
}
