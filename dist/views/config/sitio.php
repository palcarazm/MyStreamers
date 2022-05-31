<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>Configuración del sitio</h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<main class="content">

    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de configuración del sitio</h3>

            <!-- <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div> -->
        </div>
        <form action="/api/config/v1/site" method="put" class="border-secondary step-body" id="updateSite-form">
            <div class="card-body">
                <div class="form-group row">
                    <label for="titulo" class="col-sm-3 col-md-2 col-form-label">Título</label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Título del sitio" required value="<?php echo $titulo; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tema" class="col-sm-3 col-md-2 col-form-label">Tema</label>
                    <div class="col-sm-9 col-md-10">
                        <select name="tema" id="tema" class="custom-select form-control" required>
                            <option value="" disabled selected>--Selecione</option>
                            <?php foreach (getThemes() as $theme) : ?>
                                <option value="<?php echo $theme['folder']; ?>" <?php echo $theme['folder'] == $tema ? "selected" : ""; ?>><?php echo $theme['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <div class="form-group row">
                    <label for="descripcion" class="form-label col-12">Descripción del sitio</label>
                    <textarea name="descripcion" id="descripcion" class="col-12 custom-textarea form-control" required><?php echo $descripcion ?></textarea>
                </div>
                <fieldset class="mb-3">
                    <legend>Módulos</legend>
                    <div class="alert alert-info" role="alert">
                        Próximamente disponibles!
                    </div>
                    <div class="form-row">
                        <div class=" col-md-6 col-lg-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input form-control" id="eventos" name="eventos" <?php echo "disabled"; //echo $eventos ? "checked" : ""; 
                                                                                                                                ?>>
                                <label class="custom-control-label" for="eventos">Eventos</label>
                            </div>
                        </div>
                        <div class=" col-md-6 col-lg-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input form-control" id="noticias" name="noticias" <?php echo "disabled"; //echo $noticias ? "checked" : ""; 
                                                                                                                                ?>>
                                <label class="custom-control-label" for="noticias">Noticias</label>
                            </div>
                        </div>
                        <div class=" col-md-6 col-lg-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input form-control" id="normas" name="normas" <?php echo "disabled"; //echo $normas ? "checked" : ""; 
                                                                                                                            ?>>
                                <label class="custom-control-label" for="normas">Normas</label>
                            </div>
                        </div>
                        <div class=" col-md-6 col-lg-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input form-control" id="enlaces" name="enlaces" <?php echo "disabled"; //echo $enlaces ? "checked" : ""; 
                                                                                                                                ?>>
                                <label class="custom-control-label" for="enlaces">Enlaces personalizados</label>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary d-block ml-auto mr-0">Registrar información del sitio</button>
            </div>
            <!-- /.card-footer-->
        </form>
    </div>
    <!-- /.card -->

</main>