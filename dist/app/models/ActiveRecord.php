<?php

namespace Model;

use mysqli;

class ActiveRecord
{
    protected static mysqli $db;
    protected static String $table = '';
    protected static String $PK;
    protected static String $image;
    protected static String$defaultOrder = '';
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
        $this->validate();
        $attr = $this->sanitize();
        return is_null($this->id) ? $this->create($attr) : $this->update($attr);
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

        $result = self::$db->query($query);
        return (bool) $result;
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
        $query .= " WHERE " . self::$PK . " = '" . self::$db->escape_string($this->self::$PK) . "'";

        $result = self::$db->query($query);
        return (bool) $result;
    }

    /**
     * Elimina el registro en la base de datos
     *
     * @return bool Completado con éxito (Si/No)
     */
    public function delete(): bool
    {
        $this->deleteImage();
        $query = "DELETE FROM " . static::$table . " WHERE " . self::$PK . " = '" . self::$db->escape_string($this->self::$PK) . "'";
        $result = self::$db->query($query);
        return (bool) $result;
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
            if ($col == self::$PK) continue;
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
        if (!is_null($this->self::$PK)) {
            $this->deleteImage();
        }
        if ($image) {
            $this->self::$image = $image;
        }
    }

    /**
     * Devuelve la url de la imagen
     *
     * @return String url de la imagen
     */
    public function getImageURL(): String
    {
        return explode('public', IMG_DIR)[1] . $this->self::$image;
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
        if (file_exists(IMG_DIR . $this->self::$image)) {
            unlink(IMG_DIR . $this->self::$image);
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
        $query = "SELECT * FROM " . static::$table . " ORDER BY " . static::$defaultOrder;
        return self::query($query);
    }

    /** Consultar un número definido registros 
     *
     * @return array Registros
     */
    public static function allLimit(int $limit): array
    {
        $query = "SELECT * FROM " . static::$table . " ORDER BY " . static::$defaultOrder . " LIMIT " . $limit;
        return self::query($query);
    }

    /** Consultar un registro por su ID    
     *
     * @param  int $id
     * @return self
     */
    public static function find(int $id): self|null
    {
        $query = "SELECT * FROM " . static::$table . " WHERE id = ${id}";
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
        while ($item = $rs->fetch_assoc()) {
            $array[] = static::createObject($item);
        }
        // Liberar memoria
        $rs->free();

        return $array;
    }

    /**
     * Crea un objeto con los datos indicados
     *
     * @param mixed $record
     * @return static
     */
    protected static function createObject(mixed $record): static
    {
        $obj = new static;
        foreach ($record as $key => $value) {
            if (property_exists($obj, $key)) {
                $obj->$key = $value;
            }
        }
        return $obj;
    }
}
