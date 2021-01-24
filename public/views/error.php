<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/public/css/main.css">
    <link rel="stylesheet" type="text/css" href="/public/css/error.css">
    <script type="text/javascript" src="/public/js/main.js" defer></script>

    <script src="https://kit.fontawesome.com/3010d94d2f.js" crossorigin="anonymous"></script>
    <title>Error</title>
</head>
<body>
<div class="base-container">

    <?php include('navigation.php') ?>

    <main class="main">
        <h2 class="error-title">We have a problem here..</h2>
        <p class="error-message">
            <?php
                if(isset($message)) {
                    echo $message;
                } else {
                    echo "unknown error.";
                }
             ?>
        </p>
        <p class="error-message">
            For more information please contact us on: awskowalczyk@gmail.com
        </p>
    </main>

</div>
</body>
