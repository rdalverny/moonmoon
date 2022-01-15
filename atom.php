<?php
include_once(__DIR__.'/app/app.php');
include_once(__DIR__.'/app/classes/Cache.php');

if ($Planet->loadOpml($PlanetConfig->getOpmlFile()) == 0) {
    exit;
}

$Planet->loadFeeds();
$items = $Planet->getItems();
$limit = $PlanetConfig->getMaxDisplay();
$count = 0;

header('Content-Type: text/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title><?=htmlspecialchars($PlanetConfig->getName())?></title>
    <subtitle><?=htmlspecialchars($PlanetConfig->getName())?></subtitle>
    <id><?=$PlanetConfig->getUrl()?></id>
    <link rel="self" type="application/atom+xml" href="<?=$PlanetConfig->getUrl()?>atom.php" />
    <link rel="alternate" type="text/html" href="<?=$PlanetConfig->getUrl()?>" />
    <updated><?=gmdate('Y-m-d\TH:i:s\Z')?></updated>
    <author><name><?=htmlspecialchars($PlanetConfig->getName())?></name></author>
<?php $count = 0; ?>
<?php foreach ($items as $item) : ?>
    <entry>
        <title type="html"><?=htmlspecialchars($item->get_feed()->getName())?> : <?=htmlspecialchars($item->get_title())?></title>
        <id><?=htmlspecialchars($item->get_id())?></id>
        <link rel="alternate" href="<?=htmlspecialchars($item->get_permalink())?>"/>
        <published><?=$item->get_gmdate('Y-m-d\TH:i:s\Z')?></published>
        <updated><?=$item->get_gmdate('Y-m-d\TH:i:s\Z')?></updated>
        <author><name><?=($item->get_author() ? $item->get_author()->get_name() : 'anonymous')?></name></author>
        <content type="html"><![CDATA[<?=$item->get_content()?>]]></content>
    </entry>
    <?php if (++$count == $limit) {
        break;
    } ?>
<?php endforeach; ?>

</feed>
