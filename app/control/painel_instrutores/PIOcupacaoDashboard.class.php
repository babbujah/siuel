<?php
/**
 * PIOcupacaoDashboard
 *
 * @version    1.0
 * @package    control
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PIOcupacaoDashboard extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        try
        {
            $html = new THtmlRenderer('app/resources/pi_ocupacao_dashboard.html');
            
            TTransaction::open('db_painel_instrutores');
            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            
            $indicator1->enableSection('main', ['title' => 'Mensalista', 'icon' => 'user', 'background' => 'orange', 'value' => PIDocente::where('tipo_contratacao', '=', 'MENSALISTA')->count()]);
            $indicator2->enableSection('main', ['title' => 'Horista', 'icon' => 'users', 'background' => 'blue',   'value' => PIDocente::where('tipo_contratacao', '=', 'HORISTA')->count()]);
            $chart1 = new THtmlRenderer('app/resources/google_bar_chart.html');
            $data1 = [];
            $data1[] = [ 'Total', 'Instrutores' ];
            
            $stats1 = PIDocente::groupBy('area_id')->countBy('id', 'count');

            if ($stats1)
            {
                foreach ($stats1 as $row)
                {
                    $data1[] = [ PIArea::find($row->area_id)->nome, (int) $row->count];
                }
            }
            
            // replace the main section variables
            $chart1->enableSection('main', ['data'   => json_encode($data1),
                                            'width'  => '100%',
                                            'height'  => '500px',
                                            'title'  => 'Instrutores por Área',
                                            'ytitle' => 'Áreas', 
                                            'xtitle' => _t('Count'),
                                            'uniqid' => uniqid()]);
            
            $chart2 = new THtmlRenderer('app/resources/google_bar_chart.html');
            $data2 = [];
            $data2[] = [ 'Instrutor', 'CH Aulas', 'CH Outras Atividades', 'CH Disponível' ];

            $repository = new TRepository('PIViewAgendaAtividade');
            $criteria = new TCriteria();
            $criteria->add(new TFilter('mes', '=', date('m')));
            $criteria->add(new TFilter('ano', '=', date('Y')));
            $rows = $repository->load($criteria);
            if($rows){
                foreach($rows as $row){
                    $data2[] = [$row->docente->nome, 
                                $row->getTotalAgenda($row->unidade_id, $row->docente_id, 'AULA', date('m'), date('Y')),
                                $row->getTotalAgenda($row->unidade_id, $row->docente_id, 'PROJETO', date('m'), date('Y')) + 
                                $row->getTotalAgenda($row->unidade_id, $row->docente_id, 'PLANEJAMENTO', date('m'), date('Y')), 
                                $row->getTotalAgenda($row->unidade_id, $row->docente_id, 'HORARIO_DISPONIVEL', date('m'), date('Y'))
                                ];
                }
            }

            // replace the main section variables
            $chart2->enableSection('main', ['data'   => json_encode($data2),
            'width'  => '100%',
            'height'  => '500px',
            'title'  => 'Ocupação por Instrutor '.date('m').'/'.date('Y'),
            'ytitle' => 'Instrutores', 
            'xtitle' => _t('Count'),
            'uniqid' => uniqid()]);
            
            $html->enableSection('main', ['indicator1' => $indicator1,
                                          'indicator2' => $indicator2,
                                          'chart1'     => $chart1,
                                          'chart2'     => $chart2] );
            
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($html);
            
            parent::add($container);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            parent::add($e->getMessage());
        }
    }
}
