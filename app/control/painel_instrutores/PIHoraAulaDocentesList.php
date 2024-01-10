<?php
/**
* Classe que representa a CH de Docentes.
* PIHoraAulaDocentesList Listing
*
* @version    1.0
* @package    control/painel_instrutores
* @author     Michael Bruno
* @since      17/11/2023
**/
class PIHoraAulaDocentesList extends TPage
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
        $this->setActiveRecord('PIAgenda');   // defines the active record
        $this->setDefaultOrder('data_inicio', 'asc');         // defines the default order
        $this->setLimit(100);
        $criteria = new TCriteria();
        if( empty( TSession::getValue('PIAgenda_filter_data_inicio') )){
            $criteria->add(new TFilter('data_inicio', '>=', date('Y-m-01')));
            $criteria->add(new TFilter('data_fim', '<=', date('Y-m-31')));
        }

        $this->setCriteria($criteria); // define a standard filter
        
        $this->addFilterField('(SELECT id from docente WHERE id = agenda.docente_id)', '=', 'docente_id'); // filterField, operator, formField
        $this->addFilterField('unidade_id', '=', 'unidade_id'); // filterField, operator, formField
        $this->addFilterField('tipo_atividade', '=', 'tipo_atividade'); // filterField, operator, formField
        $this->addFilterField('(SELECT tipo_contratacao from docente WHERE id = agenda.docente_id)', '=', 'tipo_contratacao'); // filterField, operator, formField
        $this->addFilterField('data_inicio', '>=', 'data_inicio'); // filterField, operator, formField
        $this->addFilterField('data_fim', '<=', 'data_fim'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_PIHoraAulaDocenteList');
        $this->form->setFormTitle('<i class="fas fa-business-time fa-fw"></i> Ocupação Docentes');
        
        // create the form fields        
		$docente_id 	= new TDBUniqueSearch('docente_id', 'db_painel_instrutores', 'PIDocente', 'id', 'nome', 'nome');
        $unidade_id = new TDBCombo('unidade_id', 'db_painel_instrutores', 'PIUnidade', 'id', 'nome');
        $tipo_contratacao = new TCombo('tipo_contratacao');
        $tipo_contratacao->addItems( PIDocente::TIPOCONTRATACAO );
        $tipo_atividade = new TCombo('tipo_atividade');
        $data_inicio = new TDate('data_inicio');
        $data_fim = new TDate('data_fim');

        $date = new DateTime('now');
        $date->modify('last day of this month');

        $data_inicio->setValue(date('01-m-Y'));
        $data_fim->setValue($date->format('d-m-Y'));

        $items = array();
		$items['AULA'] = 'AULA';
		$items['BLOQUEADO'] = 'BLOQUEADO';
		$items['PROJETO'] = 'PROJETO';
		$items['PLANEJAMENTO'] = 'PLANEJAMENTO';
		$items['HORARIO_DISPONIVEL'] = 'HORARIO DISPONÍVEL';
		$tipo_atividade->addItems($items);

		$data_fim->setMask('dd/mm/yyyy');
        $data_fim->setDatabaseMask('yyyy-mm-dd');
		$data_inicio->setMask('dd/mm/yyyy');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');

        // add the fields
        $this->form->addFields( [ new TLabel('Docente') ], [ $docente_id ], [ new TLabel('Tipo Atividade') ], [ $tipo_atividade] );
        $this->form->addFields( [ new TLabel('Unidade') ], [ $unidade_id ], [ new TLabel('Tipo Contratação') ], [ $tipo_contratacao] );
        $this->form->addFields( [ new TLabel('Início') ], [ $data_inicio ], [ new TLabel('Fim') ], [ $data_fim ] );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('PIHoraAula_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'), new TAction(['PIAgendaForm', 'onEdit']), 'fa:plus green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';        

        // creates the datagrid columns
        $column_data = new TDataGridColumn('data_inicio', 'Data', 'left', '10%');
        $column_nome = new TDataGridColumn('{docente->nome}', 'Docente', 'left', '25%');
        $column_unidade = new TDataGridColumn('{unidade->nome}', 'Unidade', 'left', '10%');
        $column_contratacao = new TDataGridColumn('{docente->tipo_contratacao}', 'Tipo Contratação', 'left', '15%');
        $column_inicio = new TDataGridColumn('data_inicio', 'Dia', 'left', '10%');
        $column_hrinicio = new TDataGridColumn('data_inicio', 'Dia', 'left', '15%');
        $column_horas = new TDataGridColumn('diferenca_horas', 'Horas/Aula', 'left', '15%');
        $column_tipo_atividade = new TDataGridColumn('tipo_atividade', 'Atividade', 'left', '70%');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_data);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_unidade);
        $this->datagrid->addColumn($column_contratacao);
        $this->datagrid->addColumn($column_inicio);
        $this->datagrid->addColumn($column_hrinicio);
        $this->datagrid->addColumn($column_horas);
        $this->datagrid->addColumn($column_tipo_atividade);

        

        // define totals
        $column_horas->setTotalFunction( function($values) {
            return 'TOTAL: '.array_sum((array) $values);
        });

        // define the transformer method over image
        $column_hrinicio->setTransformer( function($value, $object, $row) {
            $inicio = new DateTime($object->data_inicio);
            $fim = new DateTime($object->data_fim);
           
            return $inicio->format('H:i'). ' - '. $fim->format('H:i');
        });
        
        $column_nome->setTransformer( function($value, $object, $row) {
            if($value){
                return strtoupper($value); 
            }        
        });
        
        $column_inicio->setTransformer( function($value, $object, $row) {
            if($value){
                // Array com os dias da semana
                $diasemana = array('Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado');

                // Variável que recebe o dia da semana (0 = domingo, 1 = segunda, 2 = terca ...)
                $diasemana_numero = date('w', strtotime($value));

                // Mostra o dia da semana
                return $diasemana[$diasemana_numero]; 
            }        
        });


        // define the transformer method over image
        $column_data->setTransformer( function($value, $object, $row) {
            if ($value)
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
            return $value;
        });

        // define the transformer method over image
        $column_tipo_atividade->setTransformer( function($value, $object, $row) {

            switch ($value) {
                case 'PROJETO':
                    $cor = 'warning';
                    break;
                case 'AULA':
                    $cor = 'primary';
                    break;
                    case 'PLANEJAMENTO':
                        $cor = 'info';
                        break;
                    case 'HORARIO_DISPONIVEL':
                        $cor = 'success';
                        break;                    
                    case 'BLOQUEADO':
                        $cor = 'danger';
                        break;
                default:
                    $cor = 'default';
            }

            $div = new TElement('span');
            $div->class="label label-{$cor}";
            $div->style="text-shadow:none; font-size:12px";
            $div->add($object->tipo_atividade);
            return $div;
        });

        //$action1 = new TDataGridAction(['PIAgendaForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        //$this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');

        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        //$this->pageNavigation->setProperties( 'style', ['text-align' => 'center'] );

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    
}
