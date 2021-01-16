<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="public/css/main.css">
    <link rel="stylesheet" type="text/css" href="public/css/login.css">
    <script type="text/javascript" src="./public/js/script.js" defer></script>
    <title>Register page</title>
</head>
<body>
    <div class="container">

        <div class="logo">
            <img src="public/img/logo.svg" alt="logo">
        </div>
<!---->
        <form class="login register" action="register" method="post">
            <p>Register</p>
            <div class="message">
                <?php
                if(isset($messages)){
                    foreach ($messages as $message) {
                        echo $message;
                    }
                }
                ?>
            </div>
            <input name="email" type="text" placeholder="email">
            <input name="username" type="text" placeholder="login">
            <input name="password" type="password" placeholder="password">
            <input name="passwordConfirm" type="password" placeholder="password">
            <select name="rank">
                <?php foreach($ranks as $rank){ ?>
                <option value="<?php echo $rank->getId() ?>"><?php echo $rank->getRank() ?></option>
                <?php } ?>
              </select>
            <button class="btn">Sign up</button>
        </form>

    </div>
</body>