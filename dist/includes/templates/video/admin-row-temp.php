<?php if(!is_null($id)):?>
<tr>
    <td class="align-middle" col-data="imagen"><img src="<?php echo $imagen;?>" alt="<?php echo $titulo;?>"></td>
    <td class="align-middle" col-data="titulo"><?php echo $titulo; ?></td>
    <td class="align-middle fecha-widget" col-data="fecha" data-widget="<?php echo $fecha;?>"></td>
    <td class="align-middle" col-data="estado"><?php echo $added ? 'Añadido' : 'No Añadido'; ?></td>
    <td class="align-middle">
        <div class="btn-group">
            <?php $data = array('id'=>$id,'titulo'=>$titulo,'fecha'=>$fecha);?>
            <button btn-action-type="send" btn-action="video-add" btn-method="POST" btn-uri="/api/video/v1/video?id=<?php echo $userID; ?>" btn-data="<?php echo htmlspecialchars(json_encode($data));?>" class="btn btn-success" <?php echo $added ? 'style="display:none;"' : ''; ?> title="Añadir video"><i class="fas fa-plus-circle"></i></button>
        </div>
    </td>
</tr>
<?php endif; ?>