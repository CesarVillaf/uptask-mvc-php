<div class="contenedor restablecer">
    
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu nuevo password</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <?php if($mostrar): ?>
            <form class="formulario" method="POST">
                <div class="campo">
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="Tu Password" name="password">
                </div> <!-- .campo -->

                <input type="submit" value="Guardar Password" class="boton">
            </form>
        <?php endif; ?>
        
        <div class="acciones">
            <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
            <a href="/crear">¿Aún no tienes una cuenta? Obten una</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div> <!-- .contenedor -->