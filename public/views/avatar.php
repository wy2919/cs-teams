<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/main.css">
    <link rel="stylesheet" type="text/css" href="public/css/avatar.css">

    <script src="https://kit.fontawesome.com/3010d94d2f.js" crossorigin="anonymous"></script>
    <title>User list</title>
</head>
<body>
    <div class="base-container">
        
        <nav class="navigation">
            <img src="public/img/logo.svg" alt="logo">
            <ul>
                <li>
                    <a href="#">User List</a>
                </li>
                <li>
                    <a href="#">Friends</a>
                </li>
                <li>
                    <a href="#">Messages</a>
                </li>
                <li>
                    <a href="#">My Profile</a>
                </li>
                <li>
                    <a href="#">Log out</a>
                </li>
            </ul>
            <i class="hamburger fas fa-bars"></i>
        </nav>

        <main class="main">
            <h1>Change your avatar</h1>
            <img class="user-avatar" src="public/img/panda-2859555_640.jpg" alt="user avatar">
            <form action="editAvatar" method="POST" enctype="multipart/form-data">
                        <?php
                        if(isset($messages)){
                            foreach ($messages as $message) {
                                echo $message;
                            }
                        }
                        ?> 
                <input type="file" name="file">
                <button class="button" type="submit">Send</button>
            </form>
        </main>

    </div>
</body>