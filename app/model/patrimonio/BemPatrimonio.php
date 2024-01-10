<?php
/**
 * BemPatrimonio Active Record
 * @author  <your-name-here>
 */
class BemPatrimonio extends TRecord
{
    const TABLENAME = 'si_bem_patrimonio';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    const CREATEDAT = 'data_criado';
    
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
    }


}
