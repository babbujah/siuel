<?php
/**
* Classe que representa Active Record de Docente 
*
* @version    1.0
* @package    model/painel_instrutores
* @author     Bruno Lopes
* @since      16/11/2023
**/
class PIDocente extends TRecord
{
    const TABLENAME = 'docente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    const CREATEDAT = 'data_cadastro';
    
    const TIPOCONTRATACAO = ['MENSALISTA' => 'MENSALISTA', 'CRED PF' => 'CRED PF', 'HORISTA' => 'HORISTA'];
    
    private $unidade;
    private $area;
    private $tipoContratacao;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);        
        parent::addAttribute('nome');
        parent::addAttribute('cpf');
        parent::addAttribute('email');
        parent::addAttribute('tipo_contratacao');
        parent::addAttribute('data_cadastro');
        parent::addAttribute('usuario_cadastro');
        parent::addAttribute('unidade_id');
        parent::addAttribute('area_id');
        
    }

    public static function findByID($id) {
        $objects = PIDocente::where('id', '=', $id)->load();
        if (count($objects) == 1) {
            return $objects[0];
        }
    }
    
    public static function findByEmail( $email ){
        $objects = PIDocente::where( 'email', '=', $email )->load();
        if( count($objects) == 1 ){
            return $objects[0];
        }
    }
    
    public function carregarDocente( PIDocente $docente ){
        $this->id = $docente->id;
        $this->nome = $docente->nome;
        $this->cpf = $docente->cpf;
        $this->email = $docente->email;
        $this->tipo_contratacao = $docente->tipo_contratacao;
        $this->data_cadastro = $docente->data_cadastro;
        $this->usuario_cadastro = $docente->usuario_cadastro;
        $this->unidade_id = $docente->unidade_id;
        $this->area_id = $docente->area_id;
    }
    
    /**
     * Method set_unidade
     * Sample of usage: $pidocente->piunidade = $object;
     * @param $object Instance of PIUnidade
     */
    public function set_unidade(PIUnidade $object)
    {
        $this->unidade = $object;
        $this->unidade_id = $object->id;
    }
    
    /**
     * Method get_unidade
     * Sample of usage: $pidocente->piunidade->attribute;
     * @returns PIUnidade instance
     */
    public function get_unidade()
    {
        // loads the associated object
        if (empty($this->unidade))
            $this->unidade = new PIUnidade($this->unidade_id);
    
        // returns the associated object
        return $this->unidade;
    }
    
    
    /**
     * Method set_area
     * Sample of usage: $pidocente->piarea = $object;
     * @param $object Instance of PIArea
     */
    public function set_area(PIArea $object)
    {
        $this->area = $object;
        $this->area_id = $object->id;
    }
    
    /**
     * Method get_area
     * Sample of usage: $pidocente->piarea->attribute;
     * @returns PIArea instance
     */
    public function get_area()
    {
        // loads the associated object
        if (empty($this->area))
            $this->area = new PIArea($this->area_id);
    
        // returns the associated object
        return $this->area;
    }
}
