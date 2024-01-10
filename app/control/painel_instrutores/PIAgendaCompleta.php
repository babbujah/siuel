<?php
/**
 * FullCalendarDatabaseView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PIAgendaCompleta extends TPage
{
    private $fc;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->fc = new TFullCalendar(date('Y-m-d'), 'month');
        $this->fc->setReloadAction(new TAction(array($this, 'getEvents')));
        $this->fc->setDayClickAction(new TAction(array('PIAgendaForm', 'onStartEdit')));
        $this->fc->setEventClickAction(new TAction(array('PIAgendaForm', 'onEdit')));
        $this->fc->setEventUpdateAction(new TAction(array('PIAgendaForm', 'onUpdateEvent')));
        
        $this->fc->setOption('businessHours', [ [ 'dow' => [ 1, 2, 3, 4, 5 ], 'start' => '08:00', 'end' => '18:00' ]]);
        //$this->fc->setTimeRange('10:00', '18:00');
        //$this->fc->disableDragging();
        //$this->fc->disableResizing();
        parent::add( $this->fc );
    }
    
    /**
     * Output events as an json
     */
    public static function getEvents($param=NULL)
    {
        $return = array();
        try
        {
            TTransaction::open('db_painel_instrutores');
            
            $events = PIAgenda::where('data_inicio', '<=', $param['end'])
                                   ->where('data_fim',   '>=', $param['start'])->load();
            
            if ($events)
            {
                foreach ($events as $event)
                {
                    $event_array = $event->toArray();
                    $event_array['start'] = str_replace( ' ', 'T', $event_array['data_inicio']);
                    $event_array['end']   = str_replace( ' ', 'T', $event_array['data_fim']);
                    $event_array['color'] = $event_array['cor'];
                    //$event_array['title'] = PIDocente::findByID($event_array->docente_id)->nome;
                    $event_array['title'] = '['.substr($event_array['data_inicio'], -8, 5) .' / '. substr($event_array['data_fim'], -8, 5) .'] '.PIDocente::findByID($event_array['docente_id'])->nome;
                    
                    $popover_content = $event->render("<b>Title</b>: {observacao} <br> <b>Description</b>: {observacao}");
                    $event_array['observacao'] = TFullCalendar::renderPopover($event_array['observacao'], 'Popover title', $popover_content);
                    
                    $return[] = $event_array;
                }
            }
            TTransaction::close();
            echo json_encode($return);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Reconfigure the callendar
     */
    public function onReload($param = null)
    {
        if (isset($param['view']))
        {
            $this->fc->setCurrentView($param['view']);
        }
        
        if (isset($param['date']))
        {
            $this->fc->setCurrentDate($param['date']);
        }
    }
}
