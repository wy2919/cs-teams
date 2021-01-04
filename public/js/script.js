const form = document.querySelector('form');
const emailInput = form.querySelector('input[name="email"]')
const passwordConfirmInput = form.querySelector('input[name="passwordConfirm"]')


function isEmail(email) {
    return /\S+@\S+\.|S+/.test(email);
}

function arePasswordsSame(password, passwordConfirm) {
    return password === passwordConfirm;
}

function markValidation(element, condition) {
    !condition ? element.classList.add('invalid') : element.classList.remove('invalid')
}

emailInput.addEventListener('keyup', function () {
    setTimeout(function () {
        markValidation(emailInput, isEmail(emailInput.value))
    }, 1000);
    })



passwordConfirmInput.addEventListener('keyup', function () {
    const condition = arePasswordsSame(
        passwordConfirmInput.previousElementSibling.value,
        passwordConfirmInput.value
    )
    setTimeout(function () {
        markValidation(passwordConfirmInput, condition)
    },1000);
})
