<?php

use Model\Usuario;

switch ($option) {
    case 'HOME':
        $class = '';
        $detail = false;
        break;
    case 'FICHA':
        $class = 'col-sm-6 col-md-4 col-lg-3';
        $detail = false;
        break;
    default:
        $class = 'col-sm-6 col-md-4 col-lg-3';
        $detail = true;
        break;
}
$user =  (Usuario::find($this->FK_id_user));
?>

<div class="<?php echo $class; ?> card-video video-widget" data-id="<?php echo $this->getID(); ?>">
    <img src="<?php echo $this->getImageURL(); ?>" alt="<?php echo $this->titulo; ?>" class="video-img">
    <div class="video-info d-flex align-items-center">
        <?php if ($detail) : ?>
            <a href="/participantes/ficha?participante=<?php echo $user->username; ?>" class="flex-shrink-0">
                <img src="<?php echo $user->getImageURL(); ?>" alt="<?php echo $user->username; ?>" class="img-circle img-thumbnail shadow-sm img-size-small">
            </a>
        <?php endif; ?>
        <div class="ml-1 flex-grow-1 d-flex flex-column">
            <h3 class="text-body fs-6 video-title"><?php echo $this->titulo; ?></h3>
            <?php if ($detail) : ?>
                <a class="video-user text-body" href="/participantes/ficha?participante=<?php echo $user->username; ?>"><?php echo $user->username; ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>