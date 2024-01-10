<?php

/**
 * TLdap Class LDAP settings
 * @author Daniel Franklin
 */
 
 class TLdap
 {
     private $server; #Servidor de Hospedagem do AD
     private $connUser; #Usuário Admin para Consultas
     private $connPassword; #Senha do Usuário Admin 
     private $dc; #Domain Control
     private $ds; #Connection ldap
     private $auth; #autenticação
     
          
     public function __construct($server = '10.134.24.155', $connUser = 'sistemas@fiern.local', $connPassword = 'infrA@15,', $dc = 'dc=fiern,dc=local')
     {
         $this->server = $server;
         $this->connUser = $connUser;
         $this->connPassword = $connPassword;
         $this->dc = $dc;
         $this->open();
     }    
     
     public function open()
     {
         $this->ds = ldap_connect($this->server);
         ldap_set_option ($this->ds, LDAP_OPT_REFERRALS, 0);
	     ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	     ldap_set_option($this->ds, LDAP_OPT_SIZELIMIT, 5000); 
	     $this->auth = ldap_bind($this->ds, $this->connUser, $this->connPassword);         
     }
     
     
     public static function init($server = '10.134.24.155', $connUser = 'sistemas@fiern.local', $connPassword = 'infrA@15,', $dc = 'dc=fiern,dc=local')
     {
         $ldap = new TLdap;
         $ldap->open();
         return $ldap;
     }
     
     public function close()
     {
         ldap_close($this->ds);
     }
     
     
     public function getError()
     {
         return ldap_error($this->ds);         
     }
          
     
     public function getDataByCpf( $cpf )
     {
         try
         {
             if ( empty($this->ds) ){
                 throw new Exception('Não foi chamado o método ->open() de TLdap instanciando a conexão!');
             }
             
             $search = ldap_search($this->ds, $this->dc, "cpf=$cpf");
             $data = ldap_get_entries( $this->ds, $search);
             if($data['count'] == 0){
                 return [];
             }
             $data = $data[0];
             $return = [];
             foreach($data as $index => $d){
                 if(!is_numeric($index)){
                     if( is_array($data[$index]) ){
                        $count = $data[$index]['count'];
                        if($count == 1){
                            $return[$index] = $data[$index][0];
                        }else{
                            $obj = [];
                            for($i = 0;$i<$count;$i++){
                                $obj[$i] = $data[$index][$i];
                            }
                            $return[$index] = $obj;
                        }   
                     } 
                 }
             }             
             return $return;
         }
         catch(Exception $e){
             new TMessage('error', $e->getMessage());    
         }                    
     }
     
     public function getDataByField($field , $value , array $attr = array() /* Parâmetros de retorno da busca */ , $order = NULL)
     {
         try
         {
             if ( empty($this->ds) ){
                 throw new Exception('Não foi chamado o método ->open() de TLdap instanciando a conexão!');
             }
             
             $attributes = [];
             if ( !empty($attr) )
             {
                 foreach( $attr as $a )
                 {
                     $attributes[] = $a;    
                 }        
             }
             
             $search = ldap_search($this->ds, $this->dc, "$field=$value", $attributes);
             
             if ( !empty($order) )
    		 {
    		     ldap_sort( $this->ds, $search, $order);
    		 }
             
             $data = ldap_get_entries( $this->ds, $search);
             
             if($data['count'] == 0){
                 return [];
             }
             
            return $this->formatArrayData($data);
         }
         catch(Exception $e){
             new TMessage('error', $e->getMessage());    
         }           
     }
     
     
     public function validateUser($user,$password,$field = 'mail')
     {
         try
         {
             $search = $this->getDataByField($field,$user);
             
             if ( empty($search) ){
                 return false;
             }
             
             $bind = @ldap_bind($this->ds, $search[0]['distinguishedname'], $password);
             
             $this->close($this->ds);
             $this->open();
             
             return !!$bind;
         }
         catch(Exception $e){
             new TMessage('error', $e->getMessage()); 
             return false;   
         }     
     }
     
     /**
     * Função para Salvar ou Editar campos no AD
     * $params deve receber Array identado com os campos do AD e conter obrigatoriamente cpf e cn
     * $ou deve receber array com a localização onde o usuário deverá estar localizado, deve ser seguida a ordem da árvore exatamente igual ao AD
     */
     public function store(array $params, array $ou = array() )
     {
          try
         {
             if( empty($params['cpf']) ){
                 throw new Exception('O parâmetro obrigtório "cpf" não foi encontrado no Array de Parâmetros');
             }    
             
             $busca = $this->getDataByCpf($params['cpf']);          
            
             if( empty($busca) ){   #cria usuário                
                 
                 if( empty($params['cn']) ){
                     throw new Exception('O parâmetro obrigtório "cn" não foi encontrado no Array de Parâmetros');
                 } 
                 
                 empty($ou) ? $ou = array("NOVOS") : '';
                 $ou_atual = '';
                 for ($i=(count($ou)-1) ; $i>=0 ; $i--){
                    $ou_atual.= ',ou='.$ou[$i];
                 }
                 
                 $dn = "cn=".$params['cn'].$ou_atual.','.$this->dc;                     
                 $params["objectClass"] = "user";
                 
                 $result = ldap_add( $this->ds, $dn , $params );                 
                 
                 return $result;
                 
             }
             else{ #edita Usuário já existente
                 
                 $dn = $busca['distinguishedname'];
                 unset($params['cpf']);
                 unset($params['cn']);
                 $result = ldap_modify( $this->ds, $dn, $params );
                 
                 if( !empty($ou) ){
                 
                     $ou_atual = '';
                     for ($i=(count($ou)-1) ; $i>=0 ; $i--){
                        $ou_atual.= 'ou='.$ou[$i].',';
                     }
                     
                     $result = ldap_rename( $this->ds, $dn ,'cn='.$busca['cn'], $ou_atual.$this->dc, TRUE );                 
                     return  $result;
                 }                
                 return $result;    
             }            
             
                
         }
         catch(Exception $e){
             new TMessage('error', $e->getMessage());    
         }
     
     }
     
     public function enableUserByField( $field, $value, $attr = [] /* atributos que serão alterados */ ){
         $search = $this->getDataByField($field, $value);
         
         if( !empty($search) ){
             $user = (object) $search[0];
                          
             $attr['userAccountControl'] = dechex(512);
             $attr['distinguishedname'] = $user->distinguishedname;
             $attr['cn'] = $user->cn; 
             
             return $this->update($attr);
         }
         
         throw new Exception("{$field} {$value} não encontrado.");
     }
     
     public function disableUserByField( $field, $value, $attr = [] /* atributos que serão alterados */ ){
         $search = $this->getDataByField($field, $value);
         
         if( !empty($search) ){
             $user = (object) $search[0];
             
             $attr['userAccountControl'] = dechex(514);
             $attr['distinguishedname'] = $user->distinguishedname;
             $attr['cn'] = $user->cn; 
             
             return $this->update($attr);
         }
         
         throw new Exception("{$field} {$value} não encontrado.");
     }
     
     public function update( array $params /*Parâmetros que serão alterados*/ , $novaOu = NULL /* string contendo DN da OU de destino*/)
     {
         try
         {
             if( empty($params['distinguishedname']) || empty($params['cn'])  )
                 throw new Exception('Os atributos DISTINGUISHEDNAME e CN precisam ser enviados no array de parâmetros');
             
             $params = $this->formatBlank($params);
             
             $cn = $params['cn'];
             $dn = $params['distinguishedname'];
             
             unset($params['distinguishedname']);
             unset($params['cn']);
             unset($params['name']);
             
             $result = ldap_modify( $this->ds, $dn, $params );
             if (!$result)
                     throw new Exception('Não foi possivel realizar as alterações do usuário. - '.$this->getError());             
                     
             if( !is_null($novaOu) ){                
                 $result = ldap_rename( $this->ds, $dn , 'cn='.$cn , $novaOu , TRUE );
                 if (!$result)
                     throw new Exception('Não foi possivel realizar a mudança de OU. - '.$this->getError());
             }                
             return $result;
         }
         catch(Exception $e)
         {
             new TMessage('error', $e->getMessage());        
         }        
     }
     
     
     public function delete($field , $value )
     {
        try
        {
            $busca = $this->getDataByField($field,$value);
            
            if( empty($busca) ){
                throw new Exception("Não existem registros que atendam a essa solicitação");
            }
            
            foreach($busca as $b){
                ldap_delete( $this->ds, $b['distinguishedname'] );
            }
            
        }
        catch(Exception $e){
            new TMessage('error', $e->getMessage());    
        }
         
     }
     
     public function getUsuarios(array $attr = array() /* Parâmetros de retorno da busca */ , $order = NULL)
     {
         $attributes = [];
         if ( !empty($attr) )
         {
             foreach( $attr as $a )
             {
                 $attributes[] = $a;    
             }        
         }
         $search_filter = '(&(objectCategory=person)(mail=*)(objectClass=user))';
         $search = ldap_search($this->ds, $this->dc, $search_filter, $attributes);
		 
		 if ( !empty($order) )
		 {
		     ldap_sort( $this->ds, $search, $order);
		 }
		 
		 $data = ldap_get_entries($this->ds, $search);
		 
		 return $this->formatArrayData($data);        
     }
     
     public function getUsuariosAtivos(array $attr = array() /* Parâmetros de retorno da busca */ , $order = NULL)
     {
         $attributes = [];
         if ( !empty($attr) )
         {
             foreach( $attr as $a )
             {
                 $attributes[] = $a;    
             }        
         }
         $search_filter = '(&(objectCategory=person)(mail=*)(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))';
         $search = ldap_search($this->ds, $this->dc, $search_filter, $attributes);
		 
		 if ( !empty($order) )
		 {
		     ldap_sort( $this->ds, $search, $order);
		 }		 
		 
		 $data = ldap_get_entries($this->ds, $search);
		 
		 return $this->formatArrayData($data);        
     }
     
     public function getUsuariosInativos(array $attr = array() /* Parâmetros de retorno da busca */ , $order = NULL)
     {
         $attributes = [];
         if ( !empty($attr) )
         {
             foreach( $attr as $a )
             {
                 $attributes[] = $a;    
             }        
         }
         $search_filter = '(&(objectCategory=person)(mail=*)(objectClass=user)(userAccountControl:1.2.840.113556.1.4.803:=2))';
         $search = ldap_search($this->ds, $this->dc, $search_filter, $attributes);
		 
		 if ( !empty($order) )
		 {
		     ldap_sort( $this->ds, $search, $order);
		 }
		 
		 $data = ldap_get_entries($this->ds, $search);
		 
		 return $this->formatArrayData($data);
     }
     
     
     public function getAllOU()
     {
         $attributes = array("dn", "ou");
         $search_filter="(objectClass=organizationalunit)";
         $search = ldap_search($this->ds, $this->dc, $search_filter, $attributes);
         ldap_sort( $this->ds, $search, 'ou');
         $data = ldap_get_entries($this->ds, $search);
         $data_format = [];
         foreach ( $data as $index => $d  )
         {
             if ( $d['ou'][0] )
             $data_format[] = array( 'ou' => $d['ou'][0], 'dn' => $d['dn'] );   
         }
         
         return $data_format;           
     }
     
         
     public function convertZeroaEsquerdaCPF()
     {
           $users = $this->getUsuarios();
           $count = 0;
           foreach( $users as $u )
           {
               if( isset($u['cpf']) )
               {
                   if ( strlen($u['cpf']) < 11)
                   {
                       $params['cpf'] = str_pad( $u['cpf'], 11, '0', STR_PAD_LEFT );
                       if ( ldap_modify( $this->ds, $u['distinguishedname'], $params ) )
                       {
                           $count++;
                       }                           
                   }                                      
               }
           }
           return $count; //Retorna quantidade de CPFs atualizados
     }
     
     public static function convertTimestampToWindows( $timestamp )
     {
         $time = strtotime('-1 hour',strtotime($timestamp));
         //$time = strtotime($timestamp);
         return ( $time + 11644477200).'0000000';
     }
    
     public static function convertWindowsToTimestamp( $win_time ) {
         //round the win timestamp down to seconds and remove the seconds between 1601-01-01 and 1970-01-01
         $time = round($win_time / 10000000) - 11644477200;
         return date('Y-m-d H:i:s',$time);
     }
     
     public function formatBlank(array $params)
     {
         try
         {
             $excecao = [
                 'accountexpires' => TRUE
             ];
             foreach( $params as $index => $p )
             {            
                if( empty($params[$index]) && !isset($excecao[$index]) )
                {
                     $params[$index] = array();
                }
             }
             return $params;     
         }
         catch(Exception $e)
         {
             new TMessage('error', $e->getMessage() );
         }
     }
         
     /*
     **Formata Array enviado pelo AD para Array Identado
     */
     public function formatArrayData($data)
     {
         $return_global = [];
         $count_global = $data['count'];
         
         for($i=0;$i<$count_global;$i++){
             $current = $data[$i];
             $return = [];                
             foreach($current as $index => $d){
                 if(!is_numeric($index)){
                     if( is_array($current[$index]) ){
                        $count = $current[$index]['count'];
                        if($count == 1){
                            $return[$index] = $current[$index][0];
                        }else{
                            $obj = [];
                            for($j = 0;$j<$count;$j++){
                                $obj[$j] = $current[$index][$j];
                            }
                            $return[$index] = $obj;
                        }   
                     } 
                 }
             } 
            
            $return_global[] = $return;                 
         }
         
         return $return_global;          
     }
      
 }