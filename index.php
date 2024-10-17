<?php
session_start(); // Iniciar la sesión
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
    <link rel="manifest" href="manifest.json">
    <title>Oni Mexicano - Aplicación de Rutinas</title>
</head>

<body>

    <div class="login-container">
        <div class="card login-card">
            <div class="card-header text-bg-danger p-3 text-center">
                Iniciar Sesión
            </div>
            <div class="card-body text-bg-dark">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="username" placeholder="Usuario">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" class="form-control">
                </div>

                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-outline-danger me-2" id="loginBtn">Login</button>
                    <button type="button" class="btn btn-outline-warning" id="registerBtn">Registrarse</button>
                </div>

                <div id="message" class="text-center mt-3"></div> <!-- Mensaje de error o éxito -->
            </div>
        </div>
    </div>

    <!-- Banner de instalación -->
    <div id="installBanner" class="alert alert-warning text-center" style="display: none;">
        <p>Instala nuestra aplicación para una mejor experiencia.</p>
        <button id="installBtn" class="btn btn-success">Instalar</button>
    </div>

    <script src="login.js"></script>
    <script>
        // Registro del Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js')
                    .then(registration => {
                        console.log('Service Worker registrado con éxito:', registration);
                    })
                    .catch(error => {
                        console.log('Error al registrar el Service Worker:', error);
                    });
            });
        }

        // Banner de instalación
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (event) => {
            // Prevenir que el navegador muestre el prompt automáticamente
            event.preventDefault();
            deferredPrompt = event;
            // Mostrar el banner de instalación
            document.getElementById('installBanner').style.display = 'block';
        });

        document.getElementById('installBtn').addEventListener('click', () => {
            document.getElementById('installBanner').style.display = 'none';
            // Mostrar el prompt de instalación nativo
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('El usuario aceptó instalar la PWA');
                } else {
                    console.log('El usuario rechazó instalar la PWA');
                }
                deferredPrompt = null;
            });
        });
    </script>

</body>

</html>