<?php
/**
* Classe que representa Active Record de Pessoa
*
* @version    1.0
* @package    model/entidade
* @author     Bruno Lopes
* @since      23/01/2024
**/
class Pessoa extends TRecord{
    const TABLENAME = 'si_pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';
    
    const CREATEDAT = 'data_criacao';
    
    public function __construct( $id = NULL, $callObjectLoad = TRUE ){
        parent::__construct( $id, $callObjectLoad );
        parent::addAttribute( 'nome' );
        parent::addAttribute( 'data_nascimento' );
        parent::addAttribute( 'data_criacao' );
    } 
}
