const rank = document.querySelector('select[name="rank"]');
const elo = document.querySelector('input[name="elo"]');
const userContainer = document.querySelector('.users');
// const messageContainer = document.querySelector('.inside-friend');
// const currentUserImage = document.querySelector('input[name="userImage"]');

function addEvent(element){
    element.addEventListener('change', () => {

        const data = {rank: rank.value, elo: elo.value}

        fetch("/filter", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then( response => {
                return response.json();
            })
            .then(users => {
                userContainer.innerHTML = "";
                loadUsers(users);
            })
    });
}

function loadUsers(users) {
    users.forEach(user => {
        createUser(user);
    })
}

function createUser(user) {
    const template = document.querySelector('#user-template');

    const clone = template.content.cloneNode(true )

    const image = clone.querySelector("img");
    image.src= `/public/uploads/${user.image}`

    const username = clone.querySelector('#username');
    username.innerHTML = user.username;

    const rank = username.nextElementSibling;
    rank.innerHTML = rank.innerHTML + ' ' + user.rank;

    const elo = rank.nextElementSibling;
    elo.innerHTML = elo.innerHTML + ' ' + user.elo;

    const inputs = clone.querySelectorAll('input[name=userId]')
    inputs.forEach( input => {
        input.value = user.id;
    })

    userContainer.appendChild(clone);
}


addEvent(rank);
addEvent(elo)