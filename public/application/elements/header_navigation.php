<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<header class="navbar navbar-static-top documentation-header" role="banner">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="navbar-header">
                    <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".documentation-navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                    <a href="<?=DIR_REL?>/" class="header-logo"><img src="<?=$view->getThemePath()?>/images/logo.png" style="width: 160px">
                    <span><?=t('translate')?></span></a>
                </div>
                <nav class="collapse navbar-collapse documentation-navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="active">
                            <a href="<?=DIR_REL?>/teams">Translation teams</a>
                        </li>
                        <li>
                            <a href="<?=DIR_REL?>/translate">Translate</a>
                        </li>
                        <li>
                            <a href="<?=DIR_REL?>/translate-your-packages">Translate your packages</a>
                        </li>
                        <li class="hidden-xs">
                            <div class="wcbit-toggle">

                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <?php View::element('header_notifications', array(), 'concrete5_theme'); ?>
</header>
