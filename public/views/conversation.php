<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/main.css">
    <link rel="stylesheet" type="text/css" href="public/css/conversation.css">
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
            <input type="hidden" value="0">
            <p class = "header">Conversation</p>
            
            <div class="communicator-container">
                <div class="friend-list">
                    <?php foreach($conversations as $conv){ ?>
                    <form class="single" action="conversation" method="post">
                        <input name="userId" type="hidden" value="<?= $conv->getUserId()?>">
                        <img src="public/uploads/<?= $conv->getImage()?>" alt="user avatar" class="friend-img">
                        <p><?= $conv->getUsername()?></p>
                        <button class="msg-btn" type="submit" ><i class="far fa-envelope"></i></button>
                    </form>
                    <hr class="solid">
                    <?php } ?>
                </div>
                <div class="friend">

                    <div class="inside-friend">
                        
                        <form class="friend-details" action="profile" method="post">
                            <input type="hidden" name="userId" value=" <?=$selected->getUserId()?>->">
                            <img src="public/uploads/<?= $selected->getImage() ?>" alt="user avatar" class="friend-img">
                            <p class="friend-name"><?= $selected->getUsername() ?></p>
                            <button type="submit" class="btn profile-btn">Profile</button>
                        </form>

                        <div class="message-box">
                            <div class="messages">

                                <?php foreach($messages as $message){ ?>
                                <div class="msg-container">
                                    <?php if(!$message->isSendByFriend()) { ?>
                                    <div class="self-msg">
                                        <p><?= $message->getMessage() ?></p>
                                    </div>
                                    <img src="public/uploads/<?= $user->getImage() ?>" alt="user avatar" class="small-img">
                                    <?php } else {?>
                                        <img src="public/uploads/<?= $selected->getImage() ?>" alt="user avatar" class="small-img">
                                        <div class="friend-msg">
                                            <p><?= $message->getMessage() ?></p>
                                        </div>
                                    <?php } ?>
                                </div>

                                <?php } ?>

                            </div>

                            <div class="message">
                                <input class="msg-input" type="text">
                                <button class="btn">Send</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

    </div>
</body>