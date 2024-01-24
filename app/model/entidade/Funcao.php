<?php
/**
* Classe que representa Active Record de Função dentro do sistema
*
* @version    1.0
* @package    model/entidade
* @author     Bruno Lopes
* @since      24/01/2024
**/
class Funcao extends TRecord{
    const TABLENAME = 'si_funcao';
    const PRIMARYKEY = 'id';
    const IDPOLICE = 'max';
    
    public function __construct( $id = NULL, $callObjectLoad = TRUE ){
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute( 'nome' );
        
    }
    
}