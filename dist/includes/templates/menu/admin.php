<?php

use Model\Rol;

$usuario = getAuthUser();
if (!is_null($usuario)) :
?>
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <?php if ($usuario->can(Rol::PERMS_USUARIOS)) : ?>
            <li class="nav-item">
                <div class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        Usuarios
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </div>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="/admin/usuarios/listar" class="nav-link">
                            <i class="nav-icon fas fa-list"></i>
                            <p>Listar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/usuarios/crear" class="nav-link">
                            <i class="nav-icon fas fa-user-plus"></i>
                            <p>Añadir</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <div class="nav-link">
                            <i class="nav-icon fas fa-link"></i>
                            <p>
                                Enlaces
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </div>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/admin/usuarios/enlaces/listar" class="nav-link">
                                    <i class="nav-icon fas fa-list"></i>
                                    <p>Listar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/admin/usuarios/enlaces/crear" class="nav-link">
                                    <i class="nav-icon fas fa-plus-circle"></i>
                                    <p>Añadir</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php if ($usuario->can(Rol::PERMS_CONFIG)) : ?>
            <li class="nav-item">
                <div class="nav-link">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>
                        Configuration
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </div>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="/admin/config/sitio" class="nav-link">
                            <i class="nav-icon fas fa-info-circle"></i>
                            <p>Sitio</p>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
<?php endif; ?>