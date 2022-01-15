<?php
$pageTitle = $PlanetConfig->getName();
$limit = $PlanetConfig->getMaxDisplay();
$count = 0;

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
                            <?php echo strip_tags(($item->get_author()? $item->get_author()->get_name() : 'Anonymous')); ?>,
                            <?php
                            $ago = time() - $item->get_date('U');
                            //echo '<span title="'.Duration::toString($ago).' ago" class="date">'.date('d/m/Y', $item->get_date('U')).'</span>';
                            echo '<span id="post'.$item->get_date('U').'" class="date">'.$item->get_date('d/m/Y').'</span>';
                            ?>

                            |

                            <?=_g('Source:')?> <?php
                            $feed = $item->get_feed();
                            echo '<a href="'.$feed->getWebsite().'" class="source">'.$feed->getName().'</a>';
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
