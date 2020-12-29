let loginRefButton = document.getElementById('login-ref');
if (loginRefButton != null) {
    loginRefButton.addEventListener('click', () => document.location.href = 'login');
}

let registerRefButton = document.getElementById("register-btn");
if (registerRefButton != null){
    registerRefButton.addEventListener('click', () => document.location.href = 'register');
}

let usersRefButton = document.getElementById('users-ref');
if (usersRefButton != null) {
    usersRefButton.addEventListener('click', () => document.location.href = 'users');
}

let friendsRefButton = document.getElementById('friends-ref');
if (friendsRefButton != null) {
    friendsRefButton.addEventListener('click', () => document.location.href = 'friends');
}

let profileRefButton = document.getElementById('profile-ref');
if (profileRefButton != null) {
    profileRefButton.addEventListener('click', () => document.location.href = 'myDetails');
}
