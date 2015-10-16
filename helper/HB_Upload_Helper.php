<?php

/**
 * @author: Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão: 1.0
 * @copyright: (c) 2015,ewari.com.br
 * @description: Classe que trabalha com armazenamento de arquivos. 
 * Sem utilizar banco de dados apenas no disco rigido;
 */
class HB_Upload_Helper {

   private $pasta;
   private $largura;
   private $altura;

   public function __construct($diretorio, $largura, $altura) {
      $this->pasta = $diretorio;
      $this->largura = $largura;
      $this->altura = $altura;
   }

   private function getExtensao($paramArquivo) {
      // retorna a extensao da imagem
      $arquivo = explode('.', $paramArquivo ['name']);
      $extensao = strtolower(end($arquivo));
      return $extensao;
   }

   public function ehImagem($extensao) {
      $extensoes = array(
          'image/gif',
          'image/jpeg',
          'image/jpg',
          'image/png'
      ); // extensoes permitidas
      if (in_array($extensao, $extensoes)):
         return true;
      endif;
   }

   // largura, altura, tipo, localizacao da imagem original
   private function redimensionar($imgLarg, $imgAlt, $tipo, $img_localizacao) {
      // descobrir novo tamanho sem perder a proporcao
      if ($imgLarg > $imgAlt) {
         $novaLarg = $this->largura;
         $novaAlt = round(($novaLarg / $imgLarg) * $imgAlt);
      } elseif ($imgAlt > $imgLarg) {
         $novaAlt = $this->altura;
         $novaLarg = round(($novaAlt / $imgAlt) * $imgLarg);
      } else {
         // altura == largura
         $novaAltura = $novaLargura = max($this->largura, $this->altura);
      }
      // cria uma nova imagem com o novo tamanho
      $novaimagem = imagecreatetruecolor($novaLarg, $novaAlt);

      switch ($tipo) {
         case 1 : // gif
            $origem = imagecreatefromgif($img_localizacao);
            imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $novaLarg, $novaAlt, $imgLarg, $imgAlt);
            imagegif($novaimagem, $img_localizacao);
            break;
         case 2 : // jpg
            $origem = imagecreatefromjpeg($img_localizacao);
            imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $novaLarg, $novaAlt, $imgLarg, $imgAlt);
            imagejpeg($novaimagem, $img_localizacao);
            break;
         case 3 : // png
            $origem = imagecreatefrompng($img_localizacao);
            imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $novaLarg, $novaAlt, $imgLarg, $imgAlt);
            imagepng($novaimagem, $img_localizacao);
            break;
      }
      // destroi as imagens criadas image
      imagedestroy($novaimagem);
      imagedestroy($origem);
   }

   public function salvar($requestData, $grupo, $texto) {
      $extensao = $this->getExtensao($requestData);
      // localizacao do arquivo
      $diretorioDestino = $this->getDestino($grupo);
      $arquivoDestino = $this->getDestino($grupo, $requestData ['name']);

      if (!is_dir($diretorioDestino)) {
         mkdir($diretorioDestino, 0777);
      }

      // configurar o upload-max-filesize', '10M' em php.ini
      // move o arquivo
      if (!move_uploaded_file($requestData ['tmp_name'], $arquivoDestino . "." . $extensao)) {
         if ($requestData ['error'] == 1) {
            $v = (ini_get('upload_max_filesize'));
            return show_error("Tamanho excede os " . $v . " permitidos. Para aumentar o tamanho dos uploads das imagens entre em contato com o suporte.", 404);
         } else {
            return show_error("Erro {$requestData ['error']}", 404);
         }
      }

      if ($this->ehImagem($requestData['type'])) {
         // pega a largura, altura, tipo e atributo da imagem
         list ( $largura, $altura, $tipo, $atributo ) = getimagesize($arquivoDestino . "." . $extensao);
         // testa se é preciso redimensionar a imagem
         if (($largura > $this->largura) || ($altura > $this->altura)) {
            $this->redimensionar($largura, $altura, $tipo, $arquivoDestino . "." . $extensao);
         }
      }

      // cria txt da descricao do arquivo
      $fp = fopen($arquivoDestino . ".txt", "a");
      // Escreve "exemplo de escrita"
      fwrite($fp, $texto);
      // Fecha o arquivo
      fclose($fp);

      return array(
          "mini" => $arquivoDestino . "." . $extensao,
          "thumbUrl" => $arquivoDestino . "." . $extensao,
          "caption" => $texto
      );
   }

   public function buscar($grupo, $nome = null) {

      $destino = $this->getDestino($grupo, $nome);

      $listaArquivos = array();

      if (is_dir($destino)) {

         $d = dir($destino);

         while (false !== ($entry = $d->read())) {

            if (substr($destino . $entry, - 1) != ".") {
               if (substr($entry, - 3) != "txt") {
                  $texto = $this->PegarTexto($destino . $entry);
                  $array = array(
                      "mini" => $destino . $entry,
                      "thumbUrl" => $destino . $entry,
                      "caption" => $texto
                  );
                  array_push($listaArquivos, $array);
               }
            }
         }
         $d->close();
      }

      return $listaArquivos;
   }

   private function PegarTexto($destino) {
      $texto = explode('.', $destino);
      $extensao = "." . end($texto);
      $nome = str_replace($extensao, ".txt", $destino);
      $linha = "";
      if (is_file($nome)) {
         $ponteiro = fopen($nome, "r");
         while (!feof($ponteiro)) {
            $linha .= fgets($ponteiro, 4096);
         }
         fclose($ponteiro);
      }
      return trim($linha);
   }

   public function excluir($caminhoAbsImagem) {
      if (is_file($caminhoAbsImagem)) {
         // apaga a imagem
         if (!unlink($caminhoAbsImagem)) {
            return show_error("Erro ao excluir imagem");
         }
      }
      // apaga o txt
      $image = explode('.', $caminhoAbsImagem);
      $extensao = "." . end($image);
      $texto = str_replace($extensao, ".txt", $caminhoAbsImagem);
      if (is_file($texto)) {
         if (!unlink($texto)) {
            return show_error("Erro ao excluir o arquivo texto imagem");
         }
      }

      return "Imagem excluida com sucesso!";
   }

   public function excluirGrupo($grupo) {

      $dir = $this->getDestino($grupo);

      $files = array_diff(scandir($dir), array('.', '..'));

      foreach ($files as $file) {
         (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
      }
      if (!rmdir($dir)) {
         return show_error("Erro ao excluir diretório");
      }

      return "Diretório excluida com sucesso!";
   }

   public function alterarTexto($caminhoAbsImagem, $descricao) {
      $image = explode('.', $caminhoAbsImagem);
      $extensao = "." . end($image);
      $texto = str_replace($extensao, ".txt", $caminhoAbsImagem);

      $ponteiro = fopen($texto, "w");

      fwrite($ponteiro, trim($descricao));

      fclose($ponteiro);

      return $descricao;
   }

   private function getDestino($nomeGrupo, $nomeArquivo = null) {
      $destino = $this->pasta;
      $grupo = md5('sisco' . $nomeGrupo);
      $destino .= '/' . $grupo . '/';

      if ($nomeArquivo != null) {
         $nome = md5(time() . $nomeArquivo);
         $destino .= $nome;
      }
      return $destino;
   }

   public function conveteImagemToBase64($file) {
      if (isset($file['tmp_name'])) {
         $conteudo = file_get_contents($file['tmp_name']);
         $type = trim(str_replace("image/", "", $file['type']));
         return 'data:image/' . $type . ';base64,' . base64_encode($conteudo);
      }
      return null;
   }

}
