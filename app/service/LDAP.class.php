<?php

// Classe responsável pelo login através do LDAP V3 (Active Directory)
// Autor: Luiz Costa (inspirado em um script feito por Daniel Franklin)

class LDAP{

    const srvFiern = '10.134.25.156';
    const srvCtgas = '10.10.11.222' ;
    
    private static $ativos;
    private static $inativos;
        
    public static function valida($usr, $pwd){
        if( self::validaFiern($usr,$pwd, self::srvFiern) ){
			return true;
		}elseif ( self::validaCtgas($usr,$pwd, self::srvCtgas) ) {
			return true;
		}else{
			return false;
		}
    }
    
    public static function validaCtgas($usr, $pwd, $srv = '10.10.11.222')
	{
	    $ds = ldap_connect($srv);
	    ldap_set_option ($ds, LDAP_OPT_REFERRALS, 0);
	    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	    ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 5); /* 2 second timeout */
	    $r = ldap_bind($ds,"sistemas@ctgas.net","infrA@15,");
	    $sr = ldap_search($ds, "dc=ctgas,dc=net","mail=$usr");
	    $info = ldap_get_entries($ds, $sr);
	    
	    if (!$info['count']) {
	    	ldap_close($ds);
	    	return false;
	    }
	    //echo $info['count'];
	    $ldap_server = $srv;
	    $auth_user = $info[0]['dn'];
	    $auth_pass = $pwd;

	    // Tenta autenticar no servidor
	    if (!($bind = @ldap_bind($ds, $auth_user, $auth_pass))) {
	        // se nao validar retorna false
	        ldap_close($ds);
	        return false;
	    } else {
	        // se validar retorna true
	        ldap_close($ds);
	        return true;
	    }
	    ldap_close($ds);
	    return false;
	} // fim funcao conectar ldap
	
	public static function validaFiern($usr, $pwd, $srv = '10.134.25.156')
	{
	    $ds = ldap_connect($srv);
	    ldap_set_option ($ds, LDAP_OPT_REFERRALS, 0);
	    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	    ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 5); /* 2 second timeout */
	    $r = ldap_bind($ds,"sistemas@fiern.local","infrA@15,");
	    $sr = ldap_search($ds, "dc=fiern,dc=local","mail=$usr");
	    $info = ldap_get_entries($ds, $sr);
	    
	    if (!$info['count']) {
	    	ldap_close($ds);
	    	return false;
	    }
	    //echo $info['count'];
	    $ldap_server = $srv;
	    $auth_user = $info[0]['dn'];
	    $auth_pass = $pwd;

	    // Tenta autenticar no servidor
	    if (!($bind = @ldap_bind($ds, $auth_user, $auth_pass))) {
	        // se nao validar retorna false
	        ldap_close($ds);
	        return false;
	    } else {
	        // se validar retorna true
	        ldap_close($ds);
	        return true;
	    }
	    ldap_close($ds);
	    return false;
	} // fim funcao conectar ldap   
	
	public static function getUser($mail){
	    $ds = ldap_connect('10.134.25.156');
	    ldap_set_option ($ds, LDAP_OPT_REFERRALS, 0);
	    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	    $r = ldap_bind($ds, 'sistemas@fiern.local', 'infrA@15,');
		
		$ldap_base_dn = 'dc=fiern,dc=local';
		$search_filter = 'mail='.$mail;
		$attributes = array();
		$attributes[] = 'name';
		$attributes[] = 'mail';
		$attributes[] = 'cpf';
		$attributes[] = 'useraccountcontrol';

		$result = ldap_search($ds, $ldap_base_dn, $search_filter, $attributes);
		$e = ldap_get_entries($ds, $result);
		
        $user = Array();
		$user['nome'] = $e[0]['name'][0];
		$user['email'] = $e[0]['mail'][0];
		$user['cpf'] = $e[0]['cpf'][0];

        if ($e[0]["useraccountcontrol"][0] & 2) {
			return false;
	    } else {
			return $user;
	    }
	}
	
	private static function getUsers(){
	    $ds = ldap_connect('10.134.25.156');
	    ldap_set_option ($ds, LDAP_OPT_REFERRALS, 0);
	    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	    $r = ldap_bind($ds, 'sistemas@fiern.local', 'infrA@15,');
		
		$ldap_base_dn = 'dc=fiern,dc=local';
		$search_filter = '(&(objectCategory=person)(mail=*)(objectClass=user))';
		$attributes = array();
		$attributes[] = 'name';
		$attributes[] = 'mail';
		$attributes[] = 'cpf';
		$attributes[] = 'useraccountcontrol';

		$result = ldap_search($ds, $ldap_base_dn, $search_filter, $attributes, 0, 0);
		$entries = ldap_get_entries($ds, $result);


		
		#2 Arrays sepaarados, ativos e inativos
		$ativos = Array();
		$inativos = Array();
		foreach ($entries as $key => $e) {
			$user = Array();
			$user['nome'] = $e['name'][0];
			$user['email'] = $e['mail'][0];
			$user['cpf'] = $e['cpf'][0];
    
		    if ($e["useraccountcontrol"][0] & 2) {
				array_push($inativos, $user);
		    } else {
				array_push($ativos, $user);
		    }
		}
		
		return array($ativos, $inativos);
	}
	
	public static function getAtivos(){
	    if(!self::$ativos){
	        list(self::$ativos, self::$inativos) = self::getUsers();
	    }
	    return self::$ativos;
	}
	
	public static function getInativos(){
	    if(!self::$inativos){
	        list(self::$ativos, self::$inativos) = self::getUsers();
	    }
	    return self::$inativos;
	}
}