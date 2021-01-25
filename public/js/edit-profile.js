const oldPassword = document.getElementById('old-password');
const newPassword = document.getElementById('new-password');
const newPasswordConfirm = document.getElementById('new-password-confirm');

function markValidation(element, condition) {
    !condition ? element.classList.add('invalid') : element.classList.remove('invalid');
}
newPasswordConfirm.addEventListener('keyup', function () {
    setTimeout(function () {
        markValidation(newPasswordConfirm, newPassword.value === newPasswordConfirm.value);
        markValidation(oldPassword, oldPassword.value !== '');
    },1000);
})

oldPassword.addEventListener('keyup', function () {
    setTimeout(function () {
        markValidation(oldPassword, oldPassword.value !== '');
    },1000);
})
