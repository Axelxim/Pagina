document.getElementById("loginBtn").addEventListener("click", function () {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  fetch("login.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ username, password }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Login exitoso");
        // Redirigir a inicio_entrenador.php
        window.location.href = data.redirect;  // Redirigir a la página correspondiente
      } else {
        // Mostrar mensaje si el usuario no está activado
        if (data.activado === false) {
          document.getElementById("message").innerText =
            "El usuario no está activado. Por favor verifica tu correo.";
        } else {
          // Mostrar mensaje de error en el div
          document.getElementById("message").innerText =
            data.error || "Credenciales incorrectas";
        }
      }
    })
    .catch((error) => console.error("Error:", error));
});

document.getElementById("registerBtn").addEventListener("click", function () {
  window.location.href = "registrar.php"; // Cambia 'registrar.php' por la página que crees para el registro
});
