const form = document.querySelector('form');
const emailInput = form.querySelector('input[name="email"]');
const usernameInput = form.querySelector('input[name="username"]');
const passwordConfirmInput = form.querySelector('input[name="passwordConfirm"]');
const registerRefButton = document.querySelector("#register-btn");

if (registerRefButton != null){
    registerRefButton.addEventListener('click', () => document.location.href = '/register');
}

function isEmail(email) {
    return /\S+@\S+\.|S+/.test(email);
}

function isUsernameValid(username) {
    return username.length > 5 && username.length < 15;
}

function arePasswordsSame(password, passwordConfirm) {
    return password === passwordConfirm;
}

function markValidation(element, condition) {
    !condition ? element.classList.add('invalid') : element.classList.remove('invalid');
}

if(emailInput != null) {
    emailInput.addEventListener('keyup', function () {
        setTimeout(function () {
            markValidation(emailInput, isEmail(emailInput.value));
        }, 1000);
    });
}

if(usernameInput != null) {
    usernameInput.addEventListener('keyup', function () {
        setTimeout(function () {
            markValidation(usernameInput, isUsernameValid(usernameInput.value));
        }, 1000);
    });
}

if(passwordConfirmInput != null){
passwordConfirmInput.addEventListener('keyup', function () {
    const condition = arePasswordsSame(
        passwordConfirmInput.previousElementSibling.value,
        passwordConfirmInput.value
    )
    setTimeout(function () {
        markValidation(passwordConfirmInput, condition);
    },1000);
});
}
