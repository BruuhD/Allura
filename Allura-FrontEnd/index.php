<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Sesión - Allura</title>
    <link rel="stylesheet" href="css/login.css">
    <?php require_once('./config.php');?>
</head>
<body>
    <main>
        <div class="login">
            <div class="img-fondo"></div>
            <div class="login-content">
                <div class="login-img">
                    <div class="icono"></div>
                </div>
                <div class="login-datos">
                    <form id="loginForm"> 
                        <div class="usuario">
                            <label>Correo Electrónico</label>
                            <input type="text"  id="correo" required/>
                        </div>
                        <div class="clave">
                            <label>Contraseña</label>
                            <input type="password"  id="clave" required/>
                        </div>
                        <div class="btn-login">
                            <button type="submit"> Iniciar Sesión</button>
                        </div>
                    </form>
                </div>
                <div class="copyrigth">
                    <p>Copyrigth © 2025 • Desarrollado por Sethor</p>
                </div>
            </div>
        </div>
        
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const form = document.getElementById("loginForm");

        form.addEventListener("submit", async function (event) {
        event.preventDefault();

        const correo = document.getElementById("correo").value.trim();
        const clave = document.getElementById("clave").value.trim();

        if (!correo || !clave) {
            return Swal.fire({
            icon: "warning",
            title: "Campos vacíos",
            text: "Por favor ingrese usuario y contraseña",
            });
        }

        Swal.fire({
            title: "Validando...",
            text: "Por favor espera un momento",
            allowOutsideClick: false,
            didOpen: () => {
            Swal.showLoading();
            },
        });

        try {
            const response = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/login.php", {
                method: "POST",
                headers: {"Content-Type": "application/json","ngrok-skip-browser-warning": "true"},
                body: JSON.stringify({
                    username: correo,
                    password: clave,
                }),
            });

            const result = await response.json();

            Swal.close();

            if (result.status === true) {
                Swal.fire({
                    icon: "success",
                    title: "Bienvenido",
                    text: result.message || "Inicio de sesión exitoso",
                    confirmButtonText: "Continuar",
                }).then(() => {
                    localStorage.setItem("userAllura", JSON.stringify(result.data.user));
                    localStorage.setItem("token", result.data.token);
                    localStorage.setItem("token_exp", result.data.expires_at);
                    localStorage.setItem("suppliers", JSON.stringify(result.data.suppliers));
                    window.location.href = "pages/home.php";
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: result.message || "Credenciales inválidas",
                });
            }
        } catch (error) {
            console.error("Error de conexión:", error);
            Swal.fire({
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            });
        }
        });
    </script>

</body>
</html>