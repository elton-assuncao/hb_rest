<?php
/**
 * @author: Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão: 1.0
 * @copyright: (c) 2015,ewari.com.br
 * @description: Classe de Helper responsavel por gerenciar o controle de sessão do usuário
 */
class HB_Session_Helper {
   
   public static function sessionCreate($id, $nome, $permission) {
      $user = array('id' => $id,
          'nome' => $nome,
          'permission' => $permission,
          '$token' => md5(sha1('dados a serem criptografados')),
          'cookies' => filter_input(INPUT_COOKIE, 'ci_session'));
      $obj = & get_instance();
      $obj->session->set_userdata('user', $user);      
   }

   public static function sessionGet() {
      $obj = & get_instance();
      return $obj->session->userdata("user");
   }

   //Controle se sessão -->Destroi a sessao User ou a sessão passada no paramento
   public static function sessionDestruct() {
      $obj = & get_instance();
      $obj->session->unset_userdata('user');
   }

}
