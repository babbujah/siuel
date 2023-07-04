<?php
/**
 * LojaForm Form
 * @version    1.0
 * @package    control/loja
 * @author     brunosilva
 */
class LojaForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Loja');
        $this->form->setFormTitle('Loja');        

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $link_afiliado = new TEntry('link_afiliado');
        $logo_nova = new TFile('logo_nova');
        $logo_nova->setAllowedExtensions(['png', 'jpg']);
        //$logo_nova->setCompleteAction( new TAction( [$this, 'onChangeLogo'] ) );
        $logo_nova->setCompleteAction( new TAction( ['ManipuladorImagem', 'onChangeImagem'], ['imagem_nova_field' => 'logo_nova'] ) );
        //$logo_nova->setCompleteAction( new TAction( ['ManipuladorImagem', 'onChangeImagem'], ['imagem_nova' => 'logo_nova'] ) );
        
        $logo_view = new TImage('app/images/noimage.png');
        $logo_view->width = '200';
        $logo_view->id = 'imagem_view'; // definição do id para o classe ManipuladorImagem
        $logo_view->onerror = "this.onerror=null;this.src='app/images/noimage.png';"; // altera a imagem para a padrão quando acontece algum erro 
        
        // add the fields
        $this->form->addFields( [ new TLabel('Id'), $id ] );
        $this->form->addFields( [ new TLabel('Nome'), $nome ] );
        $this->form->addFields( [ new TLabel('Link Afiliado'), $link_afiliado ] );
        $this->form->addFields( [ new TLabel('Logo'), $logo_nova ] );
        $this->form->addContent( [$logo_view] );        

        // set sizes
        $id->setSize('100%');
        $nome->setSize('100%');
        $link_afiliado->setSize('100%');
        $logo_nova->setSize('100%');
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
                 
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Mudança da imagem da loja
     * @param $param Request
     * @author brunosilva
     * @deprecated
    **/
    public static function onChangeLogo( $param ){
        if( !empty($param['logo_nova']) ){
            $logo = empty($param['use_path']) ? './tmp/'.$param['logo_nova'] : $param['logo_nova'];
            TScript::create( "
                    $('#logo_view').attr('src', '".$logo."');
            " );
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
            
            $object = new Loja;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            if( !empty($data->logo_nova) ){
                $logo = md5(rand()).'.png';
                rename('tmp/'.$data->logo_nova, '../img/lojas/'.$logo);
                $object->logo = './img/lojas/'.$logo;
                $object->store();
                unset($data->logo_nova);            
            }
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            
            TTransaction::close(); // close the transaction
            
            if( !empty($object->logo) ){
                //self::onChangeLogo( ['logo_nova' => URL_BASE.$object->logo, 'use_path' => true ]  );
                //ManipuladorImagem::onChangeLogo( ['logo_nova' => URL_BASE.$object->logo, 'use_path' => true ]  );
                ManipuladorImagem::onChangeImagem( ['imagem_nova' => URL_BASE.$object->logo, 'use_path' => true ]  );
                
            }
            
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
                $object = new Loja($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
                
                if( !empty($object->logo) ){
                    //self::onChangeLogo( ['logo_nova' => URL_BASE.$object->logo, 'use_path' => true ]  );
                    ManipuladorImagem::onChangeImagem( ['imagem_nova' => URL_BASE.$object->logo, 'use_path' => true ]  );
                    
                    
                }
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
}
