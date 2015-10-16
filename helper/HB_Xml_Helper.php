<?php
/**
 * @author Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão:1.0
 * @copyright (c) 2015,ewari.com.br
 * Description of HB_Conveter
 */ 
class HB_Xml_Helper {
   
   public static function xml_encode($mixed, $header = true, $domElement = null, $DOMDocument = null) {
      if (is_null($DOMDocument)) { //Cria o objeto
         $DOMDocument = new DOMDocument("1.0", "UTF-8");
         $DOMDocument->formatOutput = true;
         self::xml_encode($mixed, $header, $DOMDocument, $DOMDocument);

         // Retira a declaração do header do XML $header = 'false'
         return ($header) ? $DOMDocument->saveXML() : $DOMDocument->saveXML($DOMDocument->documentElement);
      } else { // Popula
         if (is_array($mixed)) {

            foreach ($mixed as $index => $mixedElement) {
               if (is_int($index)) {

                  if ($index == 0) {
                     $node = $domElement;
                  } else {
                     $node = $DOMDocument->createElement($domElement->tagName);
                     $domElement->parentNode->appendChild($node);
                  }
               } else {

                  $plural = $DOMDocument->createElement($index);
                  $domElement->appendChild($plural);
                  $node = $plural;

                  if (rtrim($index, '') !== $index) {
                     $singular = $DOMDocument->createElement(rtrim($index, ''));
                     $plural->appendChild($singular);
                     $node = $singular;
                  }
               }

               self::xml_encode($mixedElement, $header, $node, $DOMDocument);
            }
         } else {
            $domElement->appendChild($DOMDocument->createTextNode($mixed)); // Indere o valor dentro da tag
         }
      }
   }
   
}
