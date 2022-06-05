<?php

namespace Model;

class Video extends ActiveRecord
{
    protected static String $table = 'videos';
    protected static String $defaultOrder = 'fecha DESC';
    protected static array $colDB = ['PK_id_video', 'FK_id_user', 'titulo', 'fecha'];
    protected static String $PK = 'PK_id_video';
    protected static bool $isAuto_Increment = false;

    protected string $PK_id_video;
    public int $FK_id_user;
    public string | null $titulo;
    protected string | null $fecha;

    public function __construct($args = [])
    {
        $this->PK_id_video = (string) ($args['PK_id_video'] ?? '');
        $this->FK_id_user = (int) ($args['FK_id_user'] ?? 0);
        $this->titulo = is_null($args['titulo'] ?? null) ? null : (string) $args['titulo'];
        $this->fecha = is_null($args['fecha'] ?? null) ? null : (string) $args['fecha'];
    }

    /**
     * Devuelve la fecha de publicación
     *
     * @return string|null fecha
     */
    public function getFecha(): string|null
    {
        return $this->fecha;
    }

    /**
     * Establece la fecha
     *
     * @param string $fecha
     * @return bool Completado con éxito (S/N)
     */
    public function setFecha(string $fecha): bool
    {
        $fecha = strtotime($fecha);
        if ($fecha) {
            $this->fecha = date('c', $fecha);
            return $this->save();
        } else {
            return false;
        }
    }

    /**
     * Devuelve la url de la imagen
     *
     * @return String url de la imagen
     */
    public function getImageURL(): String
    {
        return 'https://i.ytimg.com/vi/' . $this->PK_id_video . '/mqdefault.jpg';
    }

    /**
     * Devuelve los vídeos de un usuario
     *
     * @param integer $userID
     * @return Array listado de vídeos
     */
    public static function findByUserID(int $userID): array
    {
        $query = "SELECT * FROM " . static::$table;
        if (!empty(static::$joins)) {
            foreach (static::$joins as $table => $FK) {
                $query .= " INNER JOIN " . $table . " ON FK_" . $FK . " = PK_"  . $FK;
            }
        }
        $query .= " WHERE FK_id_user = ${userID}";
        return self::query($query);
    }

    /**
     * Imprime la fila de un video Temporal
     *
     * @return void
     */
    public static function printRowTemp(array $videoTemp): void
    {
        $userID = $videoTemp['userID'] ?? null;
        $id = $videoTemp['id'] ?? null;
        $titulo = $videoTemp['titulo'] ?? null;
        $fecha = $videoTemp['fecha'] ?? null;
        $added = $videoTemp['added'] ?? null;
        $imagen = is_null($id) ? '' : 'https://i.ytimg.com/vi/' . $id . '/default.jpg';
        include TEMPLATES_DIR . '/video/admin-row-temp.php';
    }
}
