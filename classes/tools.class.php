<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of field
 *
 * @author Oshan
 */
class Tools
{

    static public function toObj($array)
    {
        $obj = new stdClass();
        foreach ($array as $k => $v)
        {
            $obj->$k = $v;
        }
        return $obj;
    }

}

?>
