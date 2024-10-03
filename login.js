document.getElementById('loginBtn').addEventListener('click', function() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    fetch('login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username, password }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Login exitoso');
            // Redirigir a inicio.html
            window.location.href = 'inicio.html';
        } else {
            alert('Credenciales incorrectas');
        }
    })
    .catch(error => console.error('Error:', error));
});

document.getElementById('registerBtn').addEventListener('click', function() {
    window.location.href = 'registro.html'; // Cambia 'registro.html' por la página que crees para el registro
});