const messageContainer = document.querySelector('.messages');
const currentUserImage = document.querySelector('input[name="userImage"]');
const friendImage = document.querySelector('input[name="friendImage"]');
const message = document.querySelector('input[name="message"]');
const conversationId = document.querySelector('input[name="conversationId"]');
const sendButton = document.querySelector('button[id="send-btn"]');

messageContainer.scrollTop = messageContainer.scrollHeight;

sendButton.addEventListener('click', () => {

    const data = {conversationId: conversationId.value, message: message.value}

    fetch("/message", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then( response => {
            return response.json();
        })
        .then(messages => {
            messageContainer.innerHTML = "";
            loadMessages(messages);
        })
});


function loadMessages(messages)
{
    messages.forEach(message => {
        createMessage(message);
    })
}


function createMessage(message) {
    const template = document.querySelector('#msg-template');
    const clone = template.content.cloneNode(true )

    const messageInput = clone.querySelector('#message-value');
    messageInput.innerHTML = message.message

    const messageBox = clone.querySelector('#who-send');

    const image = clone.querySelector('img');
    if(message.sendByFriend === false) {
        image.src = `/public/uploads/${currentUserImage.value}`;
        messageBox.classList.add('self-msg');
    } else {
        image.src = `/public/uploads/${friendImage.value}`;
        messageBox.classList.add('friend-msg');
    }

    messageContainer.appendChild(clone);
    messageContainer.scrollTop = messageContainer.scrollHeight;
}