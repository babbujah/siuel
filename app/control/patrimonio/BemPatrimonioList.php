<?php
/**
* Classe que representa a listagem de Bens de patrimônio.
* BemPatrimonioList Listing
*
* @version    1.0
* @package    control/patrimonio
* @author     Bruno Lopes
* @since      11/01/2024
**/
class BemPatrimonioList extends TPage
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
        
        $this->setDatabase('siuel');            // defines the database
        $this->setActiveRecord('BemPatrimonio');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('nome', 'like', 'nome'); // filterField, operator, formField
        $this->addFilterField('descricao', 'like', 'descricao'); // filterField, operator, formField
        $this->addFilterField('patrimonio', 'like', 'patrimonio'); // filterField, operator, formField
        $this->addFilterField('status', 'like', 'status'); // filterField, operator, formField
        $this->addFilterField('data_criado', 'like', 'data_criado'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_BemPatrimonio');
        $this->form->setFormTitle('BemPatrimonio');
        

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $descricao = new TEntry('descricao');
        $patrimonio = new TEntry('patrimonio');
        $status = new TEntry('status');
        $data_criado = new TEntry('data_criado');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome ] );
        $this->form->addFields( [ new TLabel('Descricao') ], [ $descricao ] );
        $this->form->addFields( [ new TLabel('Patrimonio') ], [ $patrimonio ] );
        $this->form->addFields( [ new TLabel('Status') ], [ $status ] );
        $this->form->addFields( [ new TLabel('Data Criado') ], [ $data_criado ] );


        // set sizes
        $id->setSize('100%');
        $nome->setSize('100%');
        $descricao->setSize('100%');
        $patrimonio->setSize('100%');
        $status->setSize('100%');
        $data_criado->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['BemPatrimonioForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_patrimonio = new TDataGridColumn('patrimonio', 'Patrimonio', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'right');
        $column_data_criado = new TDataGridColumn('data_criado', 'Data Criado', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_patrimonio);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_data_criado);

        
        $action1 = new TDataGridAction(['BemPatrimonioForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
}
