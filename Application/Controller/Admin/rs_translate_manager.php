<?php

namespace rs\translate\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Request;

class rs_translate_manager extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{

    protected $_iStep=0;
    protected $_sId = "";

    public function render()
    {
        $this->addTplParam('step', $this->_iStep);
        parent::render();
        return "rs_translate_manager.tpl";
    }

    public function getLanguageFilePaths()
    {
        /**
         * @var rs\translate\Core\Language $oLang
         */
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $aPath = $oLang->rs_translate__getLangFilesPathArray();
        return $aPath;
    }

    public function displayfile()
    {
        $oRequest = oxNew(Request::class);
        $id = $oRequest->getRequestParameter("id");
        if($id!="")
        {
            $this->_sId = $id;
            $this->_iStep=1;
        }
    }

    public function getLanguageData()
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        return $oLang->getLanguageArray();
    }

    public function getLanguageFileContent()
    {
        $aRet = [];
        $id = $this->_sId;
        if ($id != "") {
            $oLang      = \OxidEsales\Eshop\Core\Registry::getLang();
            $aRet = $oLang->rs_translate__getLangFilesContent($id);
        }

        return $aRet;
    }

    public function getLanguageFileContentSaved()
    {
        $o = oxNew(\rs\translate\Model\rs_translate_list::class);
        $aRet = null;
        $id = $this->_sId;
        if ($id != "") {
            $aRet = $o->getListByFileId($id);
        }
        return $aRet;
    }

    public function getFileId()
    {
        return $this->_sId;
    }

    public function saveValue()
    {
        $oRequest = oxNew(Request::class);
        $sLangKey = $oRequest->getRequestParameter("langkey");
        $sFileId = $oRequest->getRequestParameter("fileid");
        $aLangValues = $oRequest->getRequestParameter("langvalue");

        $this->_sId = $sFileId;
        $this->_iStep=1;

        foreach($aLangValues as $iLangId=>$sLangValue)
        {
            $sOxid = md5($sLangKey."|".$sFileId."|".$iLangId);
        
            $aData = [];
            $aData['oxid']=$sOxid;
            $aData['rs_file_id']=$sFileId;
            $aData['rs_lang_id']=$iLangId;
            $aData['rs_lang_key']= $sLangKey;
            $aData['rs_lang_value']= $sLangValue;

            $oTranslate = oxNew(\rs\translate\Model\rs_translate::class);
            $oTranslate->load($sOxid);
            $oTranslate->assign($aData);
            $oTranslate->save();
        }

        http_response_code(200);
        die("");
    }

    public function deleteValue()
    {
        $oRequest    = oxNew(Request::class);
        $sLangKey    = $oRequest->getRequestParameter("langkey");
        $sFileId     = $oRequest->getRequestParameter("fileid");

        $this->_sId   = $sFileId;
        $this->_iStep = 1;

        $oDb  = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sSql="delete from rs_translate where rs_file_id='$sFileId' and rs_lang_key='$sLangKey'";
        $oDb->execute($sSql);

        http_response_code(200);
        die("");
    }
}
