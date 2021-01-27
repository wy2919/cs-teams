<template id="user-template">
    <div class="user">

        <a class="link" href="">

            <img src="" alt="user avatar">

            <div class="user-details">
                <p id="username"></p>
                <p><strong>Rank: </strong></p>
                <p><strong>Elo: </strong></p>
            </div>

        </a>

        <div class="user-buttons">

            <form action="conversation" method="post">
                <input type="hidden" name="userId">
                <button class="btn user-btn">Message</button>
            </form>

            <form class="profile-form" method="post">
                <input type="hidden" name="userId">
                <button class="btn user-btn">Profile</button>
            </form>

        </div>
    </div>
</template>