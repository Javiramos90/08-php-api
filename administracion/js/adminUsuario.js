const API_URL = 'http://localhost/08-php-api/controllers/usuarios.php'
/**
 * @param {*} str string
 * @returns string limpio de caracteres html
 * Limnpia una cadena de texto convirtiendo ciertos caracteres potencialmente peligrosos en sus equivalentes html seguros
 * [^...] coinciden con cualquier caracter que no este en el conjunto
 * \w Caracteres de palabra, letras numeros, guion bajo
 * . @- Admite punto, espacio, arroba y guion medio
 * /gi Flags para que la busqueda de caracteres sea global (g) e insensible a mayusculas y minusculas (i)
 * 
 * funcion(c){return '&#' + catches.charCodeAt(0) + ';'} crea para cada caracter su codigo ASCII
 */

function limpiarHTML(str) {
    return str.replace(/[^\w. @-]/gi, function (e) {
        return '&#' + e.charCodeAt(0) + ';';
    });
}

function validarEmail(email) {
    // buscar expresion regular
    return email;
}

function validarNombre(nombre) {
    return nombre.length >= 2 && nombre.length <= 50;
}

function esEntero(str) {
    return /^\d+$/.test(str);
}

function getUsers() {
    fetch(API_URL)
        .then(responsive => responsive.json())
        .then(users => {
            const tableBody = document.querySelector('#usersTable tbody');
            tableBody.innerHTML = '';
            users.forEach(user => {
                const sanitizedNombre = limpiarHTML(user.nombre);
                const sanitizedEmail = limpiarHTML(user.email);
                tableBody.innerHTML += `
                    <tr data-id="${user.id}" class= "view-mode">
                    <td>
                        ${user.id}
                    </td>    
                    <td>
                        <span class="listado">${sanitizedNombre}</span>
                        <input class="edicion" type="text" value="${sanitizedNombre}">
                    </td>
                    <td>
                        <span class="listado">${sanitizedEmail}</span>
                        <input class="edicion" type="email" value="${sanitizedEmail}">
                    </td>
                    <td class="td-btn">
                        <button class="listado" onclick="toggleEditMode(${user.id})">Editar</button>
                         <button class="listado" onclick="deleteUser(${user.id})">Eliminar</button>
                         <button class="edicion" onclick="updateUser(${user.id})">Guardar</button>
                         <button class="edicion" onclick="cancelEdit(${user.id})">Cancelar</button>
                         
                    </td>
                    </tr>
                `
            });

        })
        .catch(error => console.log("Error:", error))
}

function createUser(event) {
    event.preventDefault();
    const nombre = document.getElementById('createNombre').value.trim();
    const email = document.getElementById('createEmail').value.trim();
    const errorElement = document.getElementById('createError');

    if (!validarNombre(nombre)) {
        errorElement = textContent = 'El nombre debe tener entre 2 y 50 caracteres.';
        return;
    }
    if (!validarEmail(email)) {
        errorElement = textContent = 'El email no es valido.';
        return;

    }

    errorElement.textContent = '';

    //envio al controlador los datos
    fetch(API_URL, {
        method: 'POST',
        headers: {
            'content-type': 'application/json',
        },
        body: JSON.stringify({ nombre, email })
    })
        .then(response => response.json())
        .then(result => {
            console.log('Usuario creado: ', result);
            if (!esEntero(result['id'])) {
                errorElement.textContent = result['id'];
            }
            getUsers();
            event.target.reset();
        })
        .catch(error => {
            console.log('Error: ', error);
            errorElement.textContent = 'Error al crear al usuario, por favor, intentelo de nuevo';
        })
}
function updateUser(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const newNombre = row.querySelector('td:nth-child(2) input').value.trim();
    const newEmail = row.querySelector('td:nth-child(3) input').value.trim();
    const errorElement = document.getElementById('createError');
    if (!validarNombre(newNombre)) {
        alert("El nombre debe tener entre 2 y 50 caracteres.");
    }
    if (!validarEmail(newEmail)) {
        alert("El email no es valido.");
    }
    fetch(`${API_URL}?id=${id}`, {
        method: 'PUT',
        headers: {
            'content-type': 'application/json',
        },
        body: JSON.stringify({ nombre: newNombre, email: newEmail })
    })
        .then(response => response.json())
        .then(result => {
            console.log('Usuario actualizado', result);
                errorElement.textContent = result['affected'];
           
                getUsers();
            
        })
        .catch(error => {
            console.error('Error: ', error);
            alert('Error al actualizar al usuario. Por favor, intentelo de nuevo.');

        });
}

function toggleEditMode(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    row.querySelectorAll('.edicion').forEach(ro => {
        ro.style.display = 'inline-block';
    })
    row.querySelectorAll('.listado').forEach(ro => {
        ro.style.display = 'none';
    })
}
function cancelEdit(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    row.querySelectorAll('.edicion').forEach(ro => {
        ro.style.display = 'none';
    })
    row.querySelectorAll('.listado').forEach(ro => {
        ro.style.display = 'inline-block';
    })
}


function deleteUser(id) {
    if (confirm('¿Estas seguro de que quieres eliminar este usuario?')) {
        fetch(`${API_URL}?id=${id}`, {
            method: 'DELETE',
        })
            .then(response => response.json())
            .then(result => {
                console.log('Usuario eliminado: ', result);
                getUsers();
            })
            .catch(error => console.log('Error: ', error));
    }
}

document.getElementById('createForm').addEventListener('submit', createUser);

getUsers();