<?php
include("./includes/youtubei/createRequest.php");
include("includes/config.inc.php");
if (!isset($_GET['id'])) {
    echo "Please enter a channel ID";
} else {
    $id = $_GET['id'];
    $mainResponseObject = json_decode(requestChannel($id, "about"));
    // check for some channels that are "special" in that they dont have the date as other channels would normally do.
    if (isset($mainResponseObject->contents->twoColumnBrowseResultsRenderer->tabs[4]->tabRenderer->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->channelAboutFullMetadataRenderer)) {
        $metadata = $mainResponseObject->contents->twoColumnBrowseResultsRenderer->tabs[4]->tabRenderer->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->channelAboutFullMetadataRenderer;
        $cDetails = array(
            "name" => $metadata->title->simpleText,
            "description" => $metadata->description->simpleText,
            "thumbnail" => $metadata->avatar->thumbnails[0]->url,
            "joined" => $metadata->joinedDateText->runs[1]->text,
            "rss" => "https://www.youtube.com/feeds/videos.xml?channel_id=" . $id,
        );
    } else {
        $metadata = $mainResponseObject->metadata->channelMetadataRenderer;
        $cDetails = array(
            "name" => $metadata->title,
            "description" => $metadata->description,
            "thumbnail" => $metadata->avatar->thumbnails[0]->url,
            "joined" => "N/A",
            "rss" => "https://www.youtube.com/feeds/videos.xml?channel_id=" . $id,
            "avatar" => $metadata->avatar->thumbnails[0]->url,
        );
    }

    // ok now go to the HTML section below........
?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>YouTube - Your Digital Video Repository</title>
        <link rel="icon" href="yts/imgbin/favicon.ico" type="image/x-icon">
        <link href="yts/cssbin/plain.css" rel="stylesheet" type="text/css">
    </head>

    <body>
        <?php include("includes/html/header.php");?>
        <h1 class="searchTitle"><?php echo $cDetails['name'];?>'s profile</h1>
        <div class="profileContent">
            <img class="profileThumbnail" src="<?php echo htmlspecialchars($cDetails['avatar'])?>" alt="<?php echo htmlspecialchars($cDetails['name']);?>'s profile picture">
            <p>About <?php echo $cDetails['name'];?>: </p>
            <div class="description">
                Channel description: 
                <p><?php echo htmlspecialchars($cDetails['description']);?></p>
            </div>
            <div class="joinedOn">
                Joined on: <?php echo htmlspecialchars($cDetails['joined']);?>
            </div>
            <div class="rss">
                Subscribe to <?php echo htmlspecialchars($cDetails['name']);?>'s RSS feed: <a href="<?php echo $cDetails['rss'];?>">Subscribe</a>
            </div>
        </div>
    </body>
    </html>
<?php } ?>