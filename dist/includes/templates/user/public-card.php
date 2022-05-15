<div class="col-sm-4 col-md-3 col-lg-2 card-user" data-id="<?php echo $this->getID(); ?>">
    <?php switch ($link) {
        case 'PROFILE':?>
            <a href="/participantes/ficha?participante=<?php echo $this->username; ?>" class="d-flex flex-column justify-content-center align-items-center">
            <?php break;
        case 'TWITCH':?>
            <a href="https://twitch.tv/<?php echo $this->twitch_user; ?>" class="d-flex flex-column justify-content-center align-items-center" target="_blank">
            <?php break;
        
        default:?>
            <a href="/participantes/ficha?participante=<?php echo $this->username; ?>" class="d-flex flex-column justify-content-center align-items-center">
            <?php break;
    }?>
    <img src="<?php $this->printImageURL(); ?>" alt="<?php echo $this->username; ?>" class="img-circle img-thumbnail shadow-sm img-size-thumbnail">
    <h3 class="text-body"><?php echo $this->username; ?></h3>
</a>
</div>