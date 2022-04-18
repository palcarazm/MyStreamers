<tr>
    <td class="align-middle"><?php echo $this->username; ?></td>
    <td class="align-middle"><?php echo $this->email; ?></td>
    <td class="align-middle"><?php echo ucfirst($this->rol->rol); ?></td>
    <td class="align-middle">
        <div class="btn-group">
            <a href="/admin/usuarios/editar?id=<?php echo $this->getID(); ?>" class="btn btn-primary" title="Editar Usuario"><i class="fas fa-user-edit"></i></a>
            <?php $btnData = array('usuario'=> $this->username);?>
            <button btn-action-type="send" btn-action="pass-reset" btn-method="POST" btn-uri="/api/auth/v1/otp" btn-data="<?php echo htmlspecialchars(json_encode($btnData)); ?>" class="btn btn-warning" title="Restablecer contraseña"><i class="fas fa-key"></i></button>
            <button btn-action-type="send" btn-action="user-lock" btn-method="PATCH" btn-uri="/api/user/v1/user/lock?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-warning" <?php echo $this->isBlocked() ? 'style="display:none;"' : ''; ?> title="Bloquear Usuario"><i class="fas fa-lock"></i></button>
            <button btn-action-type="send" btn-action="user-unlock" btn-method="PATCH" btn-uri="/api/user/v1/user/unlock?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-warning" <?php echo $this->isBlocked() ? '' : 'style="display:none;"'; ?> title="Desbloquear Usuario"><i class="fas fa-lock-open"></i></button>
            <button btn-action-type="confirm" btn-action="user-delete" btn-method="DELETE" btn-uri="/api/user/v1/user?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-danger" title="Borrar Usuario"><i class="fas fa-user-slash"></i></button>
        </div>
    </td>
</tr>