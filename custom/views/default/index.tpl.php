<?php
$pageTitle = $PlanetConfig->getName();
$limit = $PlanetConfig->getMaxDisplay();
$count = 0;

$fmt_full = datefmt_create(
    $PlanetConfig->getLocale(),
    IntlDateFormatter::FULL,
    IntlDateFormatter::LONG,
    'UTC',
    IntlDateFormatter::GREGORIAN
);
$fmt_short = datefmt_create(
    $PlanetConfig->getLocale(),
    IntlDateFormatter::RELATIVE_LONG,
    IntlDateFormatter::NONE,
    'UTC',
    IntlDateFormatter::GREGORIAN
);

header('Content-type: text/html; charset=UTF-8');
?><!DOCTYPE html>
<html lang="<?=$PlanetConfig->getLocale()?>" class="no-js">
<head>
    <?php include(__DIR__.'/head.tpl.php'); ?>
</head>
<body>
    <div id="page">
        <?php include(__DIR__.'/top.tpl.php'); ?>

        <div id="content">
            <?php if (0 == count($items)) : ?>
                <div class="article">
                    <h2 class="article-title">
                        <?=_g('No article', 'note de trad')?>
                    </h2>
                    <p class="article-content"><?=_g('No news, good news.')?></p>
                </div>
            <?php else : ?>
                <?php foreach ($items as $item) : ?>
                    <?php
                    $arParsedUrl = parse_url($item->get_feed()->getWebsite());
                    $host = 'from-' . preg_replace('/[^a-zA-Z0-9]/i', '-', $arParsedUrl['host']);
                    ?>
                    <div class="article <?php echo $host; ?>">
                        <h2 class="article-title">
                            <a href="<?php echo $item->get_permalink(); ?>" title="Go to original place"><?php echo $item->get_title(); ?></a>
                        </h2>
                        <p class="article-info">
                            <?php
                            $feed = $item->get_feed();
                            $infos = implode(', ', array_filter([
                                sprintf(
                                    '<datetime id="post%s" class="date" title="%s">%s</datetime>',
                                    $item->get_date('U'),
                                    datefmt_format($fmt_full, $item->get_date('U')),
                                    datefmt_format($fmt_short, $item->get_date('U')),
                                ),
                                $item->get_authors() ? strip_tags($item->get_author()->get_name()) : null,
                                sprintf('<a href="%s" class="source">%s</a>', $feed->getWebsite(), $feed->getName())
                            ]));
                            echo $infos;
                            ?>
                        </p>
                        <div class="article-content">
                            <?php echo $item->get_content(); ?>
                        </div>
                    </div>
                    <?php if (++$count == $limit) {
                        break;
                    } ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php include_once(__DIR__.'/sidebar.tpl.php'); ?>
        <?php include(__DIR__.'/footer.tpl.php'); ?>
    </div>
</body>
</html>
