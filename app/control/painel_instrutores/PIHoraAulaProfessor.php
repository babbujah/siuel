<?php
/**
* Classe que define o painel de visualização de hora/aula do professor
* PIHoraAulaProfesso
*
* @version    1.0
* @package    control/painel_instrutores
* @author     Bruno Lopes
* @since      01/12/2023
**/
class PIHoraAulaProfessor extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_PIViewAgenda');
        $this->form->setFormTitle('Hora/Aula Instrutores');

        // create the form fields
        $docente_id = new TDBUniqueSearch( 'docente_id', 'db_painel_instrutores', 'PIDocente', 'id', 'nome' );
        $docente_id->setSize('100%');
        
        $unidade_id = new TDBCombo('unidade_id', 'db_painel_instrutores', 'PIUnidade', 'id', '{nome}', 'nome');
        $unidade_id->setSize('100%');
        
        // definir na busca
        $tipo_contratacao = new TCombo( 'tipo_contratacao' );
        $tipo_contratacao->setSize('100%');
        $tipo_contratacao->addItems( PIDocente::TIPOCONTRATACAO );
        
        $area_id = new TDBCombo('area_id', 'db_painel_instrutores', 'PIArea', 'id', '{nome}', 'nome');
        $area_id->setSize('100%');
        $area_id->enableSearch();
        
        list($a, $m, $d) = explode('-', date('Y-m-d'));
        
        $mes = new TCombo('mes');
        $mes->setSize('100%');
        $meses = [1 => 'Janeiro',
                  2 => 'Fevereiro',
                  3 => 'Março',
                  4 => 'Abril',
                  5 => 'Maio',
                  6 => 'Junho',
                  7 => 'Julho',
                  8 => 'Agosto',
                  9 => 'Setembro',
                  10 => 'Outubro',
                  11 => 'Novembro',
                  12 => 'Dezembro'];
        $mes->addItems($meses);
        $mes->setValue($m);
                
        $ano = new TSpinner('ano');
        $ano->setSize('100%');
        $ano->setValue($a);
        $ano->setRange(2023, 2500, 1);

        // add the fields
        $this->form->addFields( [ new TLabel('Docente')], [$docente_id ], [ new TLabel('Unidade')], [$unidade_id ] );
        $this->form->addFields( [ new TLabel('Contratação')], [$tipo_contratacao ], [ new TLabel('Area')], [$area_id ] );
        $this->form->addFields( [ new TLabel('Mês')], [$mes], [new TLabel('Ano')], [$ano] );
        //$this->form->addFields( [$data_inicio], [$data_fim] );

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'), new TAction(['', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');

        // creates the datagrid columns
        //$column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_docente_id = new TDataGridColumn('docente->nome', 'Docente', 'left');
        $column_unidade_id = new TDataGridColumn('unidade->nome', 'Unidade', 'left');
        $column_area_id = new TDataGridColumn('area->nome', 'Area', 'left');
        $column_tipo_contratacao = new TDataGridColumn( 'docente->tipo_contratacao', 'Contratação', 'left' );
        $column_hora_aula = new TDataGridColumn( 'cargaHoraria', 'Hora/Aula', 'center' );
        $column_hora_aula->setTransformer([$this, 'formatHoraAula']);
        //$column_tipo_atividade = new TDataGridColumn('tipo_atividade', 'Tipo Atividade', 'left');
        $column_mes = new TDataGridColumn('mes', 'Mês', 'left');
        $column_ano = new TDataGridColumn('ano', 'Ano', 'left');
        
        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_docente_id);
        $this->datagrid->addColumn($column_unidade_id);
        $this->datagrid->addColumn($column_area_id);
        $this->datagrid->addColumn( $column_tipo_contratacao );
        $this->datagrid->addColumn( $column_hora_aula );
        //$this->datagrid->addColumn($column_tipo_atividade);
        $this->datagrid->addColumn($column_mes);
        $this->datagrid->addColumn($column_ano);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    public static function formatHoraAula( $dataHora ){
        list($h, $m, $s) = explode(':', $dataHora);
        
        return $h.'h';
    }
    
    private function tratarFiltroData( $filtro ){
        list($n, $v) = explode('=', $filtro->dump());
        $valor = trim($v);
        $valor = trim($valor, "'");
        $valor = intval($valor);
    
        return $valor;
    }
    
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    /*public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('db_painel_instrutores'); // open a transaction with database
            $object = new PIViewAgenda($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }*/
    
    public function onSearch(){
        // get the search form data
        $data = $this->form->getData();
                
        // clear session filters
        TSession::setValue(__CLASS__.'_filter_docente_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_unidade_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_tipo_contratacao', NULL);
        TSession::setValue(__CLASS__.'_filter_area_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_mes',   NULL);
        TSession::setValue(__CLASS__.'_filter_ano',   NULL);
        
        if (isset($data->docente_id) AND ($data->docente_id)) {
            $filter = new TFilter('docente_id', '=', "{$data->docente_id}"); // create the filter
            TSession::setValue(__CLASS__.'_filter_docente_id',   $filter); // stores the filter in the session
        }
        
        if (isset($data->unidade_id) AND ($data->unidade_id)) {
            $filter = new TFilter('unidade_id', '=', "{$data->unidade_id}"); // create the filter
            TSession::setValue(__CLASS__.'_filter_unidade_id',   $filter); // stores the filter in the session
        }
        
        if (isset($data->tipo_contratacao) AND ($data->tipo_contratacao)) {
            $filter = new TFilter('tipo_contratacao', 'like', "{$data->tipo_contratacao}"); // create the filter
            TSession::setValue(__CLASS__.'_filter_tipo_contratacao',   $filter); // stores the filter in the session
        }
        
        if (isset($data->area_id) AND ($data->area_id)) {
            $filter = new TFilter('area_id', '=', "{$data->area_id}"); // create the filter
            TSession::setValue(__CLASS__.'_filter_area_id',   $filter); // stores the filter in the session
        }
        
        if (isset($data->mes) AND ($data->mes)) {
            $filter = new TFilter('mes', '=', "{$data->mes}"); // create the filter
            TSession::setValue(__CLASS__.'_filter_mes',   $filter); // stores the filter in the session
        }
        
        if (isset($data->ano) AND ($data->ano)) {
            $filter = new TFilter('ano', '=', "{$data->ano}"); // create the filter
            TSession::setValue(__CLASS__.'_filter_ano',   $filter); // stores the filter in the session
        }
                
        //fill the form with data again
        $this->form->setData( $data );
        
        //keep the search data in tehe session
        TSession::setValue( __CLASS__.'_filter_data', $data );
                
        $param = array();
        $param['offset'] = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
        
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'db_painel_instrutores'
            TTransaction::open('db_painel_instrutores');
            
            // creates a repository for PIAgenda
            //$repository = new TRepository('PIAgenda');
            $repository = new TRepository('PIViewAgenda');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
                        
            // default order
            if (empty($param['order']))
            {
                //$param['order'] = 'id';
                $param['order'] = 'docente_id';
                $param['direction'] = 'asc';
                $param['group'] = 'docente_id';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
                        
            if (TSession::getValue(__CLASS__.'_filter_docente_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_docente_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_unidade_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_unidade_id')); // add the session filter
            }
            
            if (TSession::getValue(__CLASS__.'_filter_tipo_contratacao')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_tipo_contratacao')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_area_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_area_id')); // add the session filter
            }
            
            if (TSession::getValue(__CLASS__.'_filter_mes') AND TSession::getValue(__CLASS__.'_filter_mes') instanceof TExpression ) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_mes')); // add the session filter
                
            }else{
                list($a, $m, $d) = explode('-', date('Y-m-d'));
                
                $filter = new TFilter('mes', '=', $m); // create the filter
                TSession::setValue(__CLASS__.'_filter_mes',   $filter); // stores the filter in the session
                $criteria->add( TSession::getValue(__CLASS__.'_filter_mes') );
                //TSession::setValue(__CLASS__.'_filter_mes', $m);
                //$criteria->add( new TFilter( 'mes', '=', $m ) ); // add the session filter
                
            }


            if (TSession::getValue(__CLASS__.'_filter_ano') AND TSession::getValue(__CLASS__.'_filter_ano') instanceof TExpression ) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_ano')); // add the session filter
                
            }else{
                list($a, $m, $d) = explode('-', date('Y-m-d'));
                $filter = new TFilter('ano', '=', $a); // create the filter
                TSession::setValue(__CLASS__.'_filter_ano', $filter);
                $criteria->add( TSession::getValue(__CLASS__.'_filter_ano')); // add the session filter
                
            }
            
            // load the objects according to criteria
            $data = $this->form->getData();
            $objects = $repository
                       ->groupBy('docente_id')
                       ->groupBy('area_id')
                       ->load($criteria, FALSE);
            $filtroMes = TSession::getValue(__CLASS__.'_filter_mes');
            $filtroAno = TSession::getValue(__CLASS__.'_filter_ano');
            if( !empty($filtroMes) && !empty($filtroAno) ){
                $mes = $this->tratarFiltroData( $filtroMes );
                $ano = $this->tratarFiltroData( $filtroAno );
                
            }else{
                $mes = $m;
                $ano = $a;    
            }
                        
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
                    $area = empty(TSession::getValue( __CLASS__.'filter_data' )->area_id) ? $object->area_id : TSession::getValue( __CLASS__.'filter_data' )->area_id;
                    $object->contabilizarCargaHoraria($ano, $mes, $area);
                    
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
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
            TTransaction::open('db_painel_instrutores'); // open a transaction with database
            $object = new PIViewAgenda($key, FALSE); // instantiates the Active Record
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
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
