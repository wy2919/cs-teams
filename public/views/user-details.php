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
                
                <div class="rate-user">
                    <div class="rate">
                        <p>Skills:</p>
                        <label>
                            <span class="icon">★</span>
                            <span class="icon">★</span>
                            <span class="icon">★</span>
                            <span class="icon">★</span>
                            <span class="icon">★</span>
                        </label>
                    </div>
                
                <div class="rate">
                    <p>Friendliness:</p>
                    <label>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                    </label>
                </div>
                
                <div class="rate">
                    <p>Communication:</p>
                    <label>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                        <span class="icon">★</span>
                    </label>
                </div>
            </div>
            <button class="rate-button">Rate User</button>


        </main>
</body>
            