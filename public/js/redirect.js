let loginRefButton = document.querySelectorAll('.login-ref');
if (loginRefButton != null) {
    loginRefButton.forEach( i => i.addEventListener('click', () => document.location.href = 'login'));
}

let registerRefButton = document.querySelectorAll(".register-btn");
if (registerRefButton != null){
    registerRefButton.forEach( i => i.addEventListener('click', () => document.location.href = 'register'));
}

let usersRefButton = document.querySelectorAll('.users-ref');
if (usersRefButton != null) {
    usersRefButton.forEach( i => i.addEventListener('click', () => document.location.href = 'users'));
}

let friendsRefButton = document.querySelectorAll('.friends-ref');
if (friendsRefButton != null) {
    friendsRefButton.forEach( i => i.addEventListener('click', () => document.location.href = 'conversation'));
}

let profileRefButton = document.querySelectorAll('.profile-ref');
if (profileRefButton != null) {
    profileRefButton.forEach( i => i.addEventListener('click', () => document.location.href = 'myDetails'));
}
