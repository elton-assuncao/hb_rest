<?php

set_error_handler('hb_error_handler');
set_exception_handler('hb_exception_handler');
register_shutdown_function('hb_shutdown_handler');

/**
 * @author: Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão: 1.0
 * @copyright: (c) 2015,ewari.com.br
 * @description: Classe abstrata para ser extentidas para gerenciar os controladores
 */
abstract class HB_Controller extends CI_Controller {

   protected $HttpRequest;
   private static $hash = null;
   private $view;

   public function __construct() {
      parent::__construct();
      $this->getPostArray();
      $this->filter();
      $this->view = null;
      if (TRANSACTION_DATABASE) {
         $this->db->trans_start();
      }
      
   }

   private function filter() {
      $filtro = new HB_Filter();
      //VERIFICA SESSÃO DE USUARIO E PERMISSOES
      if (AUTENTICATE) {
         $sessao = HB_Session_Helper::sessionGet();
         if (!$filtro->isPermission(new AllowAnonimos)) {
            if (!$filtro->isAutenticate($sessao)) {
               HB_Error_Helper::Erro_401("Não autenticado.");
            }
            if (!$filtro->isPermission(new AllowPermission()) && !$filtro->isPermissionSession($sessao)) {
               HB_Error_Helper::Erro_403('Permissão de acesso negada.');
            }
         }
      }
      //VERIFICA SEGURANÇA CSRF
      if (REQUEST_FORGERY) {
         self::$hash = HB_Security_Helper::getCsrf($this->HttpRequest);
      }
   }

   protected function HttpResponse($data_array = 'null', $lovs_array = 'null', $messenge_text = 'null') {
      $type = null;

      if (strripos(HB_Header::contentType(), "text/json") > -1):
         $type = new HB_View_JSON(self::$hash, $data_array, $lovs_array, $messenge_text);

      elseif (strripos(HB_Header::contentType(), "text/xml") > -1):
         $type = new HB_View_XML(self::$hash, $data_array, $lovs_array, $messenge_text);

      elseif (strripos(HB_Header::contentType(), "text/html") > -1):
         $type = new HB_View_HTML(self::$hash, $data_array, $lovs_array, $messenge_text);

      endif;
      if ($type == null) {
         HB_Error_Helper::Erro_403("HTTP_ACCEPT Não definido");
      }

      $this->view = $type->execute();
   }
  
   public function __destruct() {
      if ($this->view != null) {
         header($this->view->header);
         print_r($this->view->content);
      }
      if (TRANSACTION_DATABASE) {
         $this->db->trans_complete();
      }
   }

   #=====================Request==================================================#

   private function getPostArray() {
      if (!$this->isNullOrEmpty($_FILES)) {
         $this->HttpRequest = $_FILES['file'];
         $data = filter_input(INPUT_POST, 'data');
         // if (isset($_POST['data'])) {
         if (!$this->isNullOrEmpty($data)) {
            $this->HttpRequest['data'] = $data; //$_POST['data'];
         }
      } else {
         $this->HttpRequest = json_decode(file_get_contents('php://input'), true);
      }
      return $this->HttpRequest;
   }

   #==============================================================================#
   // *****Validacao******

   protected function isNullOrEmpty($value) {
      return (!isset($value) || empty($value));
   }

   protected function isValidateFields($array, $fieldsRequerids) {
      if ($this->isNullOrEmpty($array)) {
         echo $this->msgAdvertencia("Preencha os campos obrigatórios");
         return;
      }
      do {
         $alias = current($fieldsRequerids);
         $chave = key($fieldsRequerids);
         $values = $array [$chave];
         if ($this->isNullOrEmpty($values)) {
            echo $this->msgAdvertencia("Preencha o campo " . $alias);
            return;
         }
      } while (next($fieldsRequerids));
   }

   protected function RemoveElementArray($arr, $elementoAhRemover) {
      $newArr = array();
      foreach ($arr as $chave => $value) {
         if ($chave != $elementoAhRemover) {
            $newArr[$chave] = $value;
         }
      }
      return $newArr;
   }

   #==============================================================================# 
   // *****Mensagens do servidor******

   protected function msgSucesso($msg) {
      return '[{"Mensagem": "' . $msg . '","Tipo":"1"}]';
   }

   protected function msgAviso($msg) {
      return '[{"Mensagem": "' . $msg . '","Tipo":"2"}]';
   }

   protected function msgAdvertencia($msg) {
      HB_Error_Helper::Erro_403($msg);
   }

   protected function msgErro($msg, $status = 500) {
      HB_Error_Helper::Erro($msg, $status);
   }

}
