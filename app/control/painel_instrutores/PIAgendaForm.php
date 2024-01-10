<?php
ini_set('display_errors', 1);
/**
 * CalendarEventForm
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PIAgendaForm extends TWindow
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    public function __construct()
    {
        parent::__construct();
        parent::setSize(700, null);
        parent::setTitle('Calendário');
        //parent::removePadding();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_event');
        $this->form->setProperty('style', 'margin-bottom:0');
        
		$hours = array();
        $minutes = array();
        for ($n=7; $n<24; $n++)
        {
            $hours[$n] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }
        
        for ($n=0; $n<=55; $n+=5)
        {
            $minutes[$n] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }
		
        // create the form fields
        $view           = new THidden('view');
        $id             = new THidden('id');
        //$cor            = new TColor('cor');
        $cor            = new THidden('cor');
		$docente_id 	= new TDBUniqueSearch('docente_id', 'db_painel_instrutores', 'PIDocente', 'id', 'nome', 'nome');
		$unidade_id 	= new TDBCombo('unidade_id', 'db_painel_instrutores', 'PIUnidade', 'id', '{nome}', 'nome');
		$area_id 		= new TDBCombo('area_id', 'db_painel_instrutores', 'PIArea', 'id', '{nome}', 'nome');
        $data_inicio    = new TDate('data_inicio');
        $data_fim       = new TDate('data_fim');
		$hora_inicio 	= new TCombo('hora_inicio');
		$minuto_inicio 	= new TCombo('minuto_inicio');
		$hora_fim 		= new TCombo('hora_fim');
		$minuto_fim     = new TCombo('minuto_fim');
        $tipo_atividade = new TCombo('tipo_atividade');
        $observacao     = new TText('observacao');
		
		$hora_inicio->addItems($hours);
        $minuto_inicio->addItems($minutes);
        $hora_fim->addItems($hours);
        $minuto_fim->addItems($minutes);
        
        $id->setEditable(FALSE);
        $unidade_id->setEditable(FALSE);
        $area_id->setEditable(FALSE);
        
		
		$data_fim->setMask('dd/mm/yyyy');
        $data_fim->setDatabaseMask('yyyy-mm-dd');
		$data_inicio->setMask('dd/mm/yyyy');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');
				
		/*$items = array();
		$items['AULA'] = 'AULA';
		$items['BLOQUEADO'] = 'BLOQUEADO';
		$items['PROJETO'] = 'PROJETO';
		$items['PLANEJAMENTO'] = 'PLANEJAMENTO';
		$items['HORARIO_DISPONIVEL'] = 'HORARIO DISPONÍVEL';*/
		$tipo_atividade->addItems(PIAgenda::ATIVIDADES);
		        
        // define the sizes
        $id->setSize(40);
        $docente_id->setSize('100%');
        $unidade_id->setSize(190);
        $area_id->setSize(190);
        $data_inicio->setSize(160);
        $data_fim->setSize(160);	
        $hora_inicio->setSize(70);
        $hora_fim->setSize(70);
        $minuto_inicio->setSize(70);
        $minuto_fim->setSize(70);

    
        $docente_id->setChangeAction( new TAction(array($this, 'buscarUnidadeArea')) );
        $hora_inicio->setChangeAction(new TAction(array($this, 'onChangeStartHour')));
        $hora_fim->setChangeAction(new TAction(array($this, 'onChangeEndHour')));
        $data_inicio->setExitAction(new TAction(array($this, 'onChangeStartDate')));
        $data_fim->setExitAction(new TAction(array($this, 'onChangeEndDate')));
        $tipo_atividade->setChangeAction(new TAction(array($this, 'onChangeActivity')));
        
        // add one row for each form field
        $this->form->addFields( [$view, $id] );
        $this->form->addFields( [new TLabel('Instrutor:')], [$docente_id] );
        $this->form->addFields( [new TLabel('Unidade:')], [$unidade_id], [new TLabel('Área:')], [$area_id] );
        $this->form->addFields( [new TLabel('Atividade:')], [$tipo_atividade] );
        $this->form->addFields( [new TLabel('Período:')], [$data_inicio, $data_fim] );
        $this->form->addFields( [new TLabel('Início:')], [$hora_inicio, ':', $minuto_inicio] );
        $this->form->addFields( [new TLabel('Fim:')], [$hora_fim, ':', $minuto_fim]  );
        $this->form->addFields( [new TLabel('Descrição:')], [$observacao] );
        //$this->form->addFields( [new TLabel('Cor:')],[$cor] );
        $this->form->addFields( [$cor] );
        
        
        // VERIFICA SE O GRUPO DO USUÁRIO É ADMIN
        if($this->ehAdmin()){
            $this->form->addAction( _t('Save'),   new TAction(array($this, 'onSave')),   'fa:save green');
            $this->form->addAction( _t('Clear'),  new TAction(array($this, 'onEdit')),   'fa:eraser orange');
            $this->form->addAction( _t('Delete'), new TAction(array($this, 'onDelete')), 'far:trash-alt red');
        }else{
            $docente_id->setEditable(FALSE);
    		$unidade_id->setEditable(FALSE);
    		$area_id->setEditable(FALSE);
            $data_inicio->setEditable(FALSE);
            $data_fim->setEditable(FALSE);
    		$hora_inicio->setEditable(FALSE);
    		$minuto_inicio->setEditable(FALSE);
    		$hora_fim->setEditable(FALSE);
    		$minuto_fim->setEditable(FALSE);
            $tipo_atividade->setEditable(FALSE);
            //$observacao->setEditable(FALSE);
            
        }
        
        parent::add($this->form);
    }
    
    /**
    * Executed when user leaves start hour field
    */
   public static function onChangeActivity($param=NULL)
   {

            switch ($param['tipo_atividade']) {
                case 'AULA':
                    $cor = '#3A87AD';
                    break;
                case 'BLOQUEADO':
                    $cor = '#F44336';
                    break;
                case 'PROJETO':
                        $cor = '#673AB7';
                        break;
                case 'PLANEJAMENTO':
                    $cor = '#FFC107';
                    break;
                case 'HORARIO_DISPONIVEL':
                    $cor = '#4CAF50';
                    break;
            }

            $obj = new stdClass;
            $obj->cor = $cor;
            TForm::sendData('form_event', $obj);
        
    }
     /**
     * Executed when user leaves start hour field
     */
    public static function onChangeStartHour($param=NULL)
    {
        $obj = new stdClass;
        if (empty($param['minuto_inicio']))
        {
            $obj->minuto_inicio = '0';
            TForm::sendData('form_event', $obj);
        }
        
        if (empty($param['hora_fim']) AND empty($param['minuto_fim']))
        {
            $obj->hora_fim = $param['hora_inicio'] +3;
            $obj->minuto_fim = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves end hour field
     */
    public static function onChangeEndHour($param=NULL)
    {
        if (empty($param['minuto_fim']))
        {
            $obj = new stdClass;
            $obj->minuto_fim = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves start date field
     */
    public static function onChangeStartDate($param=NULL)
    {
        if (empty($param['data_fim']) AND !empty($param['data_inicio']))
        {
            $obj = new stdClass;
            $obj->end_date = $param['data_fim'];
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves end date field
     */
    public static function onChangeEndDate($param=NULL)
    {
        if (empty($param['hora_fim']) AND empty($param['minuto_fim']) AND !empty($param['hora_inicio']))
        {
            $obj = new stdClass;
            $obj->end_hour = min($param['hora_inicio'],22) +1;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    public function onSave()
    {
        try
        {
            // open a transaction with database 'db_painel_instrutores'
            TTransaction::open('db_painel_instrutores');
            
            $this->form->validate(); // form validation
            
            // get the form data into an active record Entry
            $data = $this->form->getData();

            $start = new DateTime($data->data_inicio);
            $end = new DateTime($data->data_fim);
            $periodArr = new DatePeriod($start , new DateInterval('P1D') , $end);

            

            foreach($periodArr as $period) {
                $object = new PIAgenda;            
                $object->fromArray( (array) $data); // load the object with data
                $object->data_inicio = $period->format('Y-m-d') . ' ' . str_pad($data->hora_inicio, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->minuto_inicio, 2, '0', STR_PAD_LEFT) . ':00';
                $object->data_fim = $period->format('Y-m-d') . ' ' . str_pad($data->hora_fim, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->minuto_fim, 2, '0', STR_PAD_LEFT) . ':00';
                $object->store();
            }
            
            $object = new PIAgenda;            
            $object->fromArray( (array) $data); // load the object with data
			$object->data_inicio = $end->format('Y-m-d') . ' ' . str_pad($data->hora_inicio, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->minuto_inicio, 2, '0', STR_PAD_LEFT) . ':00';
            $object->data_fim = $end->format('Y-m-d') . ' ' . str_pad($data->hora_fim, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->minuto_fim, 2, '0', STR_PAD_LEFT) . ':00';
            $object->store(); // stores the object*/
            
            $data->id = $object->id;
            $this->form->setData($data); // keep form data
            
            TTransaction::close(); // close the transaction
            $posAction = new TAction(array('PIAgendaCompleta', 'onReload'));
            $posAction->setParameter('view', $data->view);
            $posAction->setParameter('date', $data->data_inicio);
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $posAction);
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            $this->form->setData( $this->form->getData() ); // keep form data
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database 'samples'
                TTransaction::open('db_painel_instrutores');
                
                // instantiates object CalendarEvent
                $object = new PIAgenda($key);
                
                $data = new stdClass;
                $data->id = $object->id;
                $data->observacao = $object->observacao;
                $data->docente_id = $object->docente_id;
                $data->unidade_id = $object->unidade_id;
                $data->area_id = $object->area_id;
                $data->tipo_atividade = $object->tipo_atividade;
                $data->data_inicio = substr($object->data_inicio,0,10);
                $data->hora_inicio = substr($object->data_inicio,11,2);
                $data->minuto_inicio = substr($object->data_inicio,14,2);
                $data->data_fim = substr($object->data_fim,0,10);
                $data->hora_fim = substr($object->data_fim,11,2);
                $data->minuto_fim = substr($object->data_fim,14,2);
                $data->view = $param['view'];
                
                // fill the form with the active record data
                $this->form->setData($data);
                
                // close the transaction
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Delete event
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array('PIAgendaForm', 'Delete'));
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
            // get the parameter $key
            $key = $param['id'];

            // open a transaction with database
            TTransaction::open('db_painel_instrutores');
            
            // instantiates object
            $object = new PIAgenda($key, FALSE);
            
            // deletes the object from the database
            $object->delete();
            
            // close the transaction
            TTransaction::close();
            
            $posAction = new TAction(array('PIAgendaCompleta', 'onReload'));
            $posAction->setParameter('view', $param['view']);
            $posAction->setParameter('date', TDate::date2us($param['data_inicio']));
            
            // shows the success message
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $posAction);
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Fill form from the user selected time
     */
    public function onStartEdit($param)
    {
        $this->form->clear();
        $data = new stdClass;
        $data->view = $param['view']; // calendar view
        //$data->color = '#3a87ad';
        
        if ($param['date'])
        {
            if (strlen($param['date']) == 10)
            {
                $data->data_inicio = $param['date'];
                $data->data_fim = $param['date'];
            }
            if (strlen($param['date']) == 19)
            {
                $data->data_inicio   = substr($param['date'],0,10);
                $data->hora_inicio   = substr($param['date'],11,2);
                $data->minuto_inicio = substr($param['date'],14,2);
                
                $data->data_fim   = substr($param['date'],0,10);
                $data->hora_fim   = substr($param['date'],11,2) +1;
                $data->minuto_fim = substr($param['date'],14,2);
            }
            $this->form->setData( $data );
        }
    }
    
    /**
     * Update event. Result of the drag and drop or resize.
     */
    public static function onUpdateEvent($param)
    {
        try
        {
            if (isset($param['id']))
            {
                // get the parameter $key
                $key=$param['id'];
                
                // open a transaction with database 'db_painel_instrutores'
                TTransaction::open('db_painel_instrutores');
                
                // instantiates object CalendarEvent
                $object = new PIAgendaForm($key);
                $object->data_inicio = str_replace('T', ' ', $param['data_inicio']);
                $object->data_fim   = str_replace('T', ' ', $param['data_fim']);
                $object->store();
                                
                // close the transaction
                TTransaction::close();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * method onViewEvent()
     * Executed whenever the user clicks at the view the data event
     */
    public function onViewEvent($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database 'samples'
                TTransaction::open('db_painel_instrutores');
                
                // instantiates object CalendarEvent
                $object = new PIAgenda($key);
                
                $data = new stdClass;
                $data->id = $object->id;
                $data->observacao = $object->observacao;
                $data->docente_id = $object->docente_id;
                $data->unidade_id = $object->unidade_id;
                $data->area_id = $object->area_id;
                $data->tipo_atividade = $object->tipo_atividade;
                $data->data_inicio = substr($object->data_inicio,0,10);
                $data->hora_inicio = substr($object->data_inicio,11,2);
                $data->minuto_inicio = substr($object->data_inicio,14,2);
                $data->data_fim = substr($object->data_fim,0,10);
                $data->hora_fim = substr($object->data_fim,11,2);
                $data->minuto_fim = substr($object->data_fim,14,2);
                $data->view = $param['view'];
                
                // fill the form with the active record data
                $this->form->setData($data);
                
                
                
                // close the transaction
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
     /**
     * Busca informações de unidade e área de atuação previamente cadastradas para preenchimento automático dos respectivos campos.
     * @return envia os dados de unidade e área para preenchimento dos seus respectivos campos no formulário.
     * @author brunosilva
    **/
    public static function buscarUnidadeArea($param){
        try{
            $usuarioId = $param['docente_id'];
            
            TTransaction::open('db_painel_instrutores');            
            $usuarioBuscado = PIDocente::find( $usuarioId );
            $obj = new StdClass;
            if( $usuarioBuscado instanceof PIDocente ){
                $obj->unidade_id = $usuarioBuscado->unidade_id;
                $obj->area_id = $usuarioBuscado->area_id;
                
                TForm::sendData( 'form_event', $obj );
                //TDBCombo::reloadFromModel( 'form_event', 'unidade_id', 'db_painel_instrutores', 'PIUnidade', '{nome}', '{nome}');
            }
            
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Verifica se o usuário logado faz parte do grupo de administradores.
     * @return $ehAdmin boolean
     * @author brunosilva
    **/
    private function ehAdmin(){
        
        $ehAdmin = FALSE;
        
        TTransaction::open('permission');    
        $usuario = SystemUser::newFromLogin( TSession::getValue('login') );        
        
        
        $grupoUsuario = array();
        foreach( $usuario->getSystemUserGroups() as $grupo ){
            if( strcmp($grupo->name, 'Admin') == 0 ){
                $ehAdmin = TRUE;
            }        
        }
        
        TTransaction::close();
        
        return $ehAdmin;                
    }

    /**
     * Clonar Agenda
     */
    public function onClone($param)
    {
        try
        {
            TTransaction::open('db_painel_instrutores');
            $group = new PIAgenda($param['id']);
            $group->clonarAgenda();
            TTransaction::close();
            
            $this->onReload();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}