<?php
/**
* Classe que representa o formulário de cadastro do Docente.
* PIDocenteForm Registration
*
* @version    1.0
* @package    control/painel_instrutores
* @author     Bruno Lopes
* @since      17/11/2023
**/
class PIDocenteForm extends TPage
{
    protected $form; // form
    private $cpf;
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('db_painel_instrutores');              // defines the database
        $this->setActiveRecord('PIDocente');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_PIDocente');
        $this->form->setFormTitle('Cadastro de Docente');        

        // create the form fields
        $id = new THidden('id');
        $id->setSize('100%');
                
        //$nome = new TEntry('nome');
        $usuariosAtivos = $this->buscarUsuariosAtivosAD();
        $nome = new TCombo('nome');
        $nome->addItems($usuariosAtivos);
        $nome->setSize('100%');  
        
        $this->cpf = new TEntry('cpf');
        $this->cpf->setMask( '999.999.999-99', true );
        $this->cpf->setSize('100%');
        
        $email = new TEntry('email');
        $email->setSize( '100%' );
        
        $tipo_contratacao = new TCombo('tipo_contratacao');
        $tipo_contratacao->addItems( PIDocente::TIPOCONTRATACAO );
        $tipo_contratacao->setSize('100%');
        
        //$data_cadastro = new TEntry('data_cadastro');
        //$data_cadastro->setSize('100%');
        
        //$usuario_cadastro = new TEntry('usuario_cadastro');
        //$usuario_cadastro->setValue(TSession::getValue('username'));
        //$usuario_cadastro->setSize('100%');
        
        $unidade_id = new TDBCombo('unidade_id', 'db_painel_instrutores', 'PIUnidade', 'id', 'nome');
        //$unidade_id->enableSearch();
        $unidade_id->setSize('100%');        
        
        $area_id = new TDBCombo( 'area_id', 'db_painel_instrutores', 'PIArea', 'id', 'nome' );
        $area_id->enableSearch();
        $area_id->setSize('100%');
        
        $nome->setChangeAction( new TAction(array($this, 'buscarInformacoesUsuario')) );
        $tipo_contratacao->setChangeAction( new TAction(array($this, 'aplicarMascaraCpf')) );
        //$email->setExitAction( new TAction(array($this, 'buscarInformacoesUsuarioEmail')) );
        //$cpf->setExitAction( new TAction(array($this, 'buscarInformacoesUsuarioCpf')) );
        
        // add the fields
        $this->form->addFields( [ $id ] );        
        $this->form->addFields( [ new TLabel('Nome'), $nome ] );
        $this->form->addFields( [ new TLabel('CPF'), $this->cpf ], [ new TLabel('E-mail'), $email ] );
        //$this->form->addFields( [ new TLabel('E-mail') ], [ $email ] );
        $this->form->addFields( [ new TLabel('Tipo Contratacão'), $tipo_contratacao ], [ new TLabel('Unidade'), $unidade_id ] );
        //$this->form->addFields( [ new TLabel('Data Cadastro') ], [ $data_cadastro ] );
        //$this->form->addFields( [ new TLabel('Usuario Cadastro') ], [ $usuario_cadastro ] );
        //$this->form->addFields( [ new TLabel('Unidade') ], [ $unidade_id ] );
        $this->form->addFields( [ new TLabel('Area'), $area_id ] );
                
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         // validations
        $nome->addValidation( 'Nome', new TRequiredValidator );
        
        $this->cpf->addValidation( 'CPF', new TRequiredValidator );
        $this->cpf->addValidation( 'CPF', new TMaxLengthValidator, [14] );
        $this->cpf->addValidation( 'CPF', new TCPFValidator );
        
        $email->addValidation( 'E-mail', new TRequiredValidator );
        $email->addValidation( 'E-mail', new TEmailValidator );
        
        $tipo_contratacao->addValidation( 'Tipo Contratacão', new TRequiredValidator );
        $unidade_id->addValidation( 'Unidade', new TRequiredValidator );
        $area_id->addValidation( 'Area', new TRequiredValidator );
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction(_t('Back'),  new TAction(['PIDocenteList', 'onReload']), 'fa:undo');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    public function onSave( $param ){
        try{
            TTransaction::open('db_painel_instrutores'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            // GRAVA DOCENTE PARA ACESSO AO MÓDULO DE AGENDA
            $object = new PIDocente;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            //$object->cpf = preg_replace( "/[^0-9]/", "", $data->cpf ); // Salva somente números
            $object->nome = strtoupper($data->nome);
            $object->usuario_cadastro = TSession::getValue('username'); // Grava usuário da sessão
            $object->store(); // save the object
                        
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            // INICIO GRAVA O USUARIO DE ACESSO E SUAS PERMISSÕES
            TTransaction::open('permission');
            
            $criteria = new TCriteria;
            $criteria->add( new TFilter( 'email', '=', $data->email ) );
            
            $repository = new TRepository('SystemUser');
            $usuarios = $repository->load( $criteria );
            
            $user = new SystemUser;
            if( $usuarios ){
                $user = $usuarios[0];            
            }
            
            $user->name = $data->nome;
            $user->login = $data->email;
            $user->password = md5('12345678');
            $user->email = $data->email;
            $user->active = 'Y';
            
            $user->store();
            
            $criteria = new TCriteria;
            $criteria->add( new TFilter( 'system_user_id', '=', $user->id ) );
            $grupo = SystemGroup::findByName('Instrutor');
            $criteria->add( new TFilter( 'system_group_id', '=', $grupo->id ) );
            $repository = new TRepository( 'SystemUserGroup' );
            $grupoUsuario = $repository->load( $criteria );
            if( empty($grupoUsuario) ){
                //$newSystemUserGroup = new SystemUserGroup;
                //$newSystemUserGroup->system_user_id = $user->id;
                //$newSystemUserGroup->system_group_id = $grupoId;
                
                $user->addSystemUserGroup($grupo);
                
                //$newSystemUserGroup->store();
            }
            
            TTransaction::close();
            // FIM GRAVA O USUARIO DE ACESSO E SUAS PERMISSÕES
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }catch (Exception $e){ // in case of exception
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Busca Usuários ativos no Active Directory - AD para preencher lista de usuários.
     * @return $nomes lista de usuário ativos encontrados no AD.
     * @author brunosilva
    **/
    private function buscarUsuariosAtivosAD(){
        $ldap = new TLdap;
        $usuariosAtivos = $ldap->getUsuariosAtivos(['name']);
        $ldap->close();
        
        asort( $usuariosAtivos );
        
        $nomes = [];
        foreach( $usuariosAtivos as $usuario ){
            $nomes[$usuario['name']] = $usuario['name'];
        }
        
        //$nomes = asort($nomes);
        
        return $nomes;
    }
    
    /**
     * Busca informações de email e cpf de usuários selecionados na lista de nomes de usuários.
     * @return envia os dados de email e cpf para preenchimento dos seus respectivos campos no formulário.
     * @author brunosilva
    **/
    public static function buscarInformacoesUsuario($param){
        try{
            
            $nome = !empty($param['nome']) ? $param['nome'] : "";
                                                
            $ldap = new TLdap;
            $usuarioBuscado = $ldap->getDataByField( 'name', $nome, ['mail', 'cpf'] );
            
            $obj = new StdClass;
            $obj->email = !empty($usuarioBuscado) ? $usuarioBuscado[0]['mail'] : ""; 
            $obj->cpf = !empty($usuarioBuscado) ? $usuarioBuscado[0]['cpf'] : "";
            
            $ldap->close();
            
            TTransaction::open('db_painel_instrutores');
            $docente = PIDocente::findByEmail( $obj->email );
            TTransaction::close();
            
            if( !empty($docente) ){
                //$obj = new PIDocente;
                //$obj->carregarDocente( $docente );
                $obj->id = $docente->id;
                //$obj->nome = $docente->nome;                
                $obj->cpf = $docente->cpf;
                $obj->email = $docente->email;
                $obj->tipo_contratacao = $docente->tipo_contratacao;
                $obj->unidade_id = $docente->unidade_id;
                $obj->area_id = $docente->area_id;
                                
            }else{
                $obj->id = null;
                //$obj->nome = $docente->nome;                
                $obj->tipo_contratacao = null;
                $obj->unidade_id = null;
                $obj->area_id = null;
                                
            }
            
            TForm::sendData( 'form_PIDocente', $obj );
            
        }catch( Exception $e ){
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function aplicarMascaraCpf($param){
        TEntry::changeMask('form_PIDocente', $param['cpf'], '999.999.999-99');
    }
}
