const messageContainer = document.querySelector('.messages');
const currentUserImage = document.querySelector('input[name="userImage"]');
const friendImage = document.querySelector('input[name="friendImage"]');
const message = document.querySelector('input[name="message"]');
const conversationId = document.querySelector('input[name="conversationId"]');
const sendButton = document.querySelector('button[id="send-btn"]');

messageContainer.scrollTop = messageContainer.scrollHeight;

sendButton.addEventListener('click', () => {

    if(message.value.length > 0) {
        const data = {conversationId: conversationId.value, message: message.value};

        fetch("/message", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                return response.json();
            })
            .then(messages => {
                messageContainer.innerHTML = "";
                loadMessages(messages);
                message.value = '';
            });
    }
});

function loadMessages(messages)
{
    messages.forEach(message => {
        createMessageTemplate(message);
    })
}

function createMessageTemplate(message) {
    const template = document.querySelector('#msg-template');
    const clone = template.content.cloneNode(true );
    const messageBox = clone.querySelector('#who-send');

    const messageInput = clone.querySelector('#message-value');
    messageInput.innerHTML = message.message;

    const image = clone.querySelector('img');
    appendOnError([image]);

    if(message.sendByFriend) {
        image.src = `/public/uploads/${friendImage.value}`;
        messageBox.classList.add('friend-msg');

        const clonedMessageContainer = clone.querySelector('.msg-container');
        clonedMessageContainer.style='flex-direction: row-reverse';
    } else {
        image.src = `/public/uploads/${currentUserImage.value}`;
        messageBox.classList.add('self-msg');
    }
    messageContainer.appendChild(clone);
    messageContainer.scrollTop = messageContainer.scrollHeight;
}
