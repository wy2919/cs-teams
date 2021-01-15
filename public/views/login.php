<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/main.css">
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <script type="text/javascript" src="./public/js/redirect.js" defer></script>
    <title>Login page</title>

</head>
<body>
    <div class="container">

        <div class="logo">
            <img src="public/img/logo.svg" alt="logo">
        </div>

<!--                               html request  /login typu post-->
        <form class="login" action="login" method="post">
            <p>Log in</p>
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
            <input name="password" type="password" placeholder="password">

            <button type="submit" class="btn">Login</button>
            <button type="reset" class="btn" id="register-btn">Register</button>
        </form>

    </div>
</body>