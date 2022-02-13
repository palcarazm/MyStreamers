<?php

namespace Model;

use mysqli;

class ActiveRecord
{
    protected static mysqli $db;
    protected static String $table = '';
    protected static String $PK = '';
    protected static String $date ='';
    protected static array $joins = [];
    protected static bool $isAuto_Increment = true;
    protected static String $image;
    protected static String $defaultOrder = '';
    protected static array $colDB = [];
    protected static array $errors = [];


    /**
     * Establecer la conexión con la base de datos
     *
     * @param mysqli $database conexión con la base de datos
     * @return void
     */
    public static function connect(mysqli $database): void
    {
        self::$db = $database;
    }

    /**
     * Cerrar la conexión con la base de datos
     *
     * @return void
     */
    public static function close(): void
    {
        if (isset(self::$db) && !is_null(self::$db)) {
            self::$db->close();
        }
    }

    /**
     * Guarda el registro en la base de datos
     * Si es nuevo crea el registro, si no lo actualiza.
     *
     * @return bool Completado con éxito (Si/No)
     */
    public function save(): bool
    {
        if ($this->validate()) {
            if(static::$date != ''){$this->{static::$date} = date('Y-m-d H:i:s');}
            $attr = $this->sanitize();
            return is_null($this->{static::$PK}) ? $this->create($attr) : $this->update($attr);
        }
        return false;
    }

    /**
     * Crea un nuevo registro en la base de datos
     *
     * @param array $attr Arreglos de atributos
     * @return bool Completado con éxito (Si/No)
     */
    protected function create(array $attr): bool
    {
        $query  = "INSERT INTO " . static::$table . " ( ";
        $query .= join(" , ", array_keys($attr));
        $query .= " ) VALUES ('";
        $query .= join("' , '", array_values($attr));
        $query .= "')";
        if (self::$db->query($query)) {
            if (static::$isAuto_Increment) {
                $this->{static::$PK} = self::$db->insert_id;
            }
            return true;
        } else {
            static::$errors[] = self::$db->error;
            return false;
        }
    }

    /**
     * Actualiza el registro en la base de datos
     *
     * @param array $args Arreglos de atributos
     * @return bool Completado con éxito (Si/No)
     */
    protected function update(array $args): bool
    {
        $attr = [];
        foreach ($args as $key => $value) {
            $attr[] = "{$key} = '{$value}'";
        }

        $query = "UPDATE " . static::$table . " SET ";
        $query .= join(" , ", $attr);
        $query .= " WHERE " . static::$PK . " = '" . self::$db->escape_string($this->{static::$PK}) . "'";

        if (self::$db->query($query)) {
            return true;
        } else {
            static::$errors[] = self::$db->error;
            return false;
        }
    }

    /**
     * Elimina el registro en la base de datos
     *
     * @return bool Completado con éxito (Si/No)
     */
    public function delete(): bool
    {
        $this->deleteImage();
        $query = "DELETE FROM " . static::$table . " WHERE " . static::$PK . " = '" . self::$db->escape_string($this->{static::$PK}) . "'";
        if (self::$db->query($query)) {
            return true;
        } else {
            static::$errors[] = self::$db->error;
            return false;
        }
    }

    /**
     * Copia los datos en array
     *
     * @return array datos copiados
     */
    protected function attr(): array
    {
        $attr = [];
        foreach (static::$colDB as $col) {
            if ($col == static::$PK) continue;
            if (is_null($this->$col)) continue;
            $attr[$col] = $this->$col;
        }
        return $attr;
    }

    /**
     * Sanitiza los datos
     *
     * @return array datos sanitizados
     */
    protected function sanitize(): array
    {
        $attr = $this->attr();
        $santized = [];
        foreach ($attr as $key => $value) {
            $santized[$key] = self::$db->escape_string($value);
        }
        return $santized;
    }

    /**
     * Devuelve los errores registrados
     *
     * @return array errores
     */
    public static function errors(): array
    {
        return static::$errors;
    }

    /**
     * Valida los datos del registros
     *
     * @return array validación superada (Si/No)
     */
    public function validate(): bool
    {
        static::$errors = [];
        return empty(static::$errors);
    }
    /**
     * Comprueba si ya hay un registro con el valor indicado en la columna indicada
     *
     * @param String $col Columna que comprobar
     * @param String $type Tipo de valor ( i: integer , d: double , s: string, b: blob)
     * @return boolean Hay al menos un valor (Si/No)
     */
    protected function checkval(String $col, String $type): bool
    {
        $query = "SELECT COUNT(*) FROM " . static::$table . " WHERE " . $col . " = ? ;";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param($type, $this->$col);
        $stmt->execute();
        $stmt->bind_result($num);
        $stmt->fetch();
        $stmt->close();
        return $num > 0;
    }

    /**
     * Carga una imagen al registro
     *
     * @param String $image imagen en la carpeta de imagenes
     * @return void
     */
    public function setImage(String $image): void
    {
        //Elimina la imagen previa
        if (!is_null($this->{static::$PK})) {
            $this->deleteImage();
        }
        if ($image) {
            $this->{static::$image} = $image;
        }
    }

    /**
     * Devuelve la url de la imagen
     *
     * @return String url de la imagen
     */
    public function getImageURL(): String
    {
        return explode('public', IMG_DIR)[1] . $this->{static::$image};
    }

    /**
     * Imprime la url de la imagen
     *
     * @return void
     */
    public function printImageURL(): void
    {
        echo $this->getImageURL();
    }

    /**
     * Elimina la imagen
     *
     * @return void
     */
    protected function deleteImage(): void
    {
        if (file_exists(IMG_DIR . $this->{static::$image})) {
            unlink(IMG_DIR . $this->{static::$image});
        }
    }

    /**
     * Sincroniza los datos
     *
     * @param array $args datos a sincroniza los datos
     * @return void
     */
    public function sincronize(array $args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }

    /** Consultar todos los registros 
     *
     * @return array Registros
     */
    public static function all(): array
    {
        $query = "SELECT * FROM " . static::$table;
        if (!empty(static::$joins)) {
            foreach (static::$joins as $table => $FK) {
                $query .= " INNER JOIN " . $table . " ON FK_" . $FK . " = PK_"  . $FK;
            }
        }
        $query .= " ORDER BY " . static::$defaultOrder;
        return self::query($query);
    }

    /** Consultar un número definido registros 
     *
     * @return array Registros
     */
    public static function allLimit(int $limit): array
    {
        $query = "SELECT * FROM " . static::$table;
        if (!empty(static::$joins)) {
            foreach (static::$joins as $table => $FK) {
                $query .= " INNER JOIN " . $table . " ON FK_" . $FK . " = PK_"  . $FK;
            }
        }
        $query .= " ORDER BY " . static::$defaultOrder . " LIMIT " . $limit;
        return self::query($query);
    }

    /** Consultar un registro por su ID    
     *
     * @param  int $id
     * @return self
     */
    public static function find(int $id): self|null
    {
        $query = "SELECT * FROM " . static::$table;
        if (!empty(static::$joins)) {
            foreach (static::$joins as $table => $FK) {
                $query .= " INNER JOIN " . $table . " ON FK_" . $FK . " = PK_"  . $FK;
            }
        }
        $query .= " WHERE " . static::$PK . " = ${id}";
        $resultado = self::query($query);
        return array_shift($resultado);
    }

    /**
     * Carga los datos que conicidan con la consulta indicada
     *
     * @param String $query
     * @return array
     */
    public static function query(String $query): array
    {
        // Consultar
        $rs = self::$db->query($query);

        // Crear arreglo
        $array = [];
        $fields = [];
        foreach ($rs->fetch_fields() as $field) {
            $fields[$field->name] = $field->table;
        }

        while ($item = $rs->fetch_assoc()) {
            $array[] = static::createObject($item, $fields);
        }
        // Liberar memoria
        $rs->free();

        return $array;
    }

    /**
     * Crea un objeto con los datos indicados
     *
     * @param mixed $record
     * @param array $fields
     * @return object
     */
    protected static function createObject(mixed $record, array $fields)
    {
        $obj = new static;
        foreach ($record as $key => $value) {
            if (property_exists($obj, $key) && $fields[$key] == static::$table) {
                $obj->$key = $value;
            }
        }
        return $obj;
    }
}
