<tr>
    <td class="align-middle" col-data="username"><?php echo $this->username; ?></td>
    <td class="align-middle" col-data="email"><?php echo $this->email; ?></td>
    <td class="align-middle" col-data="rol"><?php echo ucfirst($this->rol->rol); ?></td>
    <td class="align-middle" col-data="profile"><?php echo $this->hasProfile() ? 'Si' : 'No'; ?></td>
    <td class="align-middle">
        <div class="btn-group">
            <button btn-action-type="send" btn-action="profile-create" btn-method="POST" btn-uri="/api/user/v1/profile?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-success" <?php echo $this->hasProfile() ? 'style="display:none;"' : ''; ?> title="Añadir perfil público"><i class="fas fa-plus-circle"></i></button>
            <button btn-action-type="send" btn-action="profile-lock" btn-method="PATCH" btn-uri="/api/user/v1/profile/lock?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-warning" <?php echo $this->isPublished() && $this->hasProfile() ? '' : 'style="display:none;"'; ?> title="Bloquear perfil público"><i class="fas fa-lock"></i></button>
            <button btn-action-type="send" btn-action="profile-unlock" btn-method="PATCH" btn-uri="/api/user/v1/profile/unlock?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-warning" <?php echo $this->isPublished() || !$this->hasProfile() ? 'style="display:none;"' : ''; ?> title="Desbloquear perfil público"><i class="fas fa-lock-open"></i></button>
            <a href="/participantes/ficha?participante=<?php echo $this->username; ?>" class="btn btn-info" title="Ver perfil" <?php echo $this->hasProfile() ? '' : 'style="display:none;"'; ?>><i class="fas fa-eye"></i></a>
            
        </div>
    </td>
    <td class="align-middle">
        <div class="btn-group">
            <a href="/admin/usuarios/editar?id=<?php echo $this->getID(); ?>" class="btn btn-primary" title="Editar Usuario"><i class="fas fa-user-edit"></i></a>
            <button btn-action-type="send" btn-action="pass-reset" btn-method="POST" btn-uri="/api/auth/v1/otp" btn-data="<?php echo htmlspecialchars(json_encode(array('usuario' => $this->username))); ?>" class="btn btn-warning" title="Restablecer contraseña"><i class="fas fa-key"></i></button>
            <button btn-action-type="send" btn-action="user-lock" btn-method="PATCH" btn-uri="/api/user/v1/user/lock?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-warning" <?php echo $this->isBlocked() ? 'style="display:none;"' : ''; ?> title="Bloquear Usuario"><i class="fas fa-lock"></i></button>
            <button btn-action-type="send" btn-action="user-unlock" btn-method="PATCH" btn-uri="/api/user/v1/user/unlock?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-warning" <?php echo $this->isBlocked() ? '' : 'style="display:none;"'; ?> title="Desbloquear Usuario"><i class="fas fa-lock-open"></i></button>
            <button btn-action-type="confirm" btn-action="user-delete" btn-method="DELETE" btn-uri="/api/user/v1/user?id=<?php echo $this->getID(); ?>" btn-data="{}" class="btn btn-danger" title="Borrar Usuario"><i class="fas fa-user-slash"></i></button>
        </div>
    </td>
</tr>