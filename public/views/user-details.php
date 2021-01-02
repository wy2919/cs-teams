<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/user-details.css">
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
            
            <div class="user-details">
                <h2>User Details</h2>
                <p class="messages"><?= $messages?></p>
                <div class="container">

                    <div class="user-info">
                        <p><?= $user->getUsername() ?></p>
                        <img src="public/uploads/<?= $user->getImage() ?>" alt="user avatar">
                        <p><strong>Elo: </strong><?= $user->getElo() ?></p>
                        <p><strong>Rank: </strong><?= $user->getRank() ?></p>
                    </div>
                    <form class="description" action="conversation" method="post">
                        <input type="hidden" name="userId" value="<?=$user->getId() ?>">
                        <p><?= $user->getDescription() ?></p>
                        <button type="submit" class="btn">Message</button>
                    </form>

                </div>
                </div>
                <form action="rateUser" method="post">
                    <input type="hidden" name="userId" value="<?= $user->getId()?>">
                <div class="rate-user">
                    <div class="rate">
                        <p>Skills:</p>

                        <div class="stars">
                            <input type="radio" name="skills" id="1-rate-5" value="5">
                            <label for="1-rate-5" class="fas fa-star"></label>

                            <input type="radio" name="skills" id="1-rate-4" value="4">
                            <label for="1-rate-4" class="fas fa-star"></label>

                            <input type="radio" name="skills" id="1-rate-3" value="3">
                            <label for="1-rate-3" class="fas fa-star"></label>

                            <input type="radio" name="skills" id="1-rate-2" value="2">
                            <label for="1-rate-2" class="fas fa-star"></label>

                            <input type="radio" name="skills" id="1-rate-1" value="1" checked>
                            <label for="1-rate-1" class="fas fa-star"></label>
                        </div>
                    </div>
                
                <div class="rate">
                    <p>Friendliness:</p>

                    <div class="stars">
                        <input type="radio" name="friendliness" id="2-rate-5" value="5">
                        <label for="2-rate-5" class="fas fa-star"></label>

                        <input type="radio" name="friendliness" id="2-rate-4" value="4">
                        <label for="2-rate-4" class="fas fa-star"></label>

                        <input type="radio" name="friendliness" id="2-rate-3" value="3">
                        <label for="2-rate-3" class="fas fa-star"></label>

                        <input type="radio" name="friendliness" id="2-rate-2" value="2">
                        <label for="2-rate-2" class="fas fa-star"></label>

                        <input type="radio" name="friendliness" id="2-rate-1" value="1" checked>
                        <label for="2-rate-1" class="fas fa-star"></label>
                    </div>
                </div>
                
                <div class="rate">
                    <p>Communication:</p>

                    <div class="stars">
                        <input type="radio" name="communication" id="3-rate-5" value="5">
                        <label for="3-rate-5" class="fas fa-star"></label>

                        <input type="radio" name="communication" id="3-rate-4" value="4">
                        <label for="3-rate-4" class="fas fa-star"></label>

                        <input type="radio" name="communication" id="3-rate-3" value="3">
                        <label for="3-rate-3" class="fas fa-star"></label>

                        <input type="radio" name="communication" id="3-rate-2" value="2">
                        <label for="3-rate-2" class="fas fa-star"></label>

                        <input type="radio" name="communication" id="3-rate-1" value="1" checked>
                        <label for="3-rate-1" class="fas fa-star"></label>
                    </div>
                </div>

            </div>
                <button type="submit" class="rate-button">Rate User</button>

            </form>

        </main>
</body>
            