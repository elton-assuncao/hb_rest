<?php
/**
 * @author: Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão: 1.0
 * @copyright: (c) 2015,ewari.com.br
 * @description: Classe de Helper Para seguraça da aplicação
 */
class HB_Security_Helper {

   public static function getCsrf($resquestData) {
      $metodo =filter_input(INPUT_SERVER ,'REQUEST_METHOD');
      $hash_request = $resquestData['hash'];
      $hash_atual = (isset($_SESSION["hash"])) ? $_SESSION["hash"] : null;
      $_SESSION["hash"] = sha1(md5("cli12ente412cohe1241cido21414" + time()));
       
      if ($hash_atual == null) {
         return $_SESSION["hash"];
      }
      if ($hash_atual != $hash_request && $metodo!="GET") {
         HB_Error_Helper::Erro_404('Operação ilegal. Requisição não veio de um cliente permitido: ');
      }
      return $_SESSION["hash"];
   }

}
