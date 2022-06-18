<main>
    <div class="row pt-3">
        <aside class="col-sm-5 col-md-4 col-lg-3 d-flex flex-column justify-content-center align-items-center">
            <img src="<?php $participante->printImageURL(); ?>" alt="<?php echo $participante->username; ?>" class="img-circle img-thumbnail shadow-sm img-size-thumbnail">
            <h1 class="fs-3 p-0 my-2"><?php echo $participante->isPublished() ? '' : 'Vista Privada - ';
        echo $participante->username; ?></h1>
            <div class=" d-flex flex-row justify-content-center align-items-center">
                <?php foreach($participante->getEnlaces() as $enlace){
                    $enlace->print();
                }?>
            </div>
        </aside>
        <div class="col-sm">
            <?php echo $participante->descripcion; ?>
        </div>
    </div>
</main>
<section id="archivo-video" class="row">
        <?php 
            foreach($videos as $video){
                $video->printCard('FICHA');
            }
        ?>
</section>