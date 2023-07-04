<?php
/**
 * ShareTemplateForm Form
 * @author  <your name here>
 */
class ShareTemplateForm extends TPage
{
    protected $datagrid;
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        $col_tag = new TDataGridColumn( 'tag', 'TAG', 'left', '10%' );
        $col_descricao = new TDataGridColumn( 'descricao', 'Descrição', 'left' );
        
        $datagrid_instrucao = new BootstrapDatagridWrapper( new TDataGrid() );
        $datagrid_instrucao->width = '100%';
        
        $datagrid_instrucao->addColumn($col_tag);
        $datagrid_instrucao->addColumn($col_descricao);
        
        $datagrid_instrucao->createModel();
        
        $datagrid_instrucao->clear();
        
        $item = new stdClass;
        $item->tag = '{NOME}';
        $item->descricao = 'Nome do produto';
        $datagrid_instrucao->addItem($item);
                
        $item = new stdClass;
        $item->tag = '{PRECO}';
        $item->descricao = 'Valor do produto';
        $datagrid_instrucao->addItem($item);
        
        $item = new stdClass;
        $item->tag = '{DESCRICAO}';
        $item->descricao = 'Descrição do produto';
        $datagrid_instrucao->addItem($item);
        
        $item = new stdClass;
        $item->tag = '{URL}';
        $item->descricao = 'Endereço de acesso ao produto';
        $datagrid_instrucao->addItem($item);       
        
        $item = new stdClass;
        $item->tag = 'EXEMPLO';
        $item->descricao = 'O {NOME} está com o preço {PRECO}. Ele é {DESCRICAO} e pode ser acessado através do link {URL}.
                            As tags devem ser escritas dessa mesma forma escrita para que os dados possam sem devidamente substituídos.';
        $datagrid_instrucao->addItem($item);
                
                        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ShareTemplate');
        $this->form->setFormTitle('Novo Template de Compartilhamento');        

        // create the form fields
        $id = new THidden('id');
        $content = new THtmlEditor('content');
        $content->setSize('100%', 200);
        $content->setOption('placeholder', 'Digite aqui ...');

        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ new TLabel('Content') ], [ $content ] );

        // set sizes
        $id->setSize('100%');
        $content->setSize('100%');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_content = new TDataGridColumn('content', 'Content', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_content);


        $action1 = new TDataGridAction(['ShareTemplateForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($datagrid_instrucao);
        $container->add($this->form);
        $container->add(TPanelGroup::pack('Templates criados', $this->datagrid));
        
        parent::add($container);
        
        $this->onReload($param);
        
    }
    
    public function onReload($param)
    {
        try
        {
            TTransaction::open('acheipegai'); // open a transaction
                                   
            $repository = new TRepository('ShareTemplate');
            $limit = 25;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            TTransaction::close(); // close the transaction
            
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    } 
    
    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('acheipegai'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new ShareTemplate;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            $this->onReload($param);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {   
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('acheipegai'); // open a transaction
                $object = new ShareTemplate($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
                
            }
            else
            {
                $this->form->clear(TRUE);
            }
            
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('acheipegai'); // open a transaction with database
            $object = new ShareTemplate($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
