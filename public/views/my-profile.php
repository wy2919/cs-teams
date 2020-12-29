<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/user-details.css">
    <link rel="stylesheet" type="text/css" href="public/css/main.css">
    <link rel="stylesheet" type="text/css" href="public/css/my-profile.css">
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
                        <p><strong>Elo: </strong><?=$user->getElo()?></p>
                        <p><strong>Rank: </strong><?=$user->getRank()?></p>
                    </div>
                    <div class="description">
                        <p><?= $user->getDescription()?></p>
                        <button class="btn">Edit</button>
                    </div>

                </div>
            </div>
            <div class="edit-panel">
                <button>change password</button>
                <select>
                    <option value="">Global Elite</option>
                </select>
            </div>


        </main>
</body>
            