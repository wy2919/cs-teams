const slider = document.getElementById('search-slider');
const value = document.getElementById('slide-value');

value.textContent =  'required elo: ' + slider.value;

slider.oninput = (() => {
    value.textContent = 'required elo: ' + slider.value;
})
