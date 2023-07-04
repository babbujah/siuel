<?php
/**
 * Parametros REST service
 */
class ParametrosRestService extends AdiantiRecordService
{
    const DATABASE      = 'acheipegai';
    const ACTIVE_RECORD = 'Parametros';
    
    /**
     * Find a Active Record and returns it
     * @return The Active Record itself as array
     * @param $param HTTP parameter
     */
    public function load($param = [])
    {
        $database     = static::DATABASE;
        $activeRecord = static::ACTIVE_RECORD;
        
        TTransaction::open($database);
        
        $object = $activeRecord::where('id', 'IS NOT', NULL)->first(); // instantiates the Active Record
            
        if( !$object instanceof Parametros ){
            $object = new Parametros;
        }
        
        TTransaction::close();
        $attributes = defined('static::ATTRIBUTES') ? static::ATTRIBUTES : null;
        $object_array = $object->toArray( $attributes );
        
        return $object_array;
    }
    
    /**
     * List the Active Records by the filter
     * @return The Active Record list as array
     * @param $param HTTP parameter
     */
    public function loadAll($param = [])
    {
        return $this->load();
    }
    
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
    public function handle($param = [])
    {
        return $this->load();
    }
}
