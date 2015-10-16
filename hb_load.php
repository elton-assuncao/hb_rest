<?php

/**
 * @author: Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão: 1.0
 * @copyright: (c) 2015,ewari.com.br
 * @description: Classe de Configuração do Sistema
 */
#####################Load CLASS##########################
define("DIR_PATH", APPPATH . "hb_rest");
date_default_timezone_set('America/Sao_Paulo');

function __autoload($class) {
   //Carrega arquivos do diretorio path;
   if (is_dir(DIR_PATH) && !is_file(DIR_PATH)) {
      $file = DIR_PATH . DIRECTORY_SEPARATOR . $class . '.php';
      if (is_file($file) && !is_dir($file)) {
         require_once($file);
         return;
      }
   }
   //Carrega arquivos do diretorio path;
   $childrens = array('config', 'helper', 'libs');
   foreach ($childrens as $dir) {

      if ($dir == 'libs') {
         $arrayLibs = explode(";", DIR_LIBS);
         foreach ($arrayLibs as $value) {
            $caminho_libs = DIR_PATH . DIRECTORY_SEPARATOR .$dir . DIRECTORY_SEPARATOR . $value;
            include_classe($caminho_libs, $class);
         }
      } else {
         $caminho_libs = DIR_PATH . DIRECTORY_SEPARATOR . $dir;
         include_classe($caminho_libs, $class);
      }
   }

   $dirController = APPPATH . "controllers" . DIRECTORY_SEPARATOR . "{$class}.php";
   if (!is_dir($dirController) && is_file($dirController)) {
      require_once($dirController);
   }
}

function include_classe($caminho_libs, $class) {
   if (is_dir($caminho_libs) && !is_file($caminho_libs)) :
      $file = $caminho_libs . DIRECTORY_SEPARATOR . $class . '.php';
      if (is_file($file) && !is_dir($file)):
         require_once($file);
         return;
      endif;
   endif;
}

#####################Personalização de Erros##########################

function hb_shutdown_handler() {
   $last_error = error_get_last();
   if (isset($last_error) &&
           ($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING))) {
      hb_error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
   }
}

function hb_exception_handler($exception) {
   $last_error = error_get_last();
   hb_error_handler($last_error['type'], $exception->getMessage(), $exception->getFile(), $exception->getLine());
   exit(1); // EXIT_ERROR
}

function hb_error_handler($severity, $message, $filepath, $line) {
   HB_Error_Helper::setError($severity, $message, $filepath, $line);
}

include_once(DIR_PATH . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "HB_Config.php");


#################################Partial CodeIgniter######################################  
#################################Caso utilize Sistema de Rotas Rest api#############################
#################################Não testado por completo com coldigniter#################
if (MAPPER_ROUTER):
   //$hb =  new InitLoad();
   require_once (DIR_PATH . DIRECTORY_SEPARATOR . "codeIgniterPartial.php");
   $mapa = new HB_Mapper($RTR, $URI);
   $mapa->router();
   exit;
   
endif;