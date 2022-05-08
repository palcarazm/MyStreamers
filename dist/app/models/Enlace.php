<?php

namespace Model;

class Enlace extends ActiveRecord
{
    protected static String $table = 'users_x_enlaces';
    protected static array $joins = array('enlaces' => 'id_enlace');
    protected static String $defaultOrder = 'FK_id_enlace';
    protected static array $colDB = ['FK_id_user', 'FK_id_enlace', 'enlace'];
    protected static bool $isAuto_Increment = false;

    protected int $FK_id_user;
    protected int $FK_id_enlace;
    public TipoEnlace $tipo;
    public string $enlace;


    public function __construct($args = [])
    {
        $this->FK_id_user = (int) ($args['FK_id_user'] ?? null);
        $this->FK_id_enlace = (int) ($args['FK_id_enlace'] ?? null);
        $this->enlace = (string) ($args['enlace'] ?? '');
    }

    /**
     * Crea un objeto con los datos indicados
     *
     * @param mixed $record
     * @param array $fields
     * @return static
     */
    protected static function createObject(mixed $record, array $fields): static
    {
        $obj = new static;
        $tipo_values = [];
        foreach ($record as $key => $value) {
            if (property_exists($obj, $key) && $fields[$key] == static::$table) {
                $obj->$key = $value;
            } elseif ($fields[$key] == 'enlaces') {
                $tipo_values[$key] = $value;
            }
        }
        $obj->tipo = new TipoEnlace($tipo_values);
        return $obj;
    }

    /**
     * Carga los enlaces de un usuario
     *
     * @param String $id_user ID del usuario
     * @return Array
     */
    public static function findUserLinks(int $id_user): array
    {
        $query = "SELECT * FROM " . static::$table;
        if (!empty(static::$joins)) {
            foreach (static::$joins as $table => $FK) {
                $query .= " INNER JOIN " . $table . " ON FK_" . $FK . " = PK_"  . $FK;
            }
        }
        $query .= " WHERE FK_id_user = '${id_user}'";
        return self::query($query);
    }

    /**
     * Borra los enlaces de un usuario
     *
     * @param String $id_user ID del usuario
     * @return bool enlaces borrados (S/N)
     */
    public static function deleteUserLinks(int $id_user): bool
    {
        $result = true;
        $enlaces = self::findUserLinks($id_user);
        foreach ($enlaces as $enlace) {
            if (!$enlace->delete()) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Elimina el registro en la base de datos
     *
     * @return bool Completado con éxito (Si/No)
     */
    public function delete(): bool
    {
        $query = "DELETE FROM " . static::$table . " WHERE CONCAT(FK_id_user, '-',FK_id_enlace) = '" . $this->FK_id_user . '-' . $this->FK_id_enlace . "'";
        if (self::$db->query($query)) {
            return true;
        } else {
            static::$errors[] = self::$db->error;
            return false;
        }
    }
    
    public function print(): void
    {
        include TEMPLATES_DIR . '/link/public-button.php';
    }
}
