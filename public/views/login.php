<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/public/css/main.css">
    <link rel="stylesheet" type="text/css" href="/public/css/login.css">
    <script type="text/javascript" src="/public/js/main.js" defer></script>
    <title>Login page</title>

</head>
<body>
    <div class="container">

        <div class="logo">
            <img src="/public/img/logo.svg" alt="logo">
        </div>

<!--                               html request  /login typu post-->
        <form class="login" action="login" method="post">
            <p>Log in</p>
            <div class="message">
                <?php include('message.php') ?>
            </div>
            <input name="email" type="text" placeholder="email">
            <input name="password" type="password" placeholder="password" autocomplete="on">

            <button type="submit" class="btn">Login</button>
            <button type="button" class="btn" id="register-btn">Register</button>
        </form>

    </div>
</body>