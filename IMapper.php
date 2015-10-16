<?php

/**
 * @author Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão:1.0
 * @copyright (c) 2015,ewari.com.br
 * Description of HB_Controller_Mapper
 */
abstract class IMapper {

   private $array_controller;
   private $array_action;
   private $ctrl;
   private $hashMapAction;
   private $class;
   private $method;
   private $URI;
   private $RTR;
   private $typeMetodo;
   private $injectorClass;

   public function __construct($RTR, $URI) {
      $this->URI = $URI;
      $this->RTR = $RTR;
      $this->class = $this->RTR->class;
      $this->method = $this->RTR->method;
      $this->register();
   }

   protected function openController($controller, $newController) {
      $this->array_controller[$newController] = $controller;
      $this->ctrl = $newController;
      return $this;
   }

   protected function aliasAction($metodo, $newMetodo, $typeMetodo = null, $injectorClass = null) {
      $this->array_action[$this->ctrl . '#' . $newMetodo] = $metodo;
      $this->typeMetodo[$this->ctrl . '#' . $newMetodo] = $typeMetodo;
      $this->injectorClass[$this->ctrl . '#' . $newMetodo] = $injectorClass;
      return $this;
   }

   protected function closeController() {
      $this->hashMapAction[$this->ctrl] = $this->array_action;
   }

   public function router() {

      //Procura o controller;
      if (array_key_exists($this->class, $this->array_controller)) {
         $nomeClass = $this->array_controller[$this->class];
      } else {
         $nomeClass = $this->class;
      }

      //procura o metodo;
      if (array_key_exists($this->class . '#' . $this->method, $this->array_action)) {
         $action = $this->array_action[$this->class . '#' . $this->method];
      } else {
         $action = $this->method;
      }

      $this->verificaTypeMetodo();

      if ($this->IsInjector()) {
         $param = $this->injetaClass();
         $metodoReflexionado = new ReflectionMethod($nomeClass, $action);
         $metodoReflexionado->invoke(new $nomeClass, $param);
      } else {
         call_user_func_array(array(new $nomeClass, $action), $this->getParam());
      }
   }

   /**
    * formas de usar paramentros
    * ou barra/values
    * com ?chave=valor
    */
   private function getParam() {
      $params = array_slice($this->URI->rsegments, 2);
      if (!isset($params) || empty($params)) :
         $urlParam = filter_input(INPUT_SERVER, 'QUERY_STRING');
         if (isset($urlParam) && !empty($urlParam)) :
            $array = explode("&", $urlParam);
            foreach ($array as $value):
               $item = explode("=", $value);
               $params[$item[0]] = $item[1];
            endforeach;
         endif;
      endif;
      return $params;
   }

   private function verificaTypeMetodo() {
      if (array_key_exists($this->class . '#' . $this->method, $this->typeMetodo)) {
         $metdo = strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
         $metod = strtolower($this->typeMetodo[$this->class . '#' . $this->method]);
         if ($metdo != $metod && $metod != null) {
            HB_Error_Helper::Erro_404("<p>Metodo não encontrado.</br> Verifique o REQUEST_METHOD.</p>");
         }
      }
   }

   private function injetaClass() {
      $metdo = strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
      if ($metdo != "put" && $metdo != "post") {
         HB_Error_Helper::Erro_500("<p><strong>Para injetar classe o REQUEST_METHOD deve ser POST ou PUT </p>");
      }
      if (INJECT_MODEL == null) {
         $caminho = APPPATH . "models";
      } else {
         $path = str_replace('\\', DIRECTORY_SEPARATOR, INJECT_MODEL);
         $path2 = str_replace('/', DIRECTORY_SEPARATOR, $path);
         $caminho = APPPATH . $path2 . DIRECTORY_SEPARATOR;
      }
      if (array_key_exists($this->class . '#' . $this->method, $this->injectorClass)) {
         $myClass = $this->injectorClass[$this->class . '#' . $this->method];
         if ($myClass != null) {
            $pathClass = $caminho . $myClass . '.php';
            if (is_file($pathClass) && !is_dir($pathClass)) {
               require_once ($pathClass);
               $object = new $myClass;
               $datas = json_decode(file_get_contents('php://input'), true);
               foreach ($datas as $key => $value) {
                  $key = ucfirst($key);
                  if (is_array($value)) {
                     $value = $this->preencheObjeto($key, $value, $caminho);
                  }
                  $metodoAhSerInvocado = 'set' . $key;
                  $metodoReflexionado = new ReflectionMethod($myClass, $metodoAhSerInvocado);
                  $retorno = $metodoReflexionado->invoke($object, $value);
               }
               return $object;
            } else {
               HB_Error_Helper::Erro_404("<p><strong>Classe não encontrada.</strong><br/> Verifique a classe a ser injetada ou a constante INJECT_MODEL.</p>");
            }
         }
      }
   }

   public function IsInjector() {
      $retorno = false;
      if (array_key_exists($this->class . '#' . $this->method, $this->injectorClass)) {
         $retorno = $this->injectorClass[$this->class . '#' . $this->method] != NULL;
      }
      return $retorno;
   }

   private function preencheObjeto($myClass, array $array, $caminho) {
      $pathClass = $caminho . $myClass . '.php';

      if (is_file($pathClass) && !is_dir($pathClass)) :
         require_once ($pathClass);
         $object = new $myClass;
         foreach ($array as $key => $value) :
            $key = ucfirst($key);
            if (is_array($value)) {                           
               $value = $this->preencheObjeto($key, $value, $caminho);
            }
            $metodoAhSerInvocado = 'set' . $key;         
            if (method_exists($object, $metodoAhSerInvocado) ||  REQUIRED_PROPERTIES_INJECTOR) {              
               $metodoReflexionado = new ReflectionMethod($myClass, $metodoAhSerInvocado);
               $retorno = $metodoReflexionado->invoke($object, $value);
            }
         endforeach;

         return $object;
      else :
         HB_Error_Helper::Erro_404("<p><strong>Classe não encontrada.</strong><br/>{$pathClass}</p>");
      endif;
      return NULL;
   }

}
