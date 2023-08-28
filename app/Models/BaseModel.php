<?php
/** @noinspection PhpUnused */

/** @noinspection SqlWithoutWhere */
/** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlResolve */

namespace App\Models;

use App\App;

abstract class BaseModel
{
    protected string $primaryKeyName = '';
    protected string $dbName = '';
    protected string $tableName = '';
    private array $dataChanges = array();
    private array $aggregateData = array();

    public function __construct()
    {
    }

    function filter_nulls($var): bool
    {
        if (is_array($var)) {
            return array_filter($var, array($this, 'filter_nulls')) !== array();
        } else {
            return $var !== null;
        }
    }

    /**
     * @return string
     */
    public function getPrimaryKeyName(): string
    {
        return $this->primaryKeyName;
    }

    /**
     * @param string $primaryKeyName
     * @return self
     */
    public function setPrimaryKeyName(string $primaryKeyName): self
    {
        $this->primaryKeyName = $primaryKeyName;
        return $this;
    }

    /**
     * @param string $dbName
     * @return self
     */
    public function setDbName(string $dbName): self
    {
        $this->dbName = $dbName;
        return $this;
    }

    /**
     * @param string $tableName
     * @return self
     */
    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @param bool $aggregate
     * @return $this
     */
    public function addDataChanges($key, $value, bool $aggregate = false): self
    {
        $this->dataChanges[$key] = $value;
        if ($aggregate) {
            $this->aggregateData[] = $key;
        }
        return $this;
    }

    public function create(): ?self
    {
        $dataSets = array();
        $params = array_filter(
            array_diff_key(
                get_object_vars($this),
                array_flip(array('dataChanges', 'aggregateData', 'primaryKeyName', 'dbName', 'tableName', 'db'))
            ),
            array($this, 'filter_nulls')
        );
        foreach ($params as $key => $value) {
            $dataSets[] = " `$key` = :$key ";
        }

        if (!empty($dataSets)) {
            $sql = "INSERT INTO
                        `" . $this->dbName . "`.`" . $this->tableName . "`
                    SET ";
            $sql .= implode(", ", $dataSets);

            $db = App::getDb();
            if ($db->prepare($sql, $params)) {
                $this->{$this->primaryKeyName} = $db->lastInsertId();
                return $this;
            }
        }
        return null;
    }

    public function save(): ?self
    {
        $dataSets = array();
        foreach ($this->dataChanges as $key => $value) {
            if (in_array($key, $this->aggregateData)) {
                $dataSets[] = " `$key` = `$key` + :$key ";
            } else {
                $dataSets[] = " `$key` = :$key ";
            }
        }

        if (!empty($dataSets)) {
            $params = array_merge(
                array($this->primaryKeyName => $this->{$this->primaryKeyName}),
                $this->dataChanges
            );

            $sql = "UPDATE
                    `" . $this->dbName . "`.`" . $this->tableName . "`
                SET ";
            $sql .= implode(", ", $dataSets);
            $sql .= " WHERE " . $this->primaryKeyName . " = :" . $this->primaryKeyName . ";";

            $db = App::getDb();

            if ($this->{$this->primaryKeyName}) {
                $db->prepare($sql, $params);
                return $this;
            } else {
                error_log(
                    json_encode(array(
                        'error' => "Save with ID:0",
                        'query' => $db->interpolateQuery($sql, $params),
                        'request' => array(
                            'request' => array(
                                '$_POST' => $_POST,
                                '$_GET' => $_GET,
                                'raw' => file_get_contents("php://input"),
                            ),
                        ),
                    )),
                    0
                );

            }
        }
        return null;
    }

    public function delete(): bool
    {
        if ($this->{$this->primaryKeyName}) {
            $sql = "DELETE FROM
                    `" . $this->dbName . "`.`" . $this->tableName . "`";
            $sql .= " WHERE " . $this->primaryKeyName . " = :" . $this->primaryKeyName . ";";

            $params = array($this->primaryKeyName => $this->{$this->primaryKeyName});

            $db = App::getDb();
            $db->prepare($sql, $params);
            return true;
        }
        return false;
    }

    function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        $separator = '_';
        $propertyCamelCase = lcfirst(str_replace($separator, '', ucwords($property, $separator)));
        if (property_exists($this, $propertyCamelCase)) {
            $this->$propertyCamelCase = $value;
        }
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return array_diff_key(
            get_object_vars($this),
            array_flip(array('dataChanges', 'aggregateData', 'primaryKeyName', 'dbName', 'tableName', 'db'))
        );
    }
}
