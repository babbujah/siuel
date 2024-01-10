<?php

class PIAgenda extends TRecord
{
    const TABLENAME = 'agenda';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $docente;
    private $unidade;
    private $area;
    private $cargaHoraria;
    
    const ATIVIDADES = ['AULA' => 'AULA',
                        'BLOQUEADO' => 'BLOQUEADO',
                        'PROJETO' => 'PROJETO',
                        'PLANEJAMENTO' => 'PLANEJAMENTO',
                        'HORARIO_DISPONIVEL' => 'HORARIO DISPONÍVEL'];
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('docente_id');
        parent::addAttribute('unidade_id');
        parent::addAttribute('area_id');
        parent::addAttribute('tipo_atividade');
        parent::addAttribute('data_inicio');
        parent::addAttribute('data_fim');
        parent::addAttribute('observacao');
        parent::addAttribute('cor');
        parent::addAttribute('data_cadastro');
        parent::addAttribute('usuario_cadastro');
        parent::addAttribute('title');
    }
    
    /*public function contabilizarCargaHoraria( $data_inicio = null, $data_fim = null, $area_id = null ){
        if( empty($data_inicio) ){
            $hoje = date('Y-m-d');
            list($ano, $mes, $dia) = explode('-', $hoje); 
            
            $data_inicio = $ano.'-'.$mes.'-01';
            
        }
        
        if( empty($data_fim) || $data_fim < $data_fim ){
            $hoje = date('Y-m-d');
            list($ano, $mes, $dia) = explode('-', $hoje);
            $data_fim = date($ano.'-'.$mes.'-31');
            
        }
        
        if( empty($area_id) ){
            $area_id = $this->area->id;
            
        }
                
        $rows = PIAgenda::where( 'data_inicio', '>', $data_inicio )
                        ->where( 'data_fim', '<', $data_fim )
                        ->where( 'docente_id', '=', $this->docente_id )
                        ->where( 'area_id', '=', $area_id )->load();
        
        $tempoTotalSegundos = 0;                
        foreach( $rows as $row ){
            $horaInicialBruta = substr($row->data_inicio, -8);
            $horaFimBruta = substr($row->data_fim, -8);
            
            $tempoTotalSegundos += $this->converteSegundos( $horaInicialBruta, $horaFimBruta );
            
        }
        
        $this->cargaHoraria = $this->calculaTempo( $tempoTotalSegundos );
                
        //return $tempo;
        
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

    
<<<<<<< HEAD
    public function getTotalAulas($unidade_id, $instrutor_id){
        //PIAgenda::where('unidade_id', '=', $unidade_id)->where('docente_id', '=', $instrutor_id)->groupBy('tipo_atividade')->countBy('id', 'count');
    }*/

    public function get_diferenca_horas(){

        $horaInicialBruta = substr($this->data_inicio, -8);
        $horaFimBruta = substr($this->data_fim, -8);

        $hora1 = explode( ':', $horaInicialBruta );        
        $acumulador1 = ( intval($hora1[0]) * 3600 ) + ( intval($hora1[1]) * 60 ) + intval($hora1[2]); 
                    
        $hora2 = explode( ':', $horaFimBruta );
        $acumulador2 = ( intval($hora2[0]) * 3600 ) + ( intval($hora2[1]) * 60 ) + intval($hora2[2]);
                    
        $tempoTotal = $acumulador2 - $acumulador1;
                    
        return $value = floor( $tempoTotal / 3600 );
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
