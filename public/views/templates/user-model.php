<template id="user-template">
    <div class="user">

        <img src="" alt="user avatar">

        <div class="user-details">
            <p id="username"></p>
            <p><strong>Rank: </strong></p>
            <p><strong>Elo: </strong></p>
        </div>

        <div class="user-buttons">

            <form action="conversation" method="post">
                <input type="hidden" name="userId">
                <button class="btn user-btn">Message</button>
            </form>

            <form action="profile" method="post">
                <input type="hidden" name="userId">
                <button class="btn user-btn">Profile</button>
            </form>

        </div>
    </div>
</template>