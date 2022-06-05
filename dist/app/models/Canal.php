<?php

namespace Model;

class Canal extends ActiveRecord
{
    protected static String $table = 'canales';
    protected static String $defaultOrder = 'PK_id_canal';
    protected static array $colDB = ['PK_id_canal', 'FK_id_user'];
    protected static String $PK = 'PK_id_canal';
    protected static bool $isAuto_Increment = false;

    protected string $PK_id_canal;
    public int $FK_id_user;

    public function __construct($args = [])
    {
        $this->PK_id_canal = (string) ($args['PK_id_canal'] ?? '');
        $this->FK_id_user = (int) ($args['FK_id_user'] ?? 0);
    }

    /**
     * Devuelve los canales de un usuario
     *
     * @param integer $userID
     * @return Array listado de canales
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
}
