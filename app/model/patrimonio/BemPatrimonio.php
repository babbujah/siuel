<?php
/**
* Classe que representa Active Record de BemPatrimonio 
*
* @version    1.0
* @package    model/patrimonio
* @author     Bruno Lopes
* @since      12/01/2024
**/
class BemPatrimonio extends TRecord
{
    const TABLENAME = 'si_bem_patrimonio';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    const CREATEDAT = 'data_criado';
    
    const TIPOSTATUS = ['BOM ESTADO' => 'BOM ESTADO', 'EMPRESTADO' => 'EMPRESTADO',  'BAIXA' => 'BAIXA', 'DANIFICADO' => 'DANIFICADO'];
    
    private $responsavel;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('descricao');
        parent::addAttribute('patrimonio');
        parent::addAttribute('status');
        parent::addAttribute('data_criado');
        parent::addAttribute('responsavel_id');
    }
    
    public function get_responsavel(){
        if( empty($this->responsavel) ){
            $this->responsavel = new Responsavel($this->responsavel_id);
            
        }
        
        return $this->responsavel;
        
    }
    
    public function set_responsavel( Responsavel $object ){
        $this->responsavel = $object;
        $this->responsavel_id = object->id;
    }


}
