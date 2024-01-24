<?php
/**
* Classe que representa uma seção dentro da estrutura do sistema.
* @version    1.0
* @package    model/entidade
* @author     Bruno Lopes
* @since      24/01/2024
**/
class Secao extends TRecord{
    const TABLENAME = 'si_secao';
    const PRIMARYKEY = 'id';
    const IDPOLICE = 'serial';
    
    const CREATEDAT = 'data_criacao';
    
    public function __construct( $id = NULL, $callObjectLoad = TRUE ){
        parent::__construct( $id, $callObjectLoad );
        parent::addAttribute( 'nome' );
        parent::addAttribute( 'data_criacao' );
    }
}
