<?php foreach($alertas as $key => $alerta): ?>
    <?php foreach($alerta as $menasje): ?>
        <p class="alerta <?php echo $key; ?>"><?php echo $menasje; ?></p>
    <?php endforeach; ?>
<?php endforeach; ?>