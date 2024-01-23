<?php
/**
* Classe que representa Active Record de Responsável
*
* @version    1.0
* @package    model/entidade
* @author     Bruno Lopes
* @since      23/01/2024
**/
class Responsavel TRecord{
    
    const TABLENAME = 'responsavel';
    const PRIMARYKEY = 'id';
    const IDPOLICE = 'serial';
    
    const CREATEDAT = 'data_criacao';
    
    const TIPOENTIDADE = ['PESSOA' => 'PESSOA', 'EQUIPE' => 'EQUIPE', 'SEÇÃO' => 'SEÇÃO', 'GRUPO' => 'GRUPO'];
    
    private entidade;
    
    public function __construct($id = NULL, $callObjectLoad = TRUE){
        
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('tipo');
        parent::addAttribute('entidade_id');            
        
    }
    
    public function get_entidade(){
        if( empty($this->entidade) ){
            switch ($this->tipo){
                case 'PESSOA':
                    $this->entidade = new Pessoa($this->entidade_id);
                    break;
                    
                case 'EQUIPE':
                    $this->entidade = new Equipe($this->entidade_id);
                    break;
                    
                case 'SEÇÃO':
                    $this->entidade = new Secao($this->entidade_id);
                    break;
                    
                default:
                    $this->entidade = new Grupo($this->entidade_id);
            }
            
        }
        
        return $this->entidade;
    }
    
    /*public function set_entidade( $object ){
        if( $object instanceof Pessoa ){
            $this->
        }
    }*/
}
