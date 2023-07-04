<?php
/**
 * FormatarDados classe de apoio para formatação de dados
 * @version    1.0
 * @package    service
 * @author     brunosilva
**/
class FormatarDados{
    /**
     * Função para formatação de moeda.
     * @param $valor valor a ser formatado.
     * @param $object objeto de dados completo vindo do formulário.
     * @param $row linha do formulário.
     * @author brunosilva 
    **/
    public static function formatarMoeda( $valor ){
        $valorFormatado = number_format( $valor, 2, ',', '.' );
        
        $valorFormatado = "R$ ". $valorFormatado;
        
        return $valorFormatado;
    }
    
    /**
     * Função para formatação de imagem.
     * @param $imagem path da imagem a ser formatada.
     * @param $object objeto de dados completo vindo do formulário.
     * @param $row linha do formulário.
     * @author brunosilva 
    **/
    public static function formatarImagem( $imagem ){
        
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
     * Função para formatação de link de acesso
     * @param $url link de acesso ao objeto selecionado
     * @param $object objeto de dados completo vindo do formulário.
     * @param $row linha do formulário.
     * @author brunosilva 
    **/
    public static function formatarLink( $url ){
        
        return TElement::tag( 'a', 'Clique para acessar', ['href' => $url ] );
    }
    
    public static function hash( $n, $prefixo = '', $sufixo = '' ){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
  
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
  
        return $prefixo.$randomString.$sufixo;
    
    }
}
