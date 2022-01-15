<header id="header">
    <h1 id="top">
        <a href="<?php echo $PlanetConfig->getUrl(); ?>"><?php echo $PlanetConfig->getName(); ?></a>
        <?php if ($pageRole == 'archive') : ?>
            &middot; <?=_g('All Headlines') ?>
        <?php endif; ?>
    </h1>
</header>