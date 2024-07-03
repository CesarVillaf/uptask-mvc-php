<?php include_once __DIR__ . '/header-dashboard.php'; ?>

    <div class="contenedor-sm">
        
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <a href="/perfil" class="enlace">Volver a Perfil</a>

        <form class="formulario" method="POST" action="/cambiar-password">
            <div class="campo">
                <label for="password_actual">Password Actual</label>
                <input type="password" id="password_actual" name="password_actual" placeholder="Tu Password Actual">
            </div> <!-- .campo -->

            <div class="campo">
                <label for="password_nuevo">Password Nuevo</label>
                <input type="password" id="password_nuevo" name="password_nuevo" placeholder="Tu Password Nuevo">
            </div> <!-- .campo -->

            <input type="submit" value="Guardar Cambios">
        </form>
    </div> <!-- .contenedor-sm -->

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>