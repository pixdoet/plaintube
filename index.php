<?php
include_once("./includes/youtubei/browse.php");

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);
$response_object = requestBrowse("FEwhat_to_watch");
$response = json_decode($response_object);

function homepageFeed($number, $response)
{
    $feedobj = $response
        ->contents
        ->twoColumnBrowseResultsRenderer
        ->tabs[0]
        ->tabRenderer
        ->content
        ->richGridRenderer
        ->contents[$number];
    //print_r($feedobj);
    return $feedobj;
}

$render_array = [];

for ($i = 0; $i < 10; $i++) {
    // check if box is video 
    $obj = homepageFeed($i, $response);
    if (!isset($obj->richItemRenderer->content->videoRenderer)) {
        $obj = homepageFeed($i += 1, $response);
    } else {
        // create details array
        $obj_accessor = $obj->richItemRenderer->content->videoRenderer;
        $contents = array(
            "videoTitle" => $obj_accessor->title->runs[0]->text,
            "videoThumbnail" => $obj_accessor->thumbnail->thumbnails[0]->url,
            "videoId" => $obj_accessor->videoId,
            "videoDescription" => "<i>No description</i>",
            // "videoDescription" => $obj_accessor->descriptionSnippet->runs[0]->text,
            "videoRuntime" => $obj_accessor->lengthText->simpleText,
            "videoViewCount" => $obj_accessor->viewCountText->simpleText,
            "videoAuthor" => $obj_accessor->shortBylineText->runs[0]->text,
            "videoUploadDate" => $obj_accessor->publishedTimeText->simpleText,
            "authorChannelId" => $obj_accessor->shortBylineText->runs[0]->navigationEndpoint->browseEndpoint->browseId,
        );
        // check for videos without description
        if (isset($obj_accessor->descriptionSnippet->runs[0]->text)) {
            $contents["videoDescription"] = $obj_accessor->descriptionSnippet->runs[0]->text;
        }
        array_push($render_array, $contents);
    }
}

// print_r($render_array);

echo $twig->render(
    'index.html.twig',
    [
        "entries" => $render_array,
    ]
);
