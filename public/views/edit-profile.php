<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/public/css/main.css">
    <link rel="stylesheet" type="text/css" href="/public/css/edit-profile.css">
    <script type="text/javascript" src="/public/js/main.js" defer></script>
    <script type="text/javascript" src="/public/js/edit-profile.js" defer></script>

    <script src="https://kit.fontawesome.com/3010d94d2f.js" crossorigin="anonymous"></script>
    <title>User list</title>
</head>
<body>
    <div class="base-container">

        <?php include('navigation.php') ?>

        <main class="main">

        <p class="message">
            <?php include('message.php') ?>
        </p>

        <div class="avatar-container edit-container">
            <div class="column-flex">
                <p class="panel-title title">Avatar</p>
                <img class="user-avatar" src="public/uploads/<?= $user->getImage()?>" alt="user avatar">
            </div>
            <form class="avatar-form" action="editProfile" method="POST" enctype="multipart/form-data">
                <input class="avatar-input" type="file" name="file">
                <input type="hidden" name="userId" value="<?= $user->getId()?>">
                <button class="btn button" type="submit">Change</button>
            </form>
        </div>

        <hr>

        <div class="edit-container column-flex">
            <p class="panel-title">Rank</p>

            <form class="between-form" action="editProfile" method="POST">
                <select class="rank-select" name="rankId">
                    <?php foreach ($ranks as $rank) { ?>
                        <option value="<?php echo $rank->getId() ?>" <?php if($rank->getRank() == $user->getRank()) echo"selected"?>><?php echo $rank->getRank() ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="userId" value="<?= $user->getId()?>">
                <button class="btn button" type="submit">Change</button>
            </form>
        </div>

        <hr>

            <div class="edit-container column-flex">
                <p class="panel-title">Description</p>

                <form class="between-form" action="editProfile" method="POST">
                    <textarea name="description" class="description-input" maxlength="150"><?= $user->getDescription()?></textarea>
                    <input type="hidden" name="userId" value="<?= $user->getId()?>">
                    <button class="btn button" type="submit">Change</button>
                </form>
            </div>

            <hr>

            <div class="edit-container column-flex">
                <p class="panel-title">Password</p>

                <form class="between-form" action="editPassword" method="POST">
                    <div class="column-flex">
                        <p class="password-header">Current password:</p>
                        <input id="old-password" name="password" class="password-input" type="password" autocomplete="on">
                        <p class="password-header">New password:</p>
                        <input id="new-password" name="newPassword" class="password-input" type="password" autocomplete="on">
                        <p class="password-header">Confirm new password:</p>
                        <input id="new-password-confirm" name="newPasswordConfirm" class="password-input" type="password" autocomplete="on">
                    </div>
                    <input type="hidden" name="userId" value="<?= $user->getId()?>">
                    <button class="btn button" type="submit">Change</button>
                </form>
            </div>

            <hr>
        </main>

    </div>
</body>