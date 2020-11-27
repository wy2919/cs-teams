<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/user-details.css">
    <link rel="stylesheet" type="text/css" href="public/css/main.css">
    <link rel="stylesheet" type="text/css" href="public/css/my-profile.css">


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
            
            <div class="user-details">
                <h2>User Details</h2>
                <div class="container">

                    <div class="user-info">
                        <p>John Doe</p>
                        <img src="public/uploads/<?= $image ?>" alt="user avatar">
                        <p><strong>Elo: </strong>95%</p>
                        <p><strong>Rank: </strong>Global Elite</p>
                    </div>
                    <div class="description">
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sapiente placeat deserunt nulla, laborum distinctio repellat itaque! Mollitia dolorem veritatis, praesentium sunt odio obcaecati eveniet dolore eos natus adipisci? Aliquam, ab.Suscipit perferendis quos, voluptatem omnis non tenetur exercitationem molestiae laudantium cum ex eius maxime voluptatum accusamus esse voluptatibus perspiciatis temporibus aspernatur eveniet doloremque obcaecati corporis qui! Soluta architecto magnam qui.</p>
                        <button class="button">Edit</button>
                    </div>

                </div>
            </div>
            <div class="edit-panel">
                <button>change password</button>
                <div class="switch">
                    <span>private account: </span>
                    <input type="checkbox"/>
                </div>
                <select>
                    <option value="">Global Elite</option>
                </select>
            </div>


        </main>
</body>
            