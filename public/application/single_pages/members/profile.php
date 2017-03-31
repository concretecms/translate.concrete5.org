<?php
use Concrete\Core\Authentication\AuthenticationType;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\User\UserInfo $profile */

$dh = Core::make('helper/date');
/* @var Concrete\Core\Localization\Service\Date $dh */

$c5ProfileURL = null;
try {
    $communityAuthentication = AuthenticationType::getByHandle('community');
    if ($communityAuthentication->isEnabled()) {
        $c5ProfileURL = $communityAuthentication->getController()->getConcrete5ProfileURL($profile);
    }
} catch (Exception $x) {
}

?>
<div id="ccm-profile-header">
    <div id="ccm-profile-avatar">
        <?= $profile->getUserAvatar()->output() ?>
    </div>
    <h1><?= $profile->getUserName() ?></h1>
    <div id="ccm-profile-controls">
        <?php
        if ($canEdit) {
            ?>
            <div class="btn-group">
                <a href="<?= $view->url('/account/edit_profile') ?>" class="btn btn-sm btn-default"><i class="fa fa-cog"></i> <?= t('Edit') ?></a>
                <a href="<?= $view->url('/') ?>" class="btn btn-sm btn-default"><i class="fa fa-home"></i> <?= t('Home') ?></a>
            </div>
            <?php
        } else {
            ?><div class="btn-group">
                <?php
                if ($c5ProfileURL !== null) {
                    ?><a href="<?= h($c5ProfileURL) ?>" class="btn btn-sm btn-default" target="_blank"><i class="fa-user fa"></i> <?= t('View concrete5 profile') ?></a><?php
                }
                if ($profile->getAttribute('profile_private_messages_enabled')) {
                    ?><a href="<?= $view->url('/account/messages', 'write', $profile->getUserID()) ?>" class="btn btn-sm btn-default"><i class="fa-envelope fa"></i> <?= t('Send private message') ?></a><?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<div id="ccm-profile-statistics-bar">
    <div class="ccm-profile-statistics-item">
        <i class="icon-time"></i> <?= t(/*i18n: %s is a date */'Joined on %s', $dh->formatDate($profile->getUserDateAdded(), true)) ?>
    </div>
    <?php
    $communityPoints = Concrete\Core\User\Point\Entry::getTotal($profile);
    if (!empty($communityPoints)) {
        ?>
        <div class="ccm-profile-statistics-item">
            <i class="icon-fire"></i> <?= t('%s Community Points', $communityPoints) ?>
        </div>
        <?php
    }
    if (!empty($badges)) {
        ?>
        <div class="ccm-profile-statistics-item">
            <i class="icon-bookmark"></i> <a href="#badges"><?= number_format(count($badges)) ?> <?= t2('Badge', 'Badges', count($badges)) ?></a>
        </div>
        <?php
    }
    ?>
    <div class="clearfix"></div>
</div>

<div id="ccm-profile-wrapper">
    <div id="ccm-profile-detail">
        <?php
        $uaks = UserAttributeKey::getPublicProfileList();
        foreach ($uaks as $ua) {
            ?>
            <div>
                <h4><?php echo $ua->getAttributeKeyDisplayName()?></h4>
                <?php
                $r = $profile->getAttribute($ua, 'displaySanitized', 'display');
                if ($r) {
                    echo $r;
                } else {
                    echo t('None');
                }
                ?>
            </div>
            <?php
        }
        if (!empty($badges)) {
            ?>
            <h4><?= t("Badges") ?></h4>
            <ul class="thumbnails">
                <?php
                foreach ($badges as $ub) {
                    $uf = $ub->getGroupBadgeImageObject();
                    if (is_object($uf)) {
                        ?>
                        <li class="span2">
                            <div class="thumbnail launch-tooltip ccm-profile-badge-image" title="<?= h($ub->getGroupBadgeDescription()) ?>">
                                <div><img src="<?= $uf->getRelativePath() ?>" /></div>
                                <div><?= t("Awarded %s", $dh->formatDate($ub->getGroupDateTimeEntered($profile))) ?></div>
                            </div>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
            <?php
        }

        $a = new Area('Main');
        //$a->setAttribute('profile', $profile);
        $a->setBlockWrapperStart('<div class="ccm-profile-body-item">');
        $a->setBlockWrapperEnd('</div>');
        $a->display($c);

        ?>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $('.launch-tooltip').tooltip({
        placement: 'bottom'
    });
});
</script>
