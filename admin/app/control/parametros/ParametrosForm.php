<?php
/**
 * ParametrosForm Form
 * @author  <your name here>
 */
class ParametrosForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Parametros');
        $this->form->setFormTitle('Parametros');
        

        // create the form fields
        $id = new THidden('id');
        $title = new TEntry('title');
        $description = new TEntry('description');
        $author = new TEntry('author');
        $keywords = new TEntry('keywords');
        $social_whatsapp = new TEntry('social_whatsapp');
        $social_facebook = new TEntry('social_facebook');
        $social_instagram = new TEntry('social_instagram');
        $social_email = new TEntry('social_email');
        $social_telegram = new TEntry('social_telegram');


        // add the fields
        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ new TLabel('Título') ], [ $title ] );
        $this->form->addFields( [ new TLabel('Descrição') ], [ $description ] );
        $this->form->addFields( [ new TLabel('Autor') ], [ $author ] );
        $this->form->addFields( [ new TLabel('Keywords') ], [$keywords] );
        $this->form->addFields( [ new TLabel('Whatsapp') ], [ $social_whatsapp ] );
        $this->form->addFields( [ new TLabel('Facebook') ], [ $social_facebook ] );
        $this->form->addFields( [ new TLabel('Instagram') ], [ $social_instagram ] );
        $this->form->addFields( [ new TLabel('Email') ], [ $social_email ] );
        $this->form->addFields( [ new TLabel('Telegram') ], [ $social_telegram ] );



        // set sizes
        $id->setSize('100%');
        $title->setSize('100%');
        $description->setSize('100%');
        $author->setSize('100%');
        $keywords->setSize('100%');
        $social_whatsapp->setSize('100%');
        $social_facebook->setSize('100%');
        $social_instagram->setSize('100%');
        $social_email->setSize('100%');
        $social_telegram->setSize('100%');



        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
        
        if( empty($param['method']) ){
            $this->onEdit();
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
            
            $object = new Parametros;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
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
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param = [] )
    {
        try
        {
            TTransaction::open('acheipegai'); // open a transaction
            $object = Parametros::where('id', 'IS NOT', NULL)->first(); // instantiates the Active Record
            
            if( !$object instanceof Parametros ){
                $object = new Parametros;
            }
            
            $this->form->setData($object); // fill the form
            TTransaction::close(); // close the transaction
           
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
