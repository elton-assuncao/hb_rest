<?php

/**
 * @author Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão:1.0
 * @copyright (c) 2015,ewari.com.br
 * Description of HB_View_JSON
 */
class HB_View_JSON extends IView {

   var $type = 'Content-Type: text/json;charset=UTF-8';

   public function __construct($hash, $data, $lovs_array, $messenge_text) {
      $this->hash = $hash;      
      $this->data_array = $data;
      $this->lovs_array = $lovs_array;
      $this->messenge_text = $messenge_text;
   }

   public function execute() {
      $this->header = $this->type;
      $this->content = '{"hash":' . json_encode($this->hash) .
              ' ,"data":' . json_encode($this->data_array) .
              ', "lov":' . json_encode($this->lovs_array) .
              ', "menssage":' . json_encode($this->messenge_text) . ' }';

      return $this;
   }
   
}
