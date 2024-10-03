document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("registrationForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = {
            nombres: document.getElementById("nombres").value,
            apellidoPaterno: document.getElementById("apellidoPaterno").value,
            apellidoMaterno: document.getElementById("apellidoMaterno").value,
            telefono: document.getElementById("telefono").value,
            correo: document.getElementById("correo").value,
            username: document.getElementById("username").value,
            password: document.getElementById("password").value
        };

        fetch('registro.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la red');
            }
            return response.json();
        })
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
