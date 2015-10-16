<?php
/**
 * @author Elton Assunção <ewa.soft@gmail.com>, Elton John:ewati.com.br
 * @versão:1.0
 * @copyright (c) 2015,ewari.com.br
 * Description of IView
 */ 
abstract class IView {
    public $header;
    public $content;
    
    protected  $hash;
    protected $data_array;
    protected $lovs_array;
    protected $messenge_text;

    public abstract function execute();
}
