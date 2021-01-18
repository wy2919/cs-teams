const loginRefButton = document.querySelectorAll('.login-ref');
if (loginRefButton != null) {
    loginRefButton.forEach( i => i.addEventListener('click', () => document.location.href = 'login'));
}

const registerRefButton = document.querySelector("#register-btn");
if (registerRefButton != null){
    registerRefButton.addEventListener('click', () => document.location.href = 'register');
}

const usersRefButton = document.querySelectorAll('.users-ref');
if (usersRefButton != null) {
    usersRefButton.forEach( i => i.addEventListener('click', () => document.location.href = 'users'));
}

const friendsRefButton = document.querySelectorAll('.friends-ref');
if (friendsRefButton != null) {
    friendsRefButton.forEach( i => i.addEventListener('click', () => document.location.href = 'conversation'));
}

const profileRefButton = document.querySelectorAll('.profile-ref');
if (profileRefButton != null) {
    profileRefButton.forEach( i => i.addEventListener('click', () => document.location.href = 'myDetails'));
}

const images = document.querySelectorAll("img");
images.forEach(img => img.onerror = function() {
    this.onerror=null;
    this.src='/public/uploads/placeholder.png';
})
