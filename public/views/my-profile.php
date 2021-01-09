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

        <?php include('navigation.php') ?>

        <main class="main">

            <div class="user-details">
                <h2>User Details</h2>
                <div class="container">

                    <div class="user-info">
                        <p class="username"><?= $user->getUsername() ?></p>
                        <img src="public/uploads/<?= $user->getImage() ?>" alt="user avatar">
                        <p><strong>Elo: </strong><?=$user->getElo()?></p>
                        <p><strong>Rank: </strong><?=$user->getRank()?></p>
                    </div>
                    <div class="description">
                        <p><strong> Description: </strong><?= $user->getDescription()?></p>
                        <form action="editProfile" method="get">
                            <button type="submit" class="btn">Edit</button>
                        </form>
                    </div>
                </div>
            </div>



        </main>
</body>
            