<main>
    <h1><?php echo $archivo_titulo;?></h1>
    <?php echo is_null($archivo_descripcion) ? '' : '<div>'.$archivo_descripcion.'</div>';?>
    <div class="row" id="archivo-<?php echo $archivo_item;?>">
        <?php 
            foreach($archivo as $item){
                $item->printCard();
            }
        ?>
    </div>
</main>
