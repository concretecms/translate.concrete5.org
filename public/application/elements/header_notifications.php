<?php
defined('C5_EXECUTE') or die('Access Denied.');

$user = new User();
?>
<div id="header-notifications">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php
                if ($user->isRegistered()) {
                    ?>
                    <a class="profile-link" href="<?= URL::to('/members/profile') ?>">
                        <?= $user->getUserName() ?>
                    </a>
                    <a class="logout" href="<?= URL::to('login', 'logout', Core::make('token')->generate('logout')) ?>">
                        <?= t('Logout') ?>
                    </a>
                    <?php
                } else {
                    ?>
                    <a href="https://www.concrete5.org/register" class="sign-up">
                        <?= t('Join our Community') ?>
                    </a>
                    <a href="<?= URL::to('/login') ?>" class="sign-in">
                        <?= t('Sign In') ?>
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>