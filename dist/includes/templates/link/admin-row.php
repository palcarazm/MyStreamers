<tr>
    <td class="align-middle"><?php $this->print();?> <?php echo $this->tipo; ?></td>
    <td class="align-middle">
        <div class="btn-group">
            <a href="/admin/usuarios/enlaces/editar?id=<?php echo $this->getID(); ?>" class="btn btn-primary" title="Editar tipo de enlace"><i class="fas fa-edit"></i></a>
            <button btn-action-type="confirm" btn-action="link-delete" btn-method="DELETE" btn-uri="/api/user/v1/link?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-danger" title="Borrar tipo de enlace"><i class="fas fa-trash"></i></button>
        </div>
    </td>
</tr>