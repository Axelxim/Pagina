document.addEventListener('DOMContentLoaded', function() {
    const usernameInput = document.getElementById("username");
    const correoInput = document.getElementById("correo");
    const usernameMessage = document.getElementById("usernameMessage");
    const correoMessage = document.getElementById("correoMessage");
    const tipoUsuarioInput = document.getElementById("tipoUsuario");

    // Validar username en tiempo real
    usernameInput.addEventListener("input", function() {
        const username = usernameInput.value;
        if (username.length > 0) {
            fetch(`verificar_usuario.php?username=${username}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    usernameMessage.textContent = "El nombre de usuario ya está registrado.";
                    usernameMessage.classList.add("text-danger"); // Añadir clase de Bootstrap para texto en rojo
                } else {
                    usernameMessage.textContent = "";
                    usernameMessage.classList.remove("text-danger"); // Remover clase si no hay error
                }
            })
            .catch(error => console.error('Error:', error));
        } else {
            usernameMessage.textContent = "";
            usernameMessage.classList.remove("text-danger");
        }
    });

    // Validar correo en tiempo real
    correoInput.addEventListener("input", function() {
        const correo = correoInput.value;
        if (correo.length > 0) {
            fetch(`verificar_correo.php?correo=${correo}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    correoMessage.textContent = "El correo ya está registrado.";
                    correoMessage.classList.add("text-danger"); // Añadir clase de Bootstrap para texto en rojo
                } else {
                    correoMessage.textContent = "";
                    correoMessage.classList.remove("text-danger"); // Remover clase si no hay error
                }
            })
            .catch(error => console.error('Error:', error));
        } else {
            correoMessage.textContent = "";
            correoMessage.classList.remove("text-danger");
        }
    });

    // Manejar el envío del formulario
    document.getElementById("registrationForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = {
            nombres: document.getElementById("nombres").value,
            apellidoPaterno: document.getElementById("apellidoPaterno").value,
            apellidoMaterno: document.getElementById("apellidoMaterno").value,
            telefono: document.getElementById("telefono").value,
            correo: document.getElementById("correo").value,
            username: document.getElementById("username").value,
            password: document.getElementById("password").value,
            tipoUsuario: document.getElementById("tipoUsuario").value // Cambiado a .value para obtener el valor del select
        };

        fetch('registro.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Registro exitoso. Revisa tu correo para activar tu cuenta.");
            } else {
                alert("Error en el registro: " + data.error);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('Error al enviar la solicitud.');
        });
    });
});
