<?php
/**
 * Loja Active Record
 * @version    1.0
 * @package    model/loja
 * @author     brunosilva
**/
class Loja extends TRecord{
    const TABLENAME = 'loja';
    const PRIMARYKEY = 'id';
    const IDPOLICE = 'serial';
    
    private $nome;
    private $link_afiliado;
    private $logo;
    
    /**
    * Método construtor
    **/
    public function __construct($id = NULL, $callObjectLoad = TRUE){
        parent::__construct($id, $callObjectLoad);
        
        parent::addAttribute('nome');
        parent::addAttribute('link_afiliado');
        parent::addAttribute('logo');
        
    }
}
