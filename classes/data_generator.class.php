<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of data_generator
 *
 * @author Oshan
 */
class DataGenerator
{

    private $connection;

    public function __construct()
    {
        $this->connection = new Mysql();
    }

    public function insertTo($tablename)
    {
        $dataGenerator = new DataGenerator();
        $table = $dataGenerator->scanTable($tablename);
        $data = $dataGenerator->populate($table);
        $q[] = $dataGenerator->buildSQL($data, $tablename);
        $dataGenerator->runSQL($queries);
    }

    public function scanTable($tableName)
    {

        $sql = "SHOW COLUMNS FROM $tableName";
        $columns = $this->connection->fetch($sql);
        $fks = $this->getFks($tableName);

        $columnMap = array();
        foreach ($columns as $column)
        {
            $columnMap[] = $this->generateColumnData($column, $fks);
        }
        return $columnMap;
    }

    private function generateColumnData($column, $fks)
    {
        //if auto incre
        if ((substr($column->Type, 0, 3) == 'int') && ($column->Extra == "auto_increment"))
        {
            return array('name' => $column->Field, 'type' => 'AI', 'default' => 'NULL');
        }
        elseif (isset($fks[$column->Field]))
        {
            return array('name' => $column->Field, 'type' => 'FK', 'values' => $fks[$column->Field]);
        }
        elseif ((substr($column->Type, 0, 3) == 'int'))
        {
            return array('name' => $column->Field, 'type' => 'INT');
        }
        elseif ((substr($column->Type, 0, 7) == 'decimal'))
        {
            return array('name' => $column->Field, 'type' => 'INT');
        }
        elseif (substr($column->Type, 0, 7) == 'varchar')
        {
            preg_match("/varchar\(([0-9]+)\)/", $column->Type, $matches);
            return array('name' => $column->Field, 'type' => 'String', 'length' => $matches[1]);
        }
        elseif (substr($column->Type, 0, 4) == 'enum')
        {
            preg_match_all("/\'(.*?)\'/", $column->Type, $matches);
            return array('name' => $column->Field, 'type' => 'list', 'items' => $matches[1]);
        }
        elseif ($column->Type == 'date')
        {
            return array('name' => $column->Field, 'type' => 'date');
        }
        elseif ($column->Type == 'datetime')
        {
            return array('name' => $column->Field, 'type' => 'datetime');
        }
        elseif (substr($column->Type, 0, 7) == 'tinyint')
        {
            return array('name' => $column->Field, 'type' => 'INT', 'bool' => true);
        }
        elseif ($column->Type == 'text')
        {
            return array('name' => $column->Field, 'type' => 'String', 'length' => 400);
        }
        elseif ($column->Type == 'longtext')
        {
            return array('name' => $column->Field, 'type' => 'String', 'length' => 400);
        }
        else
        {
            echo 'asd';
            var_dump($column);
            exit;
        }
    }

    public function populate($table, $size = 1, $sequal = true)
    {
        $records = array();
        for ($x = 0; $x < $size; $x++)
        {
            $record = array();
            foreach ($table as $column)
            {
                $column = Tools::toObj($column);
                $record[$column->name] = DataDummy::getData($column);
            }
            $records[] = $record;
        }
        return $records;
    }

    public function buildSQL($records, $table_name, $merge = false)
    {
        $queries = array();
        foreach ($records as $record)
        {
            $queries[] = $this->connection->buildInsertQuery($record, $table_name);
        }
        if ($merge)
        {
            return implode("\n", $queries);
        }
        return $queries;
    }

    public function runSQL($records)
    {
        foreach ($records as $sql)
        {
            $this->connection->query($sql);
        }
    }

    private function getFks($tableName)
    {
        list($schema, $tableName) = explode('.', $tableName);

        $sql = "SELECT 
                    TABLE_SCHEMA, TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME,
                    REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME 
                FROM 
                    information_schema.KEY_COLUMN_USAGE  
                WHERE 
                    information_schema.KEY_COLUMN_USAGE.TABLE_SCHEMA = '" . $schema . "' AND 
                    information_schema.KEY_COLUMN_USAGE.TABLE_NAME = '$tableName';";
        $columns = $this->connection->fetch($sql);
        $table = array();
        foreach ($columns as $column)
        {
            $table[$column->COLUMN_NAME] = $column;
        }
        return $table;
    }

    //put your code here
}

?>
