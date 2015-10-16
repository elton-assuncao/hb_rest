<?php

/**
 * @author Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão:1.0
 * @copyright (c) 2015,ewari.com.br
 * Description of HB_View_HTML
 */
class HB_View_HTML extends IView {

   var $type = 'Content-Type: text/html;charset=UTF-8';

   public function __construct($hash, $data_array, $lovs_array, $messenge_text) {
      $this->hash = $hash;
      $this->data_array = $data_array;
      $this->lovs_array = $lovs_array;
      $this->messenge_text = $messenge_text;
   }

   public function execute() {
      $this->header = $this->type;
      $this->content = $this->data_array;
      return $this;
   }

}
