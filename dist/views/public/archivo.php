<main>
    <h1><?php echo $archivo_titulo;?></h1>
    <?php echo is_null($archivo_descripcion) ? '' : '<div>'.$archivo_descripcion.'</div>';?>
    <div class="row">
        <?php 
            foreach($archivo as $item){
                $item->printCard();
            }
        ?>
    </div>
</main>
