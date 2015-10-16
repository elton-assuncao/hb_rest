<?php
/**
 * @author Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão:1.0
 * @copyright (c) 2015,ewari.com.br
 * Description of HB_Header
 */ 
class HB_Header {   
    /**
    *
    * @var string
    * @description: Se for false ira trabalhar com retorno em xml; 
    */
   const JSON = 'Content-Type: text/json;charset=UTF-8';
   const XML = 'Content-Type: text/xml;charset=UTF-8';
   const XHTML = 'Content-Type: text/html;charset=UTF-8';

   public static function contentType() {    
      return filter_input(INPUT_SERVER, 'HTTP_ACCEPT');
   }
}
