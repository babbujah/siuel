<?php
/**
 * ShareTemplates Active Record
 * @author  <your-name-here>
 */
class ShareTemplate extends TRecord
{
    const TABLENAME = 'share_template';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('content');
    }


}
