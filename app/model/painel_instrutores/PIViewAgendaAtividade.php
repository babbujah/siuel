<?php

class PIViewAgendaAtividade extends TRecord
{
    const TABLENAME = 'vw_agenda';
    const PRIMARYKEY= 'ano';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $docente;
    private $unidade;
    private $area;
    private $cargaHoraria;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('docente_id');
        parent::addAttribute('unidade_id');
        parent::addAttribute('area_id');
        parent::addAttribute('mes');
    }
    
    public function getTotalAgenda($unidade_id, $docente_id, $tipo_atividade, $mes, $ano){

        $data_inicio = $ano.'-'.$mes.'-01';
        $data_fim = $ano.'-'.$mes.'-31';

        $rows = PIAgenda::where( 'data_inicio', '>', $data_inicio )
                        ->where( 'data_fim', '<', $data_fim )
                        ->where( 'tipo_atividade', '=', $tipo_atividade )
                        ->where( 'docente_id', '=', $this->docente_id )
                        ->where( 'unidade_id', '=', $unidade_id )->load();
        
        $tempoTotalSegundos = 0;                
        foreach( $rows as $row ){
            $horaInicialBruta = substr($row->data_inicio, -8);
            $horaFimBruta = substr($row->data_fim, -8);
            
            $tempoTotalSegundos += $this->converteSegundos( $horaInicialBruta, $horaFimBruta );
            
        }        

        $resultado = $tempoTotalSegundos;
        $horaFinal = floor( $resultado / 3600 );
        
        return $horaFinal;
    }
    
    private function converteSegundos( $entrada, $saida ){
        $hora1 = explode( ':', $entrada );        
        $acumulador1 = ( intval($hora1[0]) * 3600 ) + ( intval($hora1[1]) * 60 ) + intval($hora1[2]); 
        
        $hora2 = explode( ':', $saida );
        $acumulador2 = ( intval($hora2[0]) * 3600 ) + ( intval($hora2[1]) * 60 ) + intval($hora2[2]);
        
        $resultado = $acumulador2 - $acumulador1;
        
        return $resultado;
    }
    
    private function calculaTempo( $tempoTotal ){
        $resultado = $tempoTotal;
        $horaFinal = floor( $resultado / 3600 );
        $resultado = $resultado - ( $horaFinal * 3600 );
        $minFinal = floor( $resultado / 60 );
        $resultado = $resultado - ( $minFinal * 60 );
        $secFinal = $resultado;
        
        $tempo = $horaFinal.':'.$minFinal.':'.$secFinal;
        
        return $tempo;
    }

    
    

    /**
     * Add a SystemProgram to the SystemGroup
     * @param $object Instance of SystemProgram
     */
    public function addAgenda(PIAgenda $agenda)
    {
        if (PIAgenda::where('system_program_id','=',$agenda->id)->where('system_group_id','=',$this->id)->count() == 0)
        {
            $object = new SystemGroupProgram;
            $object->system_program_id = $systemprogram->id;
            $object->system_group_id = $this->id;
            $object->store();
        }
    }
    
    /**
     * Method set_docente
     * Sample of usage: $piagenda->docente = $object;
     * @param $object Instance of PIDocente
     */
    public function set_docente( PIDocente $object ){
        $this->docente = $object;
        $this->docente_id = $object->id;
        
    }
    
    /**
     * Method get_docente
     * Sample of usage: $piagenda->docente->attribute;
     * @returns PIDocente instance
     */
    public function get_docente(){
        if( empty($this->docente) ){
            $this->docente = new PIDocente($this->docente_id);
            
        }
        
        return $this->docente;
    }
    
    /**
    * Method get_unidade
    * Sample of usage: $piagenda->unidade = $object;
    * $param $object Instance of PIUnidade
    */
    public function set_unidade( PIUnidade $object ){
        $this->unidade = $object;
        $this->unidade_id = $object->id;
        
    }
    
    /**
     * Method get_unidade
     * Sample of usage: $piagenda->unidade->attribute;
     * @returns PIUnidade instance
     */
     public function get_unidade(){
         if( empty($this->unidade) ){
             $this->unidade = new PIUnidade( $this->unidade_id );
             
         }
         
         return $this->unidade;
     }
    
    /**
    * Method get_area
    * Sample of usage: $piagenda->area = $object;
    * $param $object Instance of PIArea
    */
    public function set_area( PIArea $object ){
        $this->area = $object;
        $this->area_id = $object->id;
        
    }
    
    /**
     * Method get_area
     * Sample of usage: $piagenda->area->attribute;
     * @returns PIArea instance
     */
     public function get_area(){
         if( empty($this->area) ){
             $this->area = new PIArea( $this->area_id );
             
         }
         
         return $this->area;
     }
     
     public function get_cargaHoraria(){
         return $this->cargaHoraria;
     }


}
