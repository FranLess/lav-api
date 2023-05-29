import './bootstrap';

const form = document.getElementById('form');
const inputMessage = document.getElementById('input-message');
const listMessage = document.getElementById('list-messages');

const inputEmail = document.getElementById('input-email');
const inputPassword = document.getElementById('input-password');
const avatars = document.getElementById('avatars');
const spanTyping = document.getElementById('span-typing');

const formLogin = document.getElementById('form-login');

formLogin.addEventListener('submit', (event) => {
    event.preventDefault();
    const email = inputEmail.value;
    const password = inputPassword.value;
    logout().then(() => {
        login(email, password)
            .then(() => {
                const channel = Echo.join('presence.chat.1');

                inputMessage.addEventListener('input', event => {
                    if (inputMessage.value.length === 0)
                        channel.whisper('stop-typing');
                    else
                        channel.whisper('typing', {
                            email: email
                        })
                })

                channel.here(users => {
                    usersOnline = [...users];
                    renderAvatars();
                    console.log({ users });
                    console.log('subscribed');
                })
                    .joining(user => {
                        console.log({ user }, 'joined');
                        usersOnline.push(user);
                        renderAvatars();
                        addChatMessage(user.name, 'has joined the room!');

                    })
                    .leaving(user => {
                        console.log({ user }, 'leaving');
                        usersOnline = usersOnline.filter(userOnline => userOnline.email !== user.email);
                        renderAvatars();
                        addChatMessage(user.name, 'has left the room.', 'grey');
                    })
                    .listen('.chat-message', (event) => {
                        console.log(event);
                        const message = event.message;
                        addChatMessage(event.user.name, message);
                    })
                    .listenForWhisper('typing', event => spanTyping.textContent = `${event.email} is typing...`)
                    .listenForWhisper('stop-typing', event => spanTyping.textContent = '')

            }).then(() => {
                request('/api/v1/users', {
                    method: 'GET'
                });

            });
    })

});

let usersOnline = new Array();

function userInitial(username) {
    const initial = username.slice(0, 1);
    return initial;
}

function renderAvatars() {
    avatars.textContent = "";
    usersOnline.forEach(user => {
        const span = document.createElement('span');
        span.textContent = userInitial(user.name);
        span.classList.add('avatar');
        avatars.append(span);
    });
}

function addChatMessage(name, message, color = "black") {
    const li = document.createElement('li');
    li.classList.add('d-flex', 'flex-col');
    const span = document.createElement('span');
    span.classList.add('message-author');
    span.textContent = name;

    const messageSpan = document.createElement('span');
    messageSpan.textContent = message;
    messageSpan.style.color = color;

    li.append(span, messageSpan);

    listMessage.append(li);
}



// Nos logueamos en la API para poder hacer uso de web sockets en canales privados
// Private channels

// Función que se encarga de devolver la cookie especificada
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2)
        return parts.pop().split(';').shift();

}

// Está función obtiene la cookie por nosotros y hace la petición a la api,
// ya con headers y credentials predeterminados
function request(url, options) {
    const csrf = getCookie('XSRF-TOKEN');
    const csrfToken = decodeURIComponent(csrf);
    return fetch(url, {
        headers: {
            'content-type': 'application/json',
            'accept': 'application/json',
            'X-XSRF-TOKEN': csrfToken
        },
        credentials: 'include',
        ...options
    });
}

// Petición a la API para hacer login
function login(email, password) {
    return request('/login', {
        method: 'POST',
        body: JSON.stringify({
            email: email,
            password: password
        })
    })
        .then(() => {
            document.getElementById('section-login').classList.add('hide');
            document.getElementById('section-chat').classList.remove('hide');
        });
}

// petición a la API para loguearse
function logout() {
    return request('/logout', {
        method: 'POST'
    });
}

// Petición para obtener la cookie de csrf token en el navegador
fetch('/sanctum/csrf-cookie', {
    headers: {
        'content-type': 'application/json',
        'accept': 'application/json'
    },
    'credentials': 'include'
})

form.addEventListener('submit', (event) => {
    event.preventDefault();
    const userInput = inputMessage.value;

    axios.post('/chat-message', {
        message: userInput
    });
});


function updatePost() {
    const socket = new WebSocket(`ws://${window.location.hostname}:6001/socket/update/post`);
    socket.onopen = function (event) {
        console.log(event)
    }
}



