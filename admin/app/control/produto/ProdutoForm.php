<?php
/**
 * ProdutoForm Form
 * @version    1.0
 * @package    control/produto
 * @author     brunosilva
 */
class ProdutoForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Produto');
        $this->form->setFormTitle('Produto');
        

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $descricao = new TText('descricao');
        
        $preco = new TEntry('preco');
        $preco->setNumericMask(2, ',', '.', true);
        $preco->style = 'text-align: left';
        
        $foto_nova = new TFile('foto_nova');
        $foto_nova->setAllowedExtensions(['png', 'jpg']);
        $foto_nova->setCompleteAction( new TAction( [$this, 'onChangeFoto'] ) );
        
        $foto_nova_web = new TEntry('foto_nova_web');
        $foto_nova_web->setExitAction( new TAction( [$this, 'onChangeFotoWeb'] ) );
        
        $link_afiliado = new TEntry('link_afiliado');
        //$link_afiliado->setValueCallback( function(){
        //    return TElement::tag( 'a', 'Clique para acessar', ['href' => $link_afiliado ] );
        //}
        
        //$categoria = new TDBUniqueSearch('id_categoria', 'acheipegai', 'Categoria', 'id', 'nome');
        $categoria = new TDBCombo( 'id_categoria', 'acheipegai', 'Categoria', 'id', 'nome' );
        $categoria = new TDBEntry( 'nome_categoria', 'acheipegai', 'Categoria', 'nome', 'nome' );
        //$categoria->enableSearch();
        //$categoria->;
        
        $loja = new TDBCombo('id_loja', 'acheipegai', 'Loja', 'id', 'nome');
        $loja->enableSearch();
        
        $foto_view = new TImage('app/images/noimage.png');
        $foto_view->id = 'foto_view';
        $foto_view->width = '200';
        
        // add the fields
        $this->form->addFields( [ new TLabel('Id'), $id ] );
        $this->form->addFields( [ new TLabel('Nome'), $nome ] );
        $this->form->addFields( [ new TLabel('Descricao'), $descricao ] );
        $this->form->addFields( [ new TLabel('Link Afiliado'), $link_afiliado ] );
        $this->form->addFields( [ new TLabel('Preco'), $preco ], [ new TLabel('Categoria'), $categoria ], [ new TLabel('Loja'), $loja ] );
        $this->form->addFields( [ new TLabel('Foto'), $foto_nova ], [new TLabel('Foto Web'), $foto_nova_web] );
        $this->form->addContent( [$foto_view] );
        //$this->form->addFields( [$foto_view] );
                
        

        // set sizes
        $id->setSize('100%');
        $nome->setSize('100%');
        $descricao->setSize('100%');
        $preco->setSize('100%');
        $foto_nova->setSize('100%');
        $link_afiliado->setSize('100%');
        $categoria->setSize('100%');
        $loja->setSize('100%');
        //$foto_view
        $foto_nova->setSize('100%');
        $foto_nova_web->setSize('100%');
        
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         // validations
         //$nome->addValidation( 'Nome', new TRequiredValidator );
         $nome->addValidation( 'Nome', new TMinLengthValidator, [2] );
         $link_afiliado->addValidation( 'Link Afiliado', new TMinLengthValidator, [3] );
         $preco->addValidation( 'Preço', new TRequiredValidator );
         $categoria->addValidation( 'Categoria', new TRequiredValidator );
         $loja->addValidation( 'Loja', new TRequiredValidator );
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addHeaderActionLink(_t('Back'),  new TAction(['ProdutoList', 'onReload']), 'fa:arrow-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    public static function onChangeFotoWeb($param){
        try{
            if( !empty($param['foto_nova_web'] ) ){
                                
                if (filter_var($param['foto_nova_web'], FILTER_VALIDATE_URL) === FALSE && stristr($param['foto_nova_web'], 'data:image') === FALSE) {
                    throw new Exception('Digite com uma url válida.');
                }
                
                //$content = file_get_contents($param['foto_nova_web']);
                
                $url = $param['foto_nova_web'];
            	$ch = curl_init();
            	curl_setopt($ch, CURLOPT_URL, $url);
            	curl_setopt($ch, CURLOPT_HEADER, false);
            	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            	curl_setopt($ch, CURLOPT_USERAGENT, 'AcheiPegai/2023.1.0');
            	$content = curl_exec($ch);
            	$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
            	curl_close($ch) ;
            	
            	if( $rescode != 200 ){
            		throw new Exception('Não foi possível carregar a imagem socilitada.');
            	}
                
                $nome_foto_nova = md5(rand()).'.jpg';
                file_put_contents('tmp/'.$nome_foto_nova, $content);
                
                $object = new stdClass;
                $object->foto_nova = $nome_foto_nova;
                
                TForm::sendData('form_Produto', $object);
                
                self::onChangeFoto(['foto_nova' => $nome_foto_nova]);
            }
        }catch(Exception $e){
            new TMessage('error', $e->getMessage());
        }
        
    }
    
    public static function onChangeFoto($param){
        
        if( !empty($param['foto_nova']) ){
            $foto = empty($param['use_path']) ? './tmp/'.$param['foto_nova'] : $param['foto_nova'];
            TScript::create( "
                    $('#foto_view').attr('src', '".$foto."');
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
            
            $categoria = Categoria::saveByNome( $data->nome_categoria );
            
            $object = empty($data->id) ? new Produto : new Produto($data->id);  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            
            $object->id_categoria = $categoria->id;
            
            $object->store(); // save the object
            
            if( !empty($data->foto_nova) ){
                $nome_foto = md5(rand()).'.png';
                rename('tmp/'.$data->foto_nova, '../img/produtos/'.$nome_foto);
                $object->foto = './img/produtos/'.$nome_foto;               
                
                $object->store();
                
                unset($data->foto_nova);
                unset($data->foto_nova_web);
            }
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            if( !empty($object->foto) ){
                self::onChangeFoto( ['foto_nova' => URL_BASE.$object->foto, 'use_path' => true ]  );
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
                $object = new Produto($key); // instantiates the Active Record
                $object->nome_categoria = $object->categoria->nome;
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
                
                if( !empty($object->foto) ){
                    self::onChangeFoto( ['foto_nova' => URL_BASE.$object->foto, 'use_path' => true ]  );
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
