<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro - SIFER</title>
  <link rel="stylesheet" href="./css/style.css" />
  <link rel="stylesheet" href="./css/login.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <style>
    .error {
      display: none;
      color: red;
      margin-top: 5px;
      font-size: 0.9em;
    }
  </style>
</head>
<body class="login-page">
  <div class="login-container">
    <div class="login-card">
      <h2>Crear Cuenta</h2>
      
      <form id="registroForm" action="auntentificar_registro.php" method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" id="contraseña" name="contraseña" placeholder="Contraseña" required>
        <input type="password" id="confirmar" placeholder="Confirmar contraseña" required>
        <div id="errorMensaje" class="error">Las contraseñas no coinciden.</div>
        
        <button type="submit">Registrarse</button>

        <p class="register-link">¿Ya tenés cuenta? <a href="login.php">Iniciar sesión</a></p>
        <p class="register-link">O entrá como <a href="index.php">Invitado</a></p>
      </form>

      <script>
        const form = document.getElementById('registroForm');
        const pass = document.getElementById('contraseña');
        const confirm = document.getElementById('confirmar');
        const error = document.getElementById('errorMensaje');

        form.addEventListener('submit', (e) => {
          if (pass.value !== confirm.value) {
            e.preventDefault();
            error.style.display = 'block';
          } else {
            error.style.display = 'none';
          }
        });
      </script>

    </div>
  </div>
</body>
</html>
