<tr>
    <td class="align-middle" col-data="imagen"><img src="<?php echo $this->getImageURL(''); ?>" alt="<?php echo $this->titulo; ?>"></td>
    <td class="align-middle" col-data="titulo"><?php echo $this->titulo; ?></td>
    <td class="align-middle fecha-widget" col-data="fecha" data-widget="<?php echo $this->getFecha(); ?>"></td>
    <td class="align-middle">
        <div class="btn-group">
            <button btn-action-type="confirm" btn-action="video-delete" btn-method="DELETE" btn-uri="/api/video/v1/video?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-danger" title="Borrar video"><i class="fas fa-trash"></i></button>
        </div>
    </td>
</tr>