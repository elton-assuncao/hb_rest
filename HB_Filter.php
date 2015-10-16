<?php
/**
 * @author: Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão:1.0
 * @copyright: (c) 2015,ewari.com.br
 * @Description: Classe responsavel pela segurança e filtro das Rotas; Essa Classe é utilizada pelo HB_controller;
 * Ela gerência classes como AllowAnonimos, e AllowPermission
 */
class HB_Filter {

   private static $request;

   public function __construct() {
      self::$request = filter_input(INPUT_SERVER, 'REQUEST_URI');     
   }
   
   public function isAutenticate($sessao) {
     return $sessao['$token'] != null && $sessao['cookies'] == filter_input(INPUT_COOKIE, 'ci_session');
   }
   
   /**
    * @Description: Verifica a permissão do usuário da sessão
    */
   public function isPermissionSession($session) {
      $permission = $session['permission'];
      return $this->executeValidation($permission);
   }

   /**
    * @Description: Checa permissão das classes Allowpermission e AllowAnonimos
    */
   public function isPermission(IPermission $classePermission) {
      $permission = $classePermission->register();
      return $this->executeValidation($permission);
   }

   public function executeValidation($permission) {
      $retorno = FALSE;
      for ($i = 0; $i < count($permission); $i++) {
         if (strrpos(strtolower(self::$request), strtolower($permission[$i])) > -1) {
            return TRUE;
         }
      }
      return $retorno;
   }
   
}
