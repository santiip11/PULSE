
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>PULSE</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body class="inicio">

    <header class="barra">

        <h1>PULSE</h1>

        <nav>

            <?php if (isset($_SESSION['usuario_id'])) { ?>

                <a href="panel.php">Mi panel</a>
                <a href="cerrar_sesion.php">Cerrar sesión</a>

            <?php } else { ?>

                <a href="login.php">Iniciar sesión</a>
                <a href="registro.php">Registrarse</a>

            <?php } ?>

        </nav>

    </header>

    <section class="portada">

        <h2>BIENVENIDO A PULSE</h2>

        <p>Un espacio para descubrir y compartir tu pasión por la música.</p>

        <a href="registro.php" class="boton-principal">CREAR CUENTA</a>

    </section>

    <main>

        <section class="presentacion">

            <h2>DESCUBRÍ PULSE</h2>

            <p>
                La música nos acompaña todos los días.
                PULSE es un espacio para quienes disfrutan de ella.
            </p>

        </section>

        <section class="seccion-tarjetas">

            <h2>EL MUNDO DE LA MÚSICA</h2>

            <div class="cajitas">

                <div class="caja-inicio">

                    <img src="img/concierto.jpeg" alt="Concierto">

                    <h3>CONCIERTOS</h3>

                    <p>Viví la energía de la música en vivo.</p>

                </div>

                <div class="caja-inicio">

                    <img src="img/auriculares.jpeg" alt="Auriculares">

                    <h3>TU MÚSICA</h3>

                    <p>Encontrá tu propio estilo.</p>

                </div>

                <div class="caja-inicio">

                    <img src="img/estudio.jpg" alt="Estudio musical">

                    <h3>CREATIVIDAD</h3>

                    <p>La música es una forma de expresión.</p>

                </div>

            </div>

        </section>

    </main>

    <footer class="pie-inicio">

        <h2>PULSE</h2>

        <p>La música nos conecta.</p>

    </footer>

</body>
</html>
