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
    
    private $funcao;
    
    public function __construct( $id = NULL, $callObjectLoad = TRUE ){
        parent::__construct( $id, $callObjectLoad );
        parent::addAttribute( 'nome' );
        parent::addAttribute( 'data_nascimento' );
        parent::addAttribute( 'funcao_id' );
        parent::addAttribute( 'data_criacao' );
    }
    
    /**
     * Method get_funcao
     * Sample of usage: $pessoa->funcao->attribute;
     * @returns Funcao instance
     */
    public function get_funcao(){
        if( empty($this->funcao) ){
            $this->funcao = Funcao($this->funcao_id);
            
        }
        
        return $this->funcao;
    }
    
    /**
     * Method set_funcao
     * Sample of usage: $pessoa->funcao = $object;
     * @param $object Instance of Funcao
     */
    public function set_fucao( Funcao $object ){
        $this->funcao = $object;
        $this->funcao_id = $object->id;
    }
}
