<?php

use Model\Rol;

$usuario = getAuthUser();
if (!is_null($usuario)):
?>
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <?php if($usuario->can(Rol::PERMS_CONFIG)):?>    
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
                        <i class="fas fa-info-circle nav-icon"></i>
                        <p>Sitio</p>
                    </a>
                </li>
            </ul>
        </li>
    <?php endif;?>
</ul>
<?php endif;?>