<?php

namespace rs\translate\Model;

class rs_translate extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    protected $_sClassName = 'rs_translate';


    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('rs_translate');
    }
}