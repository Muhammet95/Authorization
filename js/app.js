const origin = window.origin;
var notifier_timeout;

document.addEventListener('DOMContentLoaded', async () => {
    me();
    document.querySelector('.container_1 .submit')
        .addEventListener('click', login);

    document.querySelector('.container_2 .logout')
        .addEventListener('click', logout);
});

function me() {
    fetch(origin + '/auth/me', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
        .then(response => response.json())
        .then(response => {
            console.log(response);
            if (response.auth === true) {
                document.querySelector('.container_1').style = "display: none;";
                document.querySelector('.container_2').style = "display: flex;";
            }
            else {
                document.querySelector('.container_2').style = "display: none;";
                document.querySelector('.container_1').style = "display: flex;";
            }
        })
        .catch(error => console.log(error));
}

function login() {
    const login = document.querySelector('#login').value;
    const password = document.querySelector('#password').value;

    if (login.length === 0 || password.length === 0)
        notifier('Электронный адрес или пароль пустые.');

    fetch(origin + '/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=utf-8',
        },
        body: JSON.stringify({
            email: login,
            password: password,
        })
    })
        .then(response => response.json())
        .then(response => {
            if (response.status === 'success') {
                me();
            }
            notifier(response.message);
        })
        .catch(error => console.log(error));
}

function logout() {
    fetch(origin + '/logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=utf-8',
        }
    })
        .then(response => response.json())
        .then(response => {
            if (response.logout === true)
                me();
        })
        .catch(error => console.log(error));
}

function notifier(message) {
    const elem = document.querySelector('#notifier');
    elem.innerHTML = message;
    elem.style = 'display: flex;opacity: 1;';
    setTimeout(() => {
        elem.style = 'opacity: 0;display:none';
    }, 5000);
}