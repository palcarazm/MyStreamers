<?php

namespace Model;

class TipoEnlace extends ActiveRecord
{
    protected static String $table = 'enlaces';
    protected static String $date ='actualizado';
    protected static array $constraints = array('users_x_enlaces'=>'FK_id_enlace');
    protected static String $defaultOrder = 'PK_id_enlace';
    protected static array $colDB = ['PK_id_enlace', 'icono', 'tipo', 'actualizado'];
    protected static String $PK = 'PK_id_enlace';

    protected int|null $PK_id_enlace;
    public string $icono;
    public string $tipo;
    protected string|null $actualizado;

    public function __construct($args = [])
    {
        $this->PK_id_enlace = is_null($args['PK_id_enlace'] ?? null) ? null : (int) $args['PK_id_enlace'];
        $this->icono = (string) ($args['icono'] ?? '');
        $this->tipo = (string) ($args['tipo'] ?? '');
        $this->actualizado = is_null($args['actualizado'] ?? null) ? null : (string) $args['actualizado'];
    }

    /**
     * Imprime el icono del tipo de enlace
     *
     * @return void
     */
    public function print(): void
    {
        echo '<i class="' . $this->icono . '"></i>';
    }

    /**
     * Imprime la fila del tipo de enlace actual de la tabla de enlaces
     *
     * @return void
     */
    public function printRow(): void
    {
        include TEMPLATES_DIR . '/link/admin-row.php';
    }
}