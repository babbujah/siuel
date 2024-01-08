<?php
class ItemAlmoxarifado extends TRecord{
    const TABLENAME = 'si_item_almoxarifado';
    const PRIMARYKEY = 'id'
    const IDPOLICY = 'serial';
    
    //const CREATEEDAT = 'data_criado';
    
    private $attribute;
    
    public function __construct($id = null, $callObjectLoad = true){
        parent::__contruct($id, $callObjectLoad);
        
        parent::addAttribute('nome');
        parent::addAttribute('descricao');
        parent::addAttribute('patrimonio');
        parent::addAttribute('status');
        //parent::addAttribute('quantidade');
        //parent::addAttribute('attribute_id');
        
    }
    
    public function get_attribute(){
        if( empty( $this->attribute ) ){
            $this->attribute = new Attribute( $this->attribute_id );
            
        }
        
        return $this->attribute;
    }
    
    public function set_attribute( $attribute ){
        $this->attribute = $attribute;
        
    }
}
