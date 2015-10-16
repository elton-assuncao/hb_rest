<?php

/**
 * @author Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão:1.0
 * @copyright (c) 2015,ewari.com.br
 * Description of HB_View_XML
 */
class HB_View_XML extends IView {
   private $type = "Content-Type: text/html;charset=UTF-8";
   
   public function __construct($hash, $data_array, $lovs_array, $messenge_text) {
      $this->hash = $hash;
      $this->data_array = $data_array;
      $this->lovs_array = $lovs_array;
      $this->messenge_text = $messenge_text;
   }

   public function execute() {
      $body = array("hash" => $this->hash, "data" =>  $this->data_array , "lov" =>  $this->lovs_array , "menssage" => $this->messenge_text);
      $xml = array("xml" => $body);
      $this->header = $this->type;
      $this->content = HB_Xml_Helper::xml_encode($xml);
      return $this;
   }

}
