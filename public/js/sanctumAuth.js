window.addEventListener('load', () => {
    /**
     *
     * @param {string} name
     * @returns
    */
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
    function login() {
        return request('/login', {
            method: 'POST',
            body: JSON.stringify({
                email: "asfd@gmail.com",
                password: "password"
            })
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
        .then(() => logout())
        .then(() => login())
        .then(() => request('/api/v1/users'))



});
