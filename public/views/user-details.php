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
                        <p>John Doe</p>
                        <img src="public/img/panda-2859555_640.jpg" alt="user avatar">
                        <p><strong>Elo: </strong>95%</p>
                        <p><strong>Rank: </strong>Global Elite</p>
                    </div>
                    <div class="description">
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sapiente placeat deserunt nulla, laborum distinctio repellat itaque! Mollitia dolorem veritatis, praesentium sunt odio obcaecati eveniet dolore eos natus adipisci? Aliquam, ab.Suscipit perferendis quos, voluptatem omnis non tenetur exercitationem molestiae laudantium cum ex eius maxime voluptatum accusamus esse voluptatibus perspiciatis temporibus aspernatur eveniet doloremque obcaecati corporis qui! Soluta architecto magnam qui.</p>
                        <button class="btn">Invite</button>
                    </div>

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
            