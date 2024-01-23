<?php
/**
* Classe que representa o formulário de cadastro de um bem de patrimônio.
* BemPatrimonioForm Registration
*
* @version    1.0
* @package    control/patrimonio
* @author     Bruno Lopes
* @since      12/01/2024
**/
class BemPatrimonioForm extends TPage
{
    protected $form; // form
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        
        $this->setDatabase('siuel');              // defines the database
        $this->setActiveRecord('BemPatrimonio');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_BemPatrimonio');
        $this->form->setFormTitle('Bens de Patrimonio');
        

        // create the form fields
        $id = new THidden('id');
        $id->setSize('100%');
        
        $nome = new TEntry('nome');
        $nome->setSize('100%');
        
        $descricao = new TText('descricao');
        $descricao->setSize('100%');
        
        $patrimonio = new TEntry('patrimonio');
        $patrimonio->setSize('100%');
        
        $status = new TCombo('status');
        $status->addItems(BemPatrimonio::TIPOSTATUS);
        $status->setValue('BOM ESTADO');
        $status->setSize('100%');
        
        //$data_criado = new TDate('data_criado');
        //$data_criado->setSize('100%');
        
        $responsavel = new TEntry('responsavel');
        $responsavel->setSize('100%');


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ new TLabel('Nome'), $nome ] );
        //$this->form->addFields( [ new TLabel('Status'), $status ], [] );
        $this->form->addFields( [ new TLabel('Descricao'), $descricao ] );
        $this->form->addFields( [ new TLabel('Patrimonio'), $patrimonio ], [ new TLabel('Status'), $status ] );
        $this->form->addFields( [new TLabel('Responsável'), $responsavel] );
        //$this->form->addFields( [ new TLabel('Data Criado') ], [ $data_criado ] );
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addActionLink( _t('Back'), new TAction(['BemPatrimonioList', 'onReload']), 'fa:undo' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
}
