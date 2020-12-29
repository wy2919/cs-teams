<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/user-list.css">
    <link rel="stylesheet" type="text/css" href="public/css/main.css">
    <script type="text/javascript" src="./public/js/redirect.js" defer></script>

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

                <div class="search-filter">

                    <div class="user-elo">
                        <p>User elo (50%):</p>
                        <input type="range">
                    </div>

                    <select name="rank">
                        <option value="0">All ranks</option>
                        <?php foreach($ranks as $rank){ ?>
                            <option value="<?php echo $rank->getId() ?>"><?php echo $rank->getRank() ?></option>
                        <?php } ?>
                    </select>

                </div>

                <input class="search-field" type="search" results="0">

                <section class="users">
                    <?php foreach($users as $user){ ?>
                        <div class="user">
                            <img src="public/uploads/<?php echo $user->getImage() ?>" alt="user avatar">
                            <div class="user-details">
                                <p><?php echo $user->getUsername() ?></p>
                                <p><strong>Rank: </strong><?php echo $rankRepository->getRank($user->getIdRank())->getRank() ?></p>
                                <p><strong>Elo: </strong><?php echo $ratingRepository->getUserElo($user->getId()) ?></p>
                            </div>
                            <div class="user-buttons">
                                <button class="btn">Invite</button>
                                <button class="btn">Profile</button>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="user">
                        <img src="public/img/panda-2859555_640.jpg" alt="user avatar">
                        <div class="user-details">
                            <p>PterrodaktylPL</p>
                            <p><strong>Private: </strong>no</p>
                            <p><strong>Rank: </strong>Global Elite</p>
                            <p><strong>Elo: </strong> 87%</p>
                        </div>
                        <div class="user-buttons">
                            <button class="btn">Invite</button>
                            <button class="btn">Profile</button>
                        </div>
                    </div>
                    <div class="user">
                        <img src="public/img/panda-2859555_640.jpg" alt="user avatar">
                        <div class="user-details">
                            <p>PterrodaktylPL</p>
                            <p><strong>Private: </strong>no</p>
                            <p><strong>Rank: </strong>Global Elite</p>
                            <p><strong>Elo: </strong> 87%</p>
                        </div>
                        <div class="user-buttons">
                            <button class="btn">Invite</button>
                            <button class="btn">Profile</button>
                        </div>
                    </div>
                    <div class="user">
                        <img src="public/img/panda-2859555_640.jpg" alt="user avatar">
                        <div class="user-details">
                            <p>PterrodaktylPL</p>
                            <p><strong>Private: </strong>no</p>
                            <p><strong>Rank: </strong>Global Elite</p>
                            <p><strong>Elo: </strong> 87%</p>
                        </div>
                        <div class="user-buttons">
                            <button class="btn">Invite</button>
                            <button class="btn">Profile</button>
                        </div>
                    </div>
                    <div class="user">
                        <img src="public/img/panda-2859555_640.jpg" alt="user avatar">
                        <div class="user-details">
                            <p>PterrodaktylPL</p>
                            <p><strong>Private: </strong>no</p>
                            <p><strong>Rank: </strong>Global Elite</p>
                            <p><strong>Elo: </strong> 87%</p>
                        </div>
                        <div class="user-buttons">
                            <button class="btn">Invite</button>
                            <button class="btn">Profile</button>
                        </div>
                    </div>
                    <div class="user">
                        <img src="public/img/panda-2859555_640.jpg" alt="user avatar">
                        <div class="user-details">
                            <p>PterrodaktylPL</p>
                            <p><strong>Private: </strong>no</p>
                            <p><strong>Rank: </strong>Global Elite</p>
                            <p><strong>Elo: </strong> 87%</p>
                        </div>
                        <div class="user-buttons">
                            <button class="btn">Invite</button>
                            <button class="btn">Profile</button>
                        </div>
                    </div>
                    <div class="user">
                        <img src="public/img/panda-2859555_640.jpg" alt="user avatar">
                        <div class="user-details">
                            <p>PterrodaktylPL</p>
                            <p><strong>Private: </strong>no</p>
                            <p><strong>Rank: </strong>Global Elite</p>
                            <p><strong>Elo: </strong> 87%</p>
                        </div>
                        <div class="user-buttons">
                            <button class="btn">Invite</button>
                            <button class="btn">Profile</button>
                        </div>
                    </div>
                    
                </section>

            </div>

        </main>

    </div>
</body>