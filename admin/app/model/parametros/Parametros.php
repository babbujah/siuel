<?php
/**
 * Parametros Active Record
 * @author  <your-name-here>
 */
class Parametros extends TRecord
{
    const TABLENAME = 'parametros';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('title');
        parent::addAttribute('description');
        parent::addAttribute('author');
        parent::addAttribute('social_whatsapp');
        parent::addAttribute('social_facebook');
        parent::addAttribute('social_instagram');
        parent::addAttribute('social_email');
        parent::addAttribute('social_telegram');
        parent::addAttribute('keywords');
    }


}
