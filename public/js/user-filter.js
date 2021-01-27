const rank = document.querySelector('select[name="rank"]');
const elo = document.querySelector('input[name="elo"]');
const userContainer = document.querySelector('.users');

function addEvent(element){
    element.addEventListener('change', () => {
        const data = {rank: rank.value, elo: elo.value};

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
        createUserTemplate(user);
    })
}

function createUserTemplate(user) {
    const template = document.querySelector('#user-template');
    const clone = template.content.cloneNode(true );
    const profileButtonForm = clone.querySelector('.profile-form');
    const link = clone.querySelector("a");

    const image = clone.querySelector("img");
    image.src= `/public/uploads/${user.image}`;
    appendOnError([image]);

    const profileLink = `/profile/${user.username}`;
    profileButtonForm.action = profileLink;
    link.href = profileLink;

    const username = clone.querySelector('#username');
    username.innerHTML = user.username;

    const rank = username.nextElementSibling;
    rank.innerHTML = rank.innerHTML + ' ' + user.rank;

    const elo = rank.nextElementSibling;
    elo.innerHTML = elo.innerHTML + ' ' + user.elo;

    const inputs = clone.querySelectorAll('input[name=userId]');
    inputs.forEach( input => {
        input.value = user.id;
    });

    userContainer.appendChild(clone);
}

addEvent(rank);
addEvent(elo);
