<?php

use Model\Rol;
use Model\Usuario;
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>Administración de <?php echo $obj_type['plural']; ?></h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<main class="content">

    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de <?php echo $obj_type['singular']; ?></h3>

            <!-- <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div> -->
        </div>
        <div class="card-body table-responsive">
            <table class="data table table-hover text-nowrap">
                <thead>
                    <tr>
                        <?php foreach ($header_list as $header) : ?>
                            <th><?php echo $header; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($obj_list as $obj) {
                        $obj->printRow();
                    } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <?php foreach ($header_list as $header) : ?>
                            <th><?php echo $header; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

</main>