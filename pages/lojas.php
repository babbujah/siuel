<?php

    class Lojas extends Page{

        protected $title = 'Achei Pegai | Lojas';

        protected function loadContent(){
            $this->content = '
                <div role="main" class="main">

                <section class="page-header page-header-modern bg-color-light-scale-1 page-header-md" style="padding:0">
                    <div class="container-fluid">
                        <div class="row align-items-center">
            
                            <div class="col">
                                <div class="row">
                                    <div class="col-md-12 align-self-center p-static order-2 text-center">
                                        <div class="overflow-hidden pb-2">
                                            <h1 class="text-dark font-weight-bold text-9 appear-animation" data-appear-animation="maskUp" data-appear-animation-delay="100">Lojas Parceiras</h2>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-center order-1">
                                        <ul class="breadcrumb d-block text-center appear-animation" data-appear-animation="fadeIn" data-appear-animation-delay="300">
                                            <li><a href="./">Principal</a></li>
                                            <li class="active">Lojas</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
            
                        </div>
                    </div>
                </section>
            
                <div class="container py-2">
            
                    <ul class="nav nav-pills sort-source sort-source-style-3 justify-content-center" data-sort-id="portfolio" data-option-key="filter" data-plugin-options="{\'layoutMode\': \'fitRows\', \'filter\': \'*\'}">
                    </ul>
            
                    <div class="sort-destination-loader sort-destination-loader-showing mt-4 pt-2">
                        <div class="row portfolio-list sort-destination lightbox" data-sort-id="portfolio" data-plugin-options="{\'delegate\': \'a.lightbox-portfolio\', \'type\': \'image\', \'gallery\': {\'enabled\': true}}">
            ';

            foreach ($this->api->getLojas() as $loja) {
                $this->content.= '
                    <div class="col-md-6 col-lg-3 isotope-item brands">
                        <div class="portfolio-item">
                            <span class="thumb-info thumb-info-lighten thumb-info-no-borders thumb-info-bottom-info thumb-info-centered-icons border-radius-0">
                                <span class="thumb-info-wrapper border-radius-0">
                                    <img src="'.$loja->logo.'" class="img-fluid border-radius-0" alt="" style="width: 256px; height: 256px" onerror="this.onerror=null;this.src=\'./img/produtos/noimage.png\';">
                                    <span class="thumb-info-title">
                                        <span class="thumb-info-inner line-height-1 font-weight-bold text-dark position-relative top-3">'.$loja->nome.'</span>
                                    </span>
                                    <span class="thumb-info-action">
                                        <a href="'.$loja->link_afiliado.'">
                                            <span class="thumb-info-action-icon thumb-info-action-icon-primary"><i class="fas fa-link"></i></span>
                                        </a>
                                    </span>
                                </span>
                            </span>
                        </div>
                    </div>
                ';
            } 

            $this->content.= '</div></div></div>';
        }

    }
            
    

			