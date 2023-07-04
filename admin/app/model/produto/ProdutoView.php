<?php
/**
 * Produto Active Record
 * @version    1.0
 * @package    model/produto
 * @author     brunosilva
**/
class ProdutoView extends TRecord{
    const TABLENAME = 'produto_view';
    const PRIMARYKEY = 'id';
    const IDPOLICE = 'serial';
        
    /**
    * Método Construtor 
    **/
    public function __construct($id = NULL, $callObjectLoad = TRUE){
        parent::__construct($id, $callObjectLoad);
        
        parent::addAttribute('nome');
        parent::addAttribute('descricao');
        parent::addAttribute('preco');
        parent::addAttribute('foto');
        parent::addAttribute('link_afiliado');
        parent::addAttribute('id_categoria');
        parent::addAttribute('id_loja');
        parent::addAttribute('data_criado');		
        parent::addAttribute('nome_categoria');
        parent::addAttribute('nome_loja');
        parent::addAttribute('logo_loja');
        parent::addAttribute('link_afiliado_loja');
    }
    
    
}
