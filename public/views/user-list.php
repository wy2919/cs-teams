<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/public/css/user-list.css">
    <link rel="stylesheet" type="text/css" href="/public/css/main.css">
    <script type="text/javascript" src="/public/js/main.js" defer></script>
    <script type="text/javascript" src="/public/js/slider.js" defer></script>
        <script type="text/javascript" src="/public/js/filter.js" defer></script>

    <script src="https://kit.fontawesome.com/3010d94d2f.js" crossorigin="anonymous"></script>
    <title>User list</title>
</head>
<body>
<div class="base-container">

    <?php include('navigation.php') ?>

    <main class="main">

        <div class="user-list">

            <p class="title">User List</p>

            <div>
                <div class="search-form">
                    <div class="search-filter">
                        <div class="user-elo">
                            <p id="slide-value"></p>
                            <input name="elo" id="search-slider" type="range" min="0" max="100" value="0">
                        </div>

                        <select class="rank-select" name="rank">
                            <option value="-1">All ranks</option>
                            <?php foreach ($ranks as $rank) { ?>
                                <option value="<?php echo $rank->getId() ?>"><?php echo $rank->getRank() ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php
            if($users != null){ ?>
            <section class="users">
                <?php foreach ($users as $user) { ?>
<!--                        TODO: ROUTE-GUARD: getIdByUsername.. wszedzie?-->
                <div class="user">

                    <a class="link" href="/profile/<?= $user->getUsername() ?>">
                        <img src="public/uploads/<?php echo $user->getImage() ?>" alt="user avatar">
                        <div class="user-details">
                            <p id="username"><?php echo $user->getUsername() ?></p>
                            <p><strong>Rank: </strong><?php echo $user->getRank() ?></p>
                            <p><strong>Elo: </strong><?php echo $user->getElo() ?></p>
                        </div>
                    </a>
                    <div class="user-buttons">
                        <form action="conversation" method="post">
                            <input type="hidden" name="userId" value="<?= $user->getId() ?>">
                            <button class="btn user-btn">Message</button>
                        </form>
                        <form action="profile/<?= $user->getUsername() ?>" method="post">
                            <button class="btn user-btn">Profile</button>
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


<template id="user-template">
    <div class="user">
        <img alt="user avatar">
        <div class="user-details">
            <p id="username"></p>
            <p><strong>Rank: </strong></p>
            <p><strong>Elo: </strong></p>
        </div>
        <div class="user-buttons">
            <form action="conversation" method="post">
                <input type="hidden" name="userId">
                <button class="btn user-btn">Message</button>
            </form>
            <form action="profile" method="post">
                <input type="hidden" name="userId">
                <button class="btn user-btn">Profile</button>
            </form>
        </div>
    </div>
</template>