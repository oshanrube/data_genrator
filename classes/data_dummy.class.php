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
class DataDummy
{

    public function getData($colunm)
    {
        $value = '';
        switch ($colunm->type)
        {
            case "AI":
                $value = NULL;
                break;
            case "INT":
                if(!isset($colunm->bool)){
                    $value = rand(0, 1);
                } else {
                    $value = rand(1, 10);
                }
                break;
            case "FK":
                $value = self::getFk($colunm->values);
                break;
            case "String":
                $value = self::getString($colunm->length);
                break;
            case "list":
                $value = $colunm->items[rand(0, (count($colunm->items) - 1))];
                break;
            case "date":
                $value = self::getDate();
                break;
            case "datetime":
                $value = self::getDatetime();
                break;
            default :

                var_dump($colunm);
                exit;
                break;
        }
        return $value;
    }

    public function getFk($colunm)
    {
        $connection = new Mysql();
        //get rec
        $sql = "SELECT `" . $colunm->REFERENCED_COLUMN_NAME . "` FROM `" . $colunm->REFERENCED_TABLE_SCHEMA . "`.`" . $colunm->REFERENCED_TABLE_NAME . "`";
        $records = $connection->fetch($sql);
        if (count($records) > 0)
        {
            $column = $colunm->REFERENCED_COLUMN_NAME;
            $rec = $records[rand(0, (count($records) - 1))];
            return $rec->$column;
        }
        else
        {
            exit('no records in reference');
            DataGenerator::insertTo($colunm->REFERENCED_TABLE_SCHEMA.'.'.$colunm->REFERENCED_TABLE_NAME);
            return getFk($colunm);
            
        }
    }

    public function getString($length)
    {
        global $text;
        $string = 'Test'.$text++;
        return substr($string, 0, $length);
    }

    public function getDate()
    {
        $days = rand(0, 60);
        return date("Y-m-d", strtotime("-" . $days . "days "));
    }

    public function getDatetime()
    {
        $days = rand(0, 60);
        $hours = rand(0, 60);
        $minutes = rand(0, 60);
        $string  = "-" . $days . "days " . $hours . " Hours " . $minutes . " Minutes ";
        return date("Y-m-d", strtotime($string));
    }

}