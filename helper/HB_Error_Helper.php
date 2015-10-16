<?php
/**
 * @author: Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão: 1.0
 * @copyright: (c) 2015,ewari.com.br
 * @description: Classe de Helper para Retornar erros do server.
 */
class HB_Error_Helper {

   public static function Erro_400($msg = null) {
      header("HTTP/1.0 404 Requisição inválida");
      die("<span style='color:red'>{$msg}</span>");
   }

   public static function Erro_401($msg = null) {
      header("HTTP/1.0 401 Não Autorizado");
      die("<span style='color:red'>{$msg}</span>");
   }

   public static function Erro_403($msg = null) {
      header("HTTP/1.0 403 Requisição inválida");
      die("<span style='color:orange'>{$msg}</span>");
   }

   public static function Erro_404($msg = null) {
      header("HTTP/1.0 404 Não encontrado");
      die("<span style='color:red'>{$msg}</span>");
   }

   public static function Erro_500($msg = null) {
      header("HTTP/1.0 500 Erro de server");
      die("<span style='color:red'>{$msg}</span>");
   }

     public static function  setError($severity, $message, $filepath, $line) {
      set_status_header(500);
      header("Content-Type: text/html;charset=UTF-8");
      // echo"<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css\">";
      echo "<div class=\"row\" style=\"margin-top:10px\"><div class=\"col-md-8 col-md-offset-2\"><div class=\"alert alert-danger\" role=\"alert\"><p><string>Mensagem: </strong>{$message}<br/>";
      echo "<string>Arquivo: </strong>{$filepath}<br/>";
      echo "<string>Linha: </strong>{$line}<br/>";
      echo "<string>severity: </strong>{$severity}<br/>";
      echo "</p></div></div></div>";
      exit(1); // EXIT_ERROR
   }

}
