<?php
/**
* Classe que representa a listagem de Docentes.
* PIDocenteList Listing
*
* @version    1.0
* @package    control/painel_instrutores
* @author     Bruno Lopes
* @since      17/11/2023
**/
class PIDocenteList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('db_painel_instrutores');            // defines the database
        $this->setActiveRecord('PIDocente');   // defines the active record
        $this->setDefaultOrder('nome', 'asc');         // defines the default order
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField        
        $this->addFilterField('nome', 'like', 'nome'); // filterField, operator, formField
        $this->addFilterField('cpf', 'like', 'cpf'); // filterField, operator, formField
        $this->addFilterField( 'email', 'like', 'email' ); // filterField, operator, formField
        $this->addFilterField('tipo_contratacao', 'like', 'tipo_contratacao'); // filterField, operator, formField
        $this->addFilterField('data_cadastro', 'like', 'data_cadastro'); // filterField, operator, formField
        $this->addFilterField('usuario_cadastro', 'like', 'usuario_cadastro'); // filterField, operator, formField
        $this->addFilterField('unidade_id', 'like', 'unidade_id'); // filterField, operator, formField
        $this->addFilterField('area_id', 'like', 'area_id'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_PIDocenteList');
        $this->form->setFormTitle('<i class="fas fa-users fa-fw"></i> Docente');
        
        // create the form fields
        $id = new TEntry('id');
        $id->setSize('100%');
           
        $nome = new TEntry('nome');
        $nome->setSize('100%');
        
        $cpf = new TEntry('cpf');
        $cpf->setMask( '999.999.999-99' );
        $cpf->setSize('100%');
        
        $email = new TEntry('email');
        $email->setSize('100%');
        
        $tipo_contratacao = new TCombo('tipo_contratacao');
        $tipo_contratacao->addItems(PIDocente::TIPOCONTRATACAO);
        $tipo_contratacao->setSize('100%');
        
        //$data_cadastro = new TEntry('data_cadastro');
        //$data_cadastro->setSize('100%');
        
        //$usuario_cadastro = new TEntry('usuario_cadastro');
        //$usuario_cadastro->setSize('100%');
        
        $unidade_id = new TDBCombo('unidade_id', 'db_painel_instrutores', 'PIUnidade', 'id', 'nome');
        $unidade_id->setSize('100%');
        
        $area_id = new TDBUniqueSearch( 'area_id', 'db_painel_instrutores', 'PIArea', 'id', 'nome' );
        $area_id->setMinLength(3);
        $area_id->setSize('100%');

        // add the fields
        //$this->form->addFields( [ new TLabel('ID') ], [ $id ] );        
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome ], [ new TLabel('CPF') ], [ $cpf ] );
        $this->form->addFields( [ new TLabel('E-mail') ], [$email], [ new TLabel('Tipo Contratacão') ], [ $tipo_contratacao ] );
        $this->form->addFields( [ new TLabel('Unidade') ], [ $unidade_id ], [ new TLabel('Área') ], [ $area_id ] );
        
        /*$id->setExitAction( new TAction([$this, 'onSearch'], ['static' => '1']) );
        $nome->setExitAction( new TAction([$this, 'onSearch'], ['static' => '1']) );
        $cpf->setExitAction( new TAction([$this, 'onSearch'], ['static' => '1']) );
        $tipo_contratacao->setExitAction( new TAction([$this, 'onSearch'], ['static' => '1']) );
        $unidade_id->setChangeAction( new TAction( [$this, 'onSearch'], ['static' => '1'] ) );
        $area_id->setChangeAction( new TAction( [$this, 'onSearch'], ['static' => '1'] ) );*/
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('PIDocente_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'), new TAction(['PIDocenteForm', 'onEdit']), 'fa:plus green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        //$column_id = new TDataGridColumn('id', 'Id', 'left', '5%');        
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        
        $column_cpf = new TDataGridColumn('cpf', 'CPF', 'left', '10%');
        $column_cpf->setTransformer([$this, 'formatarCpf']);
        
        $column_email = new TDataGridColumn('email', 'E-mail', 'left');
        
        $column_tipo_contratacao = new TDataGridColumn('tipo_contratacao', 'Tipo de Contratacão', 'left', '10%');
        //$column_data_cadastro = new TDataGridColumn('data_cadastro', 'Data Cadastro', 'left');
        //$column_usuario_cadastro = new TDataGridColumn('usuario_cadastro', 'Usuario Cadastro', 'left');
        $column_unidade_id = new TDataGridColumn('unidade->nome', 'Unidade', 'left');
        $column_area_id = new TDataGridColumn('area->nome', 'Área', 'left');

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cpf);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_tipo_contratacao);
        //$this->datagrid->addColumn($column_data_cadastro);
        //$this->datagrid->addColumn($column_usuario_cadastro);
        $this->datagrid->addColumn($column_unidade_id);
        $this->datagrid->addColumn($column_area_id);
                
        // create EDIT action
        $action_edit = new TDataGridAction(['PIDocenteForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:edit blue');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash red');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        //$this->pageNavigation->setProperties( 'style', ['text-align' => 'center'] );

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    public function formatarCpf($cpf, $object, $row) {
                
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $qtd = strlen($cpf);
     
        if($qtd >= 11) {
 
            if($qtd === 11 ) {
 
                $docFormatado = substr($cpf, 0, 3) . '.' .
                                substr($cpf, 3, 3) . '.' .
                                substr($cpf, 6, 3) . '-' .
                                substr($cpf, 9, 2);
            } else {
                $docFormatado = substr($cpf, 0, 2) . '.' .
                                substr($cpf, 2, 3) . '.' .
                                substr($cpf, 5, 3) . '/' .
                                substr($cpf, 8, 4) . '-' .
                                substr($cpf, -2);
            }
 
            return $docFormatado;
 
        } else {
            return 'Documento invalido';
            
        }
    }
}
