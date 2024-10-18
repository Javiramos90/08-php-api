const API_URL = 'http://localhost/08-php-api/controllers/directores.php';
const errorElement = document.getElementById('createError');

function limpiarHTML(str){
    return str.replace(/[^\w. @-]/gi, function(e) {
        return '&#' + e.charCodeAt(0) + ';';
    });
}

function validarNombre(nombre){
    return nombre.length >= 2 && nombre.length <= 50;
}

function validarApellido(apellido){
    return apellido.length >= 2 && apellido.length <= 50;
}
function esEntero(str) {
    return /^\d+$/.test(str);
}

function validarFecha(fecha){
    return fecha;
}

function validarBiografia(biografia){
    return biografia.length < 50000;
}
function getDirectores(){
    fetch(API_URL)
        .then(response=> response.json())
        .then(directores => {
            const tableBody = document.querySelector('#directorTable tbody');
            tableBody.innerHTML = '';
            directores.forEach(director => {
                const sanitizedNombre = limpiarHTML(director.nombre);
                const sanitizedApellido = limpiarHTML(director.apellido);
                const sanitizedBiografia = limpiarHTML(director.biografia);
                tableBody.innerHTML += `
                    <tr data-id="${director.id}">
                        <td>
                            ${director.id}
                        </td>
                        <td>
                            <span class="listado">${sanitizedNombre}</span>
                            <input class="edicion" type="text" value="${sanitizedNombre}">
                        </td>
                        <td>
                            <span class="listado">${sanitizedApellido}</span>
                            <input class="edicion" type="text" value="${sanitizedApellido}">
                        </td>
                         <td>
                            <span class="listado">${director.f_nacimiento}</span>
                            <input class="edicion" type="date" value="${director.f_nacimiento}">
                        </td>
                         <td>
                            <span class="listado">${sanitizedBiografia}</span>
                            <textarea class="edicion">${sanitizedBiografia}</textarea>
                        </td>
                        <td class="td-btn">
                            <button class="listado" onclick="editMode(${director.id})">Editar</button>
                            <button class="listado" onclick="deleteDirector(${director.id})">Eliminar</button>
                            <button class="edicion" onclick="updateDirector(${director.id})">Guardar</button>
                            <button class="edicion" onclick="cancelEdit(${director.id})">Cancelar</button>
                        </td>
                    </tr>
                `
            });

        })
        .catch(error => console.log('Error:', error));
}

function createDirectores(event){
    event.preventDefault();
    const nombre = document.getElementById('createNombre').value.trim();
    const apellido = document.getElementById('createApellido').value.trim();
    const fechaNacimiento = document.getElementById('createFechaNacimiento').value.trim();
    const biografia = document.getElementById('createBiografia').value.trim();

    if(!validarNombre(nombre)){
        errorElement.textContent = 'El nombre debe tener entre 2 y 50 caracteres.';
        return;
    }
    if(!validarApellido(apellido)){
        errorElement.textContent = 'El apellido debe tener entre 2 y 50 caracteres.';
        return;
    }
    if(!validarFecha(fechaNacimiento)){
        errorElement.textContent = 'La fecha no es valida';
        return;
    }
    if(!validarBiografia(biografia)){
        errorElement.textContent = 'La biografia es muy extensa';
        return;
    }

    errorElement.textContent = '';

    //envio al controlador los datos
    fetch(API_URL, {
        method: 'POST',
        headers: {
            'Content-Type' : 'application/json',
        },
        body: JSON.stringify({nombre, apellido, f_nacimiento: fechaNacimiento, biografia: biografia})
    })
    .then(response => response.json())
    .then(result => {
        console.log('Director creado: ', result);
        if(!esEntero(result['id'])){
            mostrarErrores(result['id']);
        }else{
            getDirectores();
        }
        event.target.reset();
    })
    .catch(error => {
        console.log('Error: ', JSON.stringify(error));
    })
}

function updateDirector(id){
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const newNombre = row.querySelector('td:nth-child(2) input').value.trim();
    const newApellido = row.querySelector('td:nth-child(3) input').value.trim();
    const newFechaNacimiento = row.querySelector('td:nth-child(4) input').value.trim();
    const newBiografia = row.querySelector('td:nth-child(5) textarea').value.trim();
    
    if(!validarNombre(newNombre)){
        alert("El nombre debe tener entre 2 y 50 caracteres.");
        return;
    }
    if(!validarApellido(newApellido)){
        alert("El apellido debe tener entre 2 y 50 caracteres.");
        return;
    }

    if(!validarFechaNacimiento(newFechaNacimiento)){
        alert('El fecha no es válido.');
        return;
    }
    if(!validarBiografia(newBiografia)){
        alert('La biografia es muy extensa');
        return;
    }

    fetch(`${API_URL}?id=${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type' : 'application/json',
        },
        body: JSON.stringify({nombre: newNombre, apellido: newApellido, f_nacimiento: newFechaNacimiento, biografia: newBiografia})
   }).then(response => response.json())
     .then(result => {
        console.log(result);
        console.log('Director actualizado', result);
        if(!esEntero(result['affected'])){
            mostrarErrores(result['affected']);
        }else{
            getDirectores();
        }
     })
     .catch(error => {
        alert('Error al actualizar al usuario. Por favor, inténtelo de nuevo.');
     });
}

function mostrarErrores(errores){
    arrayErrores = Object.values(errores);
    errorElement.innerHTML = '<ul>';
    arrayErrores.forEach(error => {
        return errorElement.innerHTML += `<li>${error}</li>`;
    });
    errorElement.innerHTML += '</ul>';
}

function editMode(id){
    const row = document.querySelector(`tr[data-id="${id}"]`);
    row.querySelectorAll('.edicion').forEach(ro => {
        ro.style.display = 'inline-block';
    })
    row.querySelectorAll('.listado').forEach(ro => {
        ro.style.display = 'none';
    })
}
function cancelEdit(id){
    const row = document.querySelector(`tr[data-id="${id}"]`);
    row.querySelectorAll('.edicion').forEach(ro => {
        ro.style.display = 'none';
    })
    row.querySelectorAll('.listado').forEach(ro => {
        ro.style.display = 'inline-block';
    })
}
function deleteDirector(id){
    if(confirm('¿Estás seguro de que quieres eliminar este director?')){
       fetch(`${API_URL}?id=${id}`, {
            method: 'DELETE',
       })
       .then(response => response.json())
       .then(result => {
            console.log('Director eliminado: ', result);
            getDirectores();
       })
       .catch(error => console.error('Error: ', error));
    }
}

document.getElementById('createForm').addEventListener('submit', createDirectores);

getDirectores();
