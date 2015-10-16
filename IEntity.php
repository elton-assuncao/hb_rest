<?php

/**
 * @author Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão:1.0
 * @copyright (c) 2015,ewari.com.br
 * @description of IEntity
 */
abstract class IEntity {

   /**
    * @description Convert Class to ARRAY
    */
   public function toArray($objeto = null) {
      if ($objeto == null) {
         return $this->toArray($this);
      }
      $function = new ReflectionClass($objeto);
      $array = (array) $objeto;
      $novArray = null;
      foreach ($array as $key => $value) {
         $novaChave = trim(str_replace($function->name, "", $key));
         if (is_object($value)) {
            $value = $this->toArray($value);
         }
         $novArray[$novaChave] = $value;
      }
      return $novArray;
   }
}
