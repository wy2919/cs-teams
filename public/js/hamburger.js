const navigation = document.querySelector('.hamburger-nav');
const hamburgers = document.querySelectorAll('.hamburger');

hamburgers.forEach(hamburger => {
    hamburger.addEventListener('click', () => {
        navigation.classList.toggle('hamburger-nav-active');
    })
});