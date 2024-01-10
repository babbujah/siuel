<?php
/**
* Classe que representa a CH de Docentes.
* PIHoraAulaList Listing
*
* @version    1.0
* @package    control/painel_instrutores
* @author     Michael Bruno
* @since      17/11/2023
**/
class PIHoraAulaList extends TPage
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
        $criteria->add(new TFilter('tipo_atividade', 'NOT IN', array('HORARIO_DISPONIVEL', 'BLOQUEADO')));
        if( empty( TSession::getValue('PIAgenda_filter_data_inicio') )){
            $criteria->add(new TFilter('data_inicio', '>=', date('Y-m-01')));
            $criteria->add(new TFilter('data_fim', '<=', date('Y-m-31')));
        }
        TTransaction::open('db_painel_instrutores');
        $criteria->add(new TFilter('docente_id', '=', PIDocente::findbyEmail(TSession::getValue('login'))->id));
        $this->setCriteria($criteria); // define a standard filter

        $this->addFilterField('data_inicio', '>=', 'data_inicio'); // filterField, operator, formField
        $this->addFilterField('data_fim', '<=', 'data_fim'); // filterField, operator, formField

        $repository = new TRepository('PIAgenda');
        //$criteria->add(new TFilter('data_inicio', '>=', TSession::getValue('PIAgenda_filter_data_inicio')->value));
        //$criteria->add(new TFilter('data_inicio', '<=', TSession::getValue('PIAgenda_filter_data_fim')->value));
        $rows = $repository->load($criteria);
        $soma = 0;
        if($rows){
            foreach($rows as $row){
                $soma += $row->diferenca_horas;
            }
        }

        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_PIHoraAulaList');
        $this->form->setFormTitle('Informe o período:');
        
        // create the form fields       
        $data_inicio = new TDate('data_inicio');
        $data_fim = new TDate('data_fim');

        $date = new DateTime('now');
        $date->modify('last day of this month');

        $data_inicio->setValue(date('01-m-Y'));
        $data_fim->setValue($date->format('d-m-Y'));

		$data_fim->setMask('dd/mm/yyyy');
        $data_fim->setDatabaseMask('yyyy-mm-dd');
		$data_inicio->setMask('dd/mm/yyyy');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');

        // add the fields
        $this->form->addFields( [ new TLabel('Início') ], [ $data_inicio ], [ new TLabel('Fim') ], [ $data_fim ] );
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('PIHoraAula_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction('Filtrar', new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('{docente->nome}', 'Nome', 'left', '25%');
        $column_data = new TDataGridColumn('data_inicio', 'Data', 'left', '10%');
        $column_inicio = new TDataGridColumn('data_inicio', '', 'left', '5%');
        $column_hrinicio = new TDataGridColumn('data_inicio', '', 'left', '10%');
        $column_horas = new TDataGridColumn('diferenca_horas', 'Horas/Aula', 'left', '10%');
        $column_tipo_atividade = new TDataGridColumn('tipo_atividade', 'Atividade', 'left', '10%');
        $column_observacao = new TDataGridColumn('observacao', 'Observações', 'left', '30%');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data);
        $this->datagrid->addColumn($column_inicio);
        $this->datagrid->addColumn($column_hrinicio);
        $this->datagrid->addColumn($column_horas);
        $this->datagrid->addColumn($column_tipo_atividade);
        $this->datagrid->addColumn($column_observacao);        

        // define the transformer method over image
        $column_hrinicio->setTransformer( function($value, $object, $row) {
            $inicio = new DateTime($object->data_inicio);
            $fim = new DateTime($object->data_fim);
           
            return $inicio->format('H:i'). ' - '. $fim->format('H:i');
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

        // define totals
        $column_horas->setTotalFunction( function($values) {
            return 'TOTAL: '.array_sum((array) $values);
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
                default:
                    $cor = 'default';
            }

            $div = new TElement('span');
            $div->class="label label-{$cor}";
            $div->style="text-shadow:none; font-size:12px";
            $div->add($object->tipo_atividade);
            return $div;
        });

        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        //$this->pageNavigation->setProperties( 'style', ['text-align' => 'center'] );

        $div = new TElement('div');
        $div->class = "row";

        $indicator1 = new THtmlRenderer('app/resources/info-box.html');
        $indicator2 = new THtmlRenderer('app/resources/info-box.html');
        
        $indicator1->enableSection('main', ['title'     => 'Soma Hora/Aula',
                                           'icon'       => 'business-time',
                                           'background' => 'blue',
                                           'value'      => $soma ] );
        
        $indicator2->enableSection('main', ['title'      => 'Users',
                                            'icon'       => 'user',
                                            'background' => 'orange',
                                            'value'      => 200 ] );
        $div->add( $i1 = TElement::tag('div', $indicator1) );
        $div->add( $i2 = TElement::tag('div', $indicator2) );

        $i1->class = 'col-sm-6';
        $i2->class = 'col-sm-6';

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        //$container->add($div);
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    
}
