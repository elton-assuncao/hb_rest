<?php
/**
 * @author: Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão: 1.0
 * @copyright: (c) 2015,ewari.com.br
 * @description: Classe responsável por dar permissões de acesso ao usuario logado à Rotas solicitada
 */
class AllowPermission implements IPermission {

   /**
    * Regitrar nossas Rotas aqui
    */
   public function register() {           
      return array('');
   }

}
