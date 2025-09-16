<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Iniciar Sesión - SIFER</title>
  <link rel="stylesheet" href="./css/style.css" />
  <link rel="stylesheet" href="./css/login.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body class="login-page">
  <div class="login-container">
    <div class="login-card">
      <h2>Iniciar Sesión</h2>

       <form action="auntentificar_usuarioContraseña.php" method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="contraseña" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>

        <p class="register-link">¿No tenés cuenta? <a href="registro.php">Registrate aquí</a></p>
        <p class="register-link">O entrá como <a href="index.php">Invitado</a></p>
      </form>

    </div>
  </div>
</body>
</html>
