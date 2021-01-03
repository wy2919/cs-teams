<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/user-list.css">
    <link rel="stylesheet" type="text/css" href="public/css/main.css">
    <script type="text/javascript" src="./public/js/redirect.js" defer></script>
    <script type="text/javascript" src="./public/js/slider.js" defer></script>

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

    <main class="main">

        <div class="user-list">

            <p>User List</p>

            <div>
                <form class="search-form" action="users" method="post">
                    <div class="search-filter">
                        <div class="user-elo">
                            <p id="slide-value"></p>
                            <input name="elo" id="search-slider" type="range" min="0" max="100" value="0">
                        </div>

                        <select name="rank">
                            <option value="-1">All ranks</option>
                            <?php foreach ($ranks as $rank) { ?>
                                <option value="<?php echo $rank->getId() ?>"><?php echo $rank->getRank() ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button class="btn user-btn search-btn" type="submit">Search</button>
                </form>
            </div>
            <?php
            if($users != null){ ?>
            <section class="users">
                <?php foreach ($users as $user) { ?>
                    <div class="user">
                        <img src="public/uploads/<?php echo $user->getImage() ?>" alt="user avatar">
                        <div class="user-details">
                            <p><?php echo $user->getUsername() ?></p>
                            <p><strong>Rank: </strong><?php echo $user->getRank() ?></p>
                            <p><strong>Elo: </strong><?php echo $user->getElo() ?></p>
                        </div>
                        <div class="user-buttons">
                            <form action="conversation" method="post">
                                <input type="hidden" name="userId" value="<?= $user->getId() ?>">
                                <button class="btn user-btn" class="btn">Message</button>
                            </form>
                            <form action="profile" method="post">
                                <input type="hidden" name="userId" value="<?= $user->getId() ?>">
                                <button class="btn user-btn" class="btn">Profile</button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </section>
            <?php } else {?>
                <p class="no-users-msg">There are no users with specified filters.</p>
            <?php } ?>
    </main>

</div>
</body>