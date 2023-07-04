<?php
/**
 * Classe para manipular imagem
 * @version 1.0
 * @package service
 * @author brunosilva
**/
class ManipuladorImagem{
    
    /**
     * Função para formatação de imagem.
     * @param $imagem path da imagem a ser formatada.
     * @param $object objeto de dados completo vindo do formulário.
     * @param $row linha do formulário.
     * @author brunosilva 
    **/
    public static function formatarImagem( $imagem, $object, $row ){
        
        $capturaImagem = $imagem;
        if( empty($capturaImagem) ){
            //return "";
            //return URL_BASE.'img/produtos/noimage.png';
            $capturaImagem = 'img/produtos/noimage.png';
            
        }
        
        //$imagemFormatada = new TImage( URL_BASE.$imagem );
        $imagemFormatada = new TImage( URL_BASE.$capturaImagem );
        $imagemFormatada->style = 'max-width: 140px';
        $imagemFormatada->onerror = "this.onerror=null;this.src='app/images/noimage.png';";
        
        return $imagemFormatada;
    }
    
    /**
     * Mudança da imagem do objeto
     * @param $param Resquest
     * @author brunosilva
    **/
    public static function onChangeImagem( $param ){
        if( !empty($param['imagem_nova_field']) ){
            $param['imagem_nova'] = $param[$param['imagem_nova_field']];
            
        }
        
        if( !empty($param['imagem_nova']) ){
            $imagem = empty($param['use_path']) ? './tmp/'.$param['imagem_nova'] : $param['imagem_nova'];
            TScript::create( "
                    $('#imagem_view').attr('src', '".$imagem."');
            " );
            
        }
    }

}
