<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="/public/css/user-details.css">
    <link rel="stylesheet" type="text/css" href="/public/css/main.css">

    <script type="text/javascript" src="/public/js/img-default.js" defer></script>
    <script src="https://kit.fontawesome.com/3010d94d2f.js" crossorigin="anonymous"></script>

    <title>User list</title>
</head>
<body>
<div class="base-container">

    <?php include('components/navigation.php') ?>

    <main class="main">
        <div class="user-details">

            <h2 class="title">User Details</h2>

            <div class="container">

                <div class="user-info">
                    <p class="username"><?= $user->getUsername() ?></p>
                    <img src="public/uploads/<?= $user->getImage() ?>" alt="user avatar">
                    <p><strong>Elo: </strong><?= $user->getElo() ?></p>
                    <p><strong>Rank: </strong><?= $user->getRank() ?></p>
                </div>

                <form class="description" action="editProfile" method="post">
                    <p><strong> Description: </strong><?= $user->getDescription() ?></p>
                    <input type="hidden" name="userId" value="<?= $user->getId() ?>">
                    <button type="submit" class="btn">Edit</button>
                </form>

            </div>

        </div>
    </main>
</body>
            