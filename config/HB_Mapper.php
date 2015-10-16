<?php

/**
 * @author Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão:1.0
 * @copyright (c) 2015,ewari.com.br
 * Description of HB_Controller_Mapper
 */
class HB_Mapper extends IMapper {

   public function register() {
      $this->openController('WelcomeController', 'bem-vindos');
      $this->aliasAction('getView', 'get-view');
      $this->aliasAction('getType', 'tipo');
      $this->aliasAction('ImprimirTodos', 'print','get',null);      
      $this->closeController();
      
      $this->openController('ProdutoController', 'produto-view');
     // $this->aliasAction('get', 'visualizar','post',"Produto"); usar assim. Obs->Apenas post e put acietam classa no para bindar os argumentos;
      $this->aliasAction('post', 'adicionar','POST', 'Pessoa');
      $this->aliasAction('put', 'atualizar');
      $this->aliasAction('delete', 'excluir');
      $this->closeController();     
   }

}
