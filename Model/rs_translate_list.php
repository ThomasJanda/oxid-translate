<?php

namespace rs\translate\Model;

class rs_translate_list extends \OxidEsales\EshopCommunity\Core\Model\ListModel
{

    public function __construct()
    {
        parent::__construct(\rs\translate\Model\rs_translate::class);
    }

    public function getListByFileId($sFileId)
    {
        $oListObject    = $this->getBaseObject();
        $sFieldList     = $oListObject->getSelectFields();
        $sQ             = "select $sFieldList from ".$oListObject->getViewName();
        $sQ             .= " where rs_file_id='".$sFileId."'";
        $this->selectString($sQ);

        return $this;
    }
}