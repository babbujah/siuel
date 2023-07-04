<?php
/**
 * ProdutoList Listing
 * @version    1.0
 * @package    control/produto
 * @author     brunosilva
 */
class ProdutoList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('acheipegai');            // defines the database
        $this->setActiveRecord('Produto');   // defines the active record
        $this->setDefaultOrder('id', 'desc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('nome', 'like', 'nome'); // filterField, operator, formField
        $this->addFilterField('descricao', 'like', 'descricao'); // filterField, operator, formField
        $this->addFilterField('preco', 'like', 'preco'); // filterField, operator, formField
        $this->addFilterField('foto', 'like', 'foto'); // filterField, operator, formField
        $this->addFilterField('link_afiliado', 'like', 'link_afiliado'); // filterField, operator, formField
        //$this->addFilterField('data_criado', 'like', 'data_criado'); // filterField, operator, formField
        $this->addFilterField('id_categoria', '=', 'id_categoria'); // filterField, operator, formField
        $this->addFilterField('id_loja', '=', 'id_loja'); // filterField, operator, formField

        $this->form = new TForm('form_search_Produto');
        
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $descricao = new TEntry('descricao');
        $preco = new TEntry('preco');
        $foto = new TEntry('foto');
        $link_afiliado = new TEntry('link_afiliado');
        //$data_criado = new TEntry('data_criado');
        $id_categoria = new TDBUniqueSearch('id_categoria', 'acheipegai', 'Categoria', 'id', 'nome');
        $id_loja = new TDBUniqueSearch('id_loja', 'acheipegai', 'Loja', 'id', 'nome');

        $id->exitOnEnter();
        $nome->exitOnEnter();
        $descricao->exitOnEnter();
        $preco->exitOnEnter();
        $foto->exitOnEnter();
        $link_afiliado->exitOnEnter();
        //$data_criado->exitOnEnter();

        $id->setSize('100%');
        $nome->setSize('100%');
        $descricao->setSize('100%');
        $preco->setSize('100%');
        $foto->setSize('100%');
        $link_afiliado->setSize('100%');
        //$data_criado->setSize('100%');
        $id_categoria->setSize('100%');
        $id_loja->setSize('100%');

        $id->tabindex = -1;
        $nome->tabindex = -1;
        $descricao->tabindex = -1;
        $preco->tabindex = -1;
        $foto->tabindex = -1;
        $link_afiliado->tabindex = -1;
        //$data_criado->tabindex = -1;
        $id_categoria->tabindex = -1;
        $id_loja->tabindex = -1;

        $id->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $nome->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $descricao->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $preco->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $foto->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $link_afiliado->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        //$data_criado->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $id_categoria->setChangeAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $id_loja->setChangeAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left', '5%');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_preco = new TDataGridColumn('preco', 'Preco', 'right', '8%');
        $column_preco->setTransformer(['FormatarDados', 'formatarMoeda']);
        
        $column_foto = new TDataGridColumn('foto', 'Foto', 'center', '10%');
        $column_foto->setTransformer(['FormatarDados', 'formatarImagem']);
        
        $column_link_afiliado = new TDataGridColumn('link_afiliado', 'Link Afiliado', 'left', '10%');
        $column_link_afiliado->setTransformer( ['FormatarDados', 'formatarLink'] );
        //$column_data_criado = new TDataGridColumn('data_criado', 'Data Criado', 'left');
        $column_id_categoria = new TDataGridColumn('categoria->nome', 'Categoria', 'left', '10%');
        $column_id_loja = new TDataGridColumn('loja->nome', 'Loja', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_preco);
        
        $this->datagrid->addColumn($column_link_afiliado);
        //$this->datagrid->addColumn($column_data_criado);
        $this->datagrid->addColumn($column_id_categoria);
        $this->datagrid->addColumn($column_id_loja);
        $this->datagrid->addColumn($column_foto);
        
        $action1 = new TDataGridAction(['ProdutoForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        $action3 = new TDataGridAction([$this, 'exibirTemplatesCompartilhamento'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        $this->datagrid->addAction($action3 ,'', 'fa:share orange');
        
        // desabilita a função padrão de clique para edição do registro
        $this->datagrid->disableDefaultClick();
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // add datagrid inside form
        $this->form->add($this->datagrid);
        
        // create row with search inputs
        $tr = new TElement('tr');
        $this->datagrid->prependRow($tr);
        
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', $id));
        $tr->add( TElement::tag('td', $nome));
        $tr->add( TElement::tag('td', $descricao));
        $tr->add( TElement::tag('td', $preco));
        
        $tr->add( TElement::tag('td', $link_afiliado));
        //$tr->add( TElement::tag('td', $data_criado));
        $tr->add( TElement::tag('td', $id_categoria));
        $tr->add( TElement::tag('td', $id_loja));
        $tr->add( TElement::tag('td', ''));

        $this->form->addField($id);
        $this->form->addField($nome);
        $this->form->addField($descricao);
        $this->form->addField($preco);
        $this->form->addField($foto);
        $this->form->addField($link_afiliado);
        //$this->form->addField($data_criado);
        $this->form->addField($id_categoria);
        $this->form->addField($id_loja);

        // keep form filled
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        //$this->pageNavigation->setProperties('style', ['text-align' => 'center']); // VERIFICAR ALINHAMENTO
        
        $panel = new TPanelGroup('Produto');
        $panel->add($this->form);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        $panel->addHeaderActionLink( _t('New'),  new TAction(['ProdutoForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green' );
        
        //$panel->addHeaderActionLink( 'Criar Novos Templates de Compartilhamento', new TAction( [$this, 'novoShareTemplate'] , ['register_state' => 'false', 'static' => '1'] ), 'fa:plus green');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($panel);
        
        parent::add($container);
        
        TScript::create( "
            function copiarConteudo( containerid ){
                if (document.selection) {
                    var range = document.body.createTextRange();
                    range.moveToElementText(document.getElementById(containerid));
                    range.select().createTextRange();
                    document.execCommand('copy');
                } else if (window.getSelection) {
                    var range = document.createRange();
                    range.selectNode(document.getElementById(containerid));
                    window.getSelection().addRange(range);
                    document.execCommand('copy');
                    alert('Template copiado com sucesso.');
                 }
            }
            
        " );
    }
    
    public static function novoShareTemplate( $param ){
        TApplication::executeMethod('ShareTemplateForm'); 
    }
    
    public static function exibirTemplatesCompartilhamento( $param ){
        
        if( !empty($param['id']) ){
            TTransaction::open('acheipegai');
    
            $key = $param['id'];
            $produto = new Produto($key);
            
            $repo = new TRepository('ShareTemplate');
            $share_templates = $repo->load();
            //var_dump($share_templates);
            
            $html = TElement::tag('div', '', ['class' => 'row']);
    
            foreach( $share_templates as $template ){
                               
                $content = str_replace('{NOME}', $produto->nome, $template->content);
                $content = str_replace('{DESCRICAO}', $produto->descricao, $content );
                $content = str_replace('{PRECO}', FormatarDados::formatarMoeda($produto->preco), $content);
                $content = str_replace('{URL}', $produto->url ,$content);
        
                $div = TElement::tag('div', '', ['class' => 'col-md-6', 'style' => 'border-width: 0 1px 1px 0; border-style: solid; border-color:gray; padding:1rem']);
                $div->add( TElement::tag('div', $content, ['id' => 'template-'.$template->id]) );
                $div->add(
                    TElement::tag(
                        'button',
                        TElement::tag('i', '', ['class' => 'fa fa-copy']),
                            ['class' => 'btn btn-info',
                             'title' => '', 
                             'data-original-title' => 'Copiar',
                             'onclick' => "copiarConteudo('template-{$template->id}')"
                            ]
                        )
                );
                $html->add( $div );
            }
            
            
            
            $window = TWindow::create('Templates de compartilhamento', 0.6, 0.6);
            $window->add($html);
            $window->show();
    
            TTransaction::close();
        }    
    }
}
