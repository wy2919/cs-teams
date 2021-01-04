<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/main.css">
    <link rel="stylesheet" type="text/css" href="public/css/edit-profile.css">
    <script type="text/javascript" src="./public/js/redirect.js" defer></script>
    <script type="text/javascript" src="./public/js/edit-profile.js" defer></script>

    <script src="https://kit.fontawesome.com/3010d94d2f.js" crossorigin="anonymous"></script>
    <title>User list</title>
</head>
<body>
    <div class="base-container">
        
        <nav class="navigation">
            <img src="public/img/logo.svg" alt="logo">
            <ul>
                <li>
                    <a id="users-ref">User List</a>
                </li>
                <li>
                    <a id="friends-ref">Friends</a>
                </li>
                <li>
                    <a id="profile-ref">My Profile</a>
                </li>
                <li>
                    <a id="login-ref">Log out</a>
                </li>
            </ul>
            <i class="hamburger fas fa-bars"></i>
        </nav>
        <main>

        <p class="message">
            <?php
            if(isset($message)) echo $message;
            ?>
        </p>

        <div class="avatar-container edit-container">
            <div class="column-flex">
                <p class="panel-title">Avatar</p>
                <img class="user-avatar" src="public/uploads/<?= $user->getImage()?>" alt="user avatar">
            </div>
            <form class="avatar-form" action="editAvatar" method="POST" enctype="multipart/form-data">
                <input class="avatar-input" type="file" name="file">
                <button class="btn button" type="submit">Change</button>
            </form>
        </div>

        <hr>

        <div class="edit-container column-flex">
            <p class="panel-title">Rank</p>

            <form class="between-form" action="editDetails" method="POST">
                <select class="rank-select" name="rank">
                    <?php foreach ($ranks as $rank) { ?>
                        <option value="<?php echo $rank->getId() ?>" <?php if($rank->getRank() == $user->getRank()) echo"selected"?>><?php echo $rank->getRank() ?></option>
                    <?php } ?>
                </select>
                <button class="btn button" type="submit">Change</button>
            </form>
        </div>

        <hr>

            <div class="edit-container column-flex">
                <p class="panel-title">Description</p>

                <form class="between-form" action="editDetails" method="POST">
                    <textarea name="description" class="description-input" type="text" maxlength="150"><?= $user->getDescription()?></textarea>
                    <button class="btn button" type="submit">Change</button>
                </form>
            </div>

            <hr>

            <div class="edit-container column-flex">
                <p class="panel-title">Password</p>

                <form class="between-form" action="editPassword" method="POST">
                    <div class="column-flex">
                        <p class="password-header">Current password:</p>
                        <input id="old-password" name="password" class="password-input" type="password">
                        <p class="password-header">New password:</p>
                        <input id="new-password" name="newPassword" class="password-input" type="password">
                        <p class="password-header">Confirm new password:</p>
                        <input id="new-password-confirm" name="newPasswordConfirm" class="password-input" type="password">
                    </div>
                    <button class="btn button" type="submit">Change</button>
                </form>
            </div>

            <hr>
        </main>

    </div>
</body>