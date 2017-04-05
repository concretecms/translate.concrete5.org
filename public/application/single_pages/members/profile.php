<?php
use CommunityTranslation\Service\Access;
use CommunityTranslation\Repository\Locale as LocaleRepository;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Block\Block;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Application;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\Page $c */
/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Html\Service\Html $html */
/* @var Concrete\Controller\SinglePage\Members\Profile $controller */

/* @var Concrete\Core\User\UserInfo $profile */
/* @var array $badges */

$app = Application::getFacadeApplication();

$dh = $app->make('date');
/* @var Concrete\Core\Localization\Service\Date $dh */

$url = $app->make('url/manager');
/* @var Concrete\Core\Url\Resolver\Manager\ResolverManager $url */

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
                <a href="<?= $url->resolve(['/account/edit_profile']) ?>" class="btn btn-sm btn-default"><i class="fa fa-cog"></i> <?= t('Edit') ?></a>
                <a href="<?= $url->resolve(['/']) ?>" class="btn btn-sm btn-default"><i class="fa fa-home"></i> <?= t('Home') ?></a>
            </div>
            <?php
        } else {
            ?><div class="btn-group">
                <?php
                if ($c5ProfileURL !== null) {
                    ?><a href="<?= h($c5ProfileURL) ?>" class="btn btn-sm btn-default" target="_blank"><i class="fa-user fa"></i> <?= t('View concrete5 profile') ?></a><?php
                }
                if ($profile->getAttribute('profile_private_messages_enabled')) {
                    ?><a href="<?= $url->resolve(['/account/messages', 'write', $profile->getUserID()]) ?>" class="btn btn-sm btn-default"><i class="fa-envelope fa"></i> <?= t('Send private message') ?></a><?php
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
        
        if (class_exists(Access::class) && $profile->getUserID() != USER_SUPER_ID) {
            $access = $app->make(Access::class);
            /* @var Access $access */
            $localeRepository = $app->make(LocaleRepository::class);
            /* @var LocaleRepository $localeRepository */
            $localeAccessList = [];
            $teamsPage = false;
            foreach ($localeRepository->getApprovedLocales() as $locale) {
                $la = $access->getLocaleAccess($locale, $profile->getEntityObject());
                if ($la >= Access::GLOBAL_ADMIN) {
                    $localeAccessList = Access::GLOBAL_ADMIN;
                    break;
                } else {
                    switch ($la) {
                        case Access::ADMIN:
                            $text = tc('User is...', 'a team coordinator for %s');
                            break;
                        case Access::TRANSLATE:
                            $text = tc('User is...', 'a translator for %s');
                            break;
                        case Access::ASPRIRING:
                            $text = tc('User is...', 'an aspiring translator for %s');
                            break;
                        default:
                            $text = null;
                            break;
                    }
                    if ($text !== null) {
                        if ($teamsPage === false) {
                            $teamsPage = null;
                            $block = Block::getByName('CommunityTranslation Team List');
                            if ($block && $block->getBlockID()) {
                                $p = $block->getOriginalCollection();
                                if ($p && !$p->isError()) {
                                    $teamsPage = $p;
                                }
                            }
                        }
                        if ($teamsPage === null) {
                            $text = sprintf($text, h($locale->getDisplayName()));
                        } else {
                            $text = sprintf($text, sprintf('<a href="%s">%s</a>', $url->resolve([$teamsPage, 'details', $locale->getID()]), h($locale->getDisplayName())));
                        }
                        $localeAccessList[] = $text;
                    }
                }
            }
            if ($localeAccessList !== []) {
                ?>
                <h4><?= t('Translator Details') ?></h4>
                <?php
                if ($localeAccessList === Access::GLOBAL_ADMIN) {
                    ?><p><?= t('%s is a site maintainer: your last hope to solve localization-related issues.', h($profile->getUserName())) ?></p><?php
                } else {
                    ?><p><?= tc('User is...', '%1$s is %2$s.', h($profile->getUserName()), Punic\Misc::join($localeAccessList)) ?></p><?php
                }
                $db = $app->make(Connection::class);
                $totalTranslations = 0;
                $currentTranslations = 0;
                /* @var Connection $db */
                $rs = $db->executeQuery('select count(*) as n, current from CommunityTranslationTranslations where createdBy = ? group by current', [$profile->getUserID()]);
                while (($row = $rs->fetch()) !== false) {
                    if ($row['current']) {
                        $currentTranslations = (int) $row['n'];
                        $totalTranslations += $currentTranslations;
                    } else {
                        $totalTranslations += (int) $row['n'];
                    }
                }
                $rs->closeCursor();
                if ($totalTranslations === 0) {
                    ?><p><?= t('%s has not yet contributed any translation.', h($profile->getUserName())) ?></p><?php
                } else {
                    ?><p><?=
                        t2(
                            '%2$s has contributed with %1$d translation',
                            '%2$s has contributed with %1$d translations',
                            $totalTranslations,
                            h($profile->getUserName())
                        )
                        .
                        t2(
                            '(%d of which is a currently approved translation)',
                            '(%d of which are currently approved translations)',
                            $currentTranslations
                        )
                    ?>.</p><?php
                }
            }
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
