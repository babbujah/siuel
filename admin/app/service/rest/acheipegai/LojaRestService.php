<?php
/**
 * Loja REST service
 */
class LojaRestService extends AdiantiRecordService
{
    const DATABASE      = 'acheipegai';
    const ACTIVE_RECORD = 'Loja';
    const ATTRIBUTES    = ['id', 'nome', 'link_afiliado', 'logo'];

	    
    /**
     * Delete an Active Record object from the database
     * @param [$id]     HTTP parameter
     */
    public function delete($param)
    {
        throw new Exception('Falha ao executar');
    }
    
    /**
     * Store the objects into the database
     * @param $param HTTP parameter
     */
    public function store($param)
    {
        throw new Exception('Falha ao executar');
    }
    
    /**
     * Delete the Active Records by the filter
     * @return The result of operation
     * @param $param HTTP parameter
     */
    public function deleteAll($param)
    {
        throw new Exception('Falha ao executar');
    }

    /**
     * Find the count Records by the filter
     * @return The Active Record list as array
     * @param $param HTTP parameter
     */
    public function countAll($param)
    {
        throw new Exception('Falha ao executar');
    }
    
    /**
     * Handle HTTP Request and dispatch
     * @param $param HTTP POST and php input vars
     */
    public function handle($param)
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        
        unset($param['class']);
        unset($param['method']);
        $param['data'] = $param;
        
        switch( $method )
        {
            case 'GET':
                if (!empty($param['id']))
                {
                    return self::load($param);
                }
                else
                {
                    return self::loadAll($param);
                }
                break;   
			
			default:
				throw new Exception('Falha ao executar');
        }
    }
}
