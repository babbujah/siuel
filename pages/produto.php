<?php

    class Produto extends Page{

        protected function loadContent(){
            $produto = $this->api->getProdutoByHash($this->params['code']);
            
            if( empty($this->params['code']) || empty($produto) ){
                $this->set404();
            }
            else{
                $this->title.= ' | '.$produto->nome;
                $this->description = $produto->descricao;
                
                $this->addMeta('og:title', $this->title);
                $this->addMeta('og:type', 'website');
                $this->addMeta('og:description', $produto->descricao);
                $this->addMeta('og:image', URL_BASE.str_replace('./', '', $produto->foto));
                $this->addMeta('og:image:width', '200');
                $this->addMeta('og:image:height', '200');
                $this->addMeta('og:url', URL_BASE.'produto?code='.$produto->hash);
                $this->addMeta('og:site_name', 'ACHEI PEGAI');
                $this->addMeta('twitter:title', $this->title);
                $this->addMeta('twitter:url', URL_BASE.'produto?code='.$produto->hash);
                $this->addMeta('twitter:description', $produto->descricao);
                $this->addMeta('twitter:image', URL_BASE.str_replace('./', '', $produto->foto));

                $this->content =  '
                    <div class="row">
                        <div class="col">
                            <ul class="breadcrumb breadcrumb-style-2 d-block text-4 mb-4">
                                <li><a href="./" class="text-color-default text-color-hover-primary text-decoration-none">Principal</a></li>
                                <li><a href="./?categoria='.$produto->nome_categoria.'" class="text-color-default text-color-hover-primary text-decoration-none">'.$produto->nome_categoria.'</a></li>
                                <li>'.$produto->nome.'</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-5 mb-5 mb-md-0">
                            <div class="thumb-gallery-wrapper">
                                <img alt="" class="img-fluid" src="'.$produto->foto.'" data-zoom-image="'.$produto->foto.'">
                            </div>
                        </div>
                    
                        <div class="col-md-7">
                    
                            <div class="summary entry-summary position-relative">
                    
                                <h1 class="mb-0 font-weight-bold text-7">'.$produto->nome.'</h1>
                    
                                <div class="divider divider-small">
                                    <hr class="bg-color-grey-scale-4">
                                </div>
                    
                                <p class="price mb-3">
                                    <span class="amount">R$ '.number_format($produto->preco, 2, ',', '.').'</span>
                                </p>
                    
                                <p class="text-3-5 mb-3">'.$produto->descricao.'</p>
                    
                                <ul class="list list-unstyled text-2">
                                    <li class="mb-0">LOJA: <strong class="text-color-dark">'.$produto->nome_loja.'</strong></li>
                                    <li class="mb-0">SKU: <strong class="text-color-dark">'.$produto->hash.'</strong></li>
                                </ul>

                                <a href="'.$produto->link_afiliado.'" class="btn btn-dark btn-modern text-uppercase bg-color-hover-primary border-color-hover-primary">PEGAI</a>

                                <div class="shareon mt-4" data-url="'.URL_BASE.'produto?code='.$produto->hash.'">
                                    <a class="telegram"></a>
                                    <a class="whatsapp"></a>
                                    <a class="copy-url"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            }            
        }

    }
