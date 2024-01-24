<?php
/**
* Classe que representa uma equipe dentro da estrutura do sistema.
* As equipe podem ser matilhas, patrulhas, equipes ou alguma da administração
* @version    1.0
* @package    model/entidade
* @author     Bruno Lopes
* @since      24/01/2024
**/
class Equipe extends TRecord{
    const TABLENAME = 'si_equipe';
    const PRIMARYKEY = 'id';
    const IDPOLICE = 'serial';
    
    const CREATEDAT = 'data_criacao';
    
    const TIPOEQUIPE = ['MATILHA' => 'MATILHA', 'PATRULHA' => 'PATRULHA', 'EQUIPE' => 'EQUIPE', 'ADMINISTRATIVA' => 'ADMINISTRATIVA'];
    
    public function __construct( $id = NULL, $callObjectLoad = TRUE ){
        parent::__construct( $id, $callObjectLoad );
        parent::addAttribute( 'nome' );
        parent::addAttribute( 'tipo' );
        parent::addAttribute( 'data_criacao' );
    }
}
