<?php
/**
* Classe que representa Active Record de Unidade 
*
* @version    1.0
* @package    model/painel_instrutores
* @author     Bruno Lopes
* @since      16/11/2023
**/
class PIUnidade extends TRecord
{
    const TABLENAME = 'unidade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    const CREATEDAT = 'data_cadastro';    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('data_cadastro');
        parent::addAttribute('usuario_cadastro');
    }
}
