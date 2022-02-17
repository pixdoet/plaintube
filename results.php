<?php

include("includes/youtubei/createRequest.php");
include("includes/config.inc.php");

if (!isset($_GET['search'])) {
    include("includes/html/noquery.php");
} else {
    $query = $_GET['search'];
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
        require('includes/html/noresults.php');
    }

?>
    <!DOCTYPE html>
    <html>

    <head>

        <title>YouTube - Broadcast Yourself.</title>

        <link rel="stylesheet" type="text/css" href="yts/cssbin/plain.css">
        <link rel="icon" href="yts/imgbin/favicon.ico" type="image/x-icon">
        <meta name="description" content="Share your videos with friends and family">
        <meta name="keywords" content="video,sharing,camera phone,video phone">

    </head>

    <body>
        <?php include("includes/html/header.php");?>
        <h1 class="searchTitle">Search results for <?php echo htmlspecialchars($query); ?></h1>
        <div class="searchContainer">
        <?php
        for ($i = $arr; $i < $loopRounds; $i++) {
            // declare vars
            // the array format used in watch.php is too annoying
            // so everything is it's own variable now! yay!
            if (isset($items[$i]->videoRenderer)) {
                $videoId = $items[$i]->videoRenderer->videoId;
                $videoTitle = $items[$i]->videoRenderer->title->runs[0]->text;
                //$videoDescription = $items[$]->videoRenderer->
                $videoThumbnail = $items[$i]->videoRenderer->thumbnail->thumbnails[0]->url;
                $videoAuthor = $items[$i]->videoRenderer->longBylineText->runs[0]->text;
                $videoViews = $items[$i]->videoRenderer->viewCountText->simpleText;
                $videoRuntime = $items[$i]->videoRenderer->lengthText->simpleText;
                $videoUploadTime = $items[$i]->videoRenderer->publishedTimeText->simpleText;
                $authorChannelId = $items[$i]->videoRenderer->longBylineText->runs[0]->navigationEndpoint->browseEndpoint->browseId;
        ?>
                <div class="moduleEntry">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr valign="top">
                                <td>
                                    <table cellspacing="0" cellpadding="0">
                                        <tbody>
                                            <tr>
                                                <td><a href="watch.php?v=<?php echo $videoId; ?>"><img src="<?php echo $videoThumbnail; ?>" class="moduleEntryThumb" width="100" height="75"></a></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </td>
                                <td width="100%">
                                    <div class="moduleEntryTitle"><a href="watch.php?v=<?php echo $videoId; ?>&search=youtube"><?php echo $videoTitle; ?></a></div>
                                    <div class="moduleEntryDetails">Added: <?php echo $videoUploadTime; ?> by <a href="profile.php?id=<?php echo $authorChannelId; ?>"><?php echo $videoAuthor; ?></a></div>
                                    <div class="moduleEntryDetails">Runtime: <?php echo $videoRuntime; ?> | Views: <?php echo $videoViews; ?></div>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php
            } elseif (isset($items[$i]->channelRenderer)) {
                $i += 1;
            }
            ?>
        <?php
        }
        ?>
        </div>
    </body>

    </html>
<?php
}
// end file
?>