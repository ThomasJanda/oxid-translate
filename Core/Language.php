<?php

namespace rs\translate\Core;

class Language extends Language_parent
{

    public function rs_translate__getLangFilesContent($id)
    {
        $aLangPaths = $this->rs_translate__getLangFilesPathArray();
        $aLangPaths = $aLangPaths[$id];

        $oConfig   = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sBasePath    = $oConfig->getConfigParam('sShopDir');

        $aLangContent = [];
        foreach($aLangPaths as $iLangId => $sLangPath)
        {
            $sPath = $sBasePath.$sLangPath;
            require $sPath;

            $aLangContent[$iLangId] = $aLang;
        }

        $aResult = [];
        foreach($aLangContent as $iLangId => $aLang)
        {
            $sCharSet = 'UTF-8';
            if(isset($aLang['charset']))
                $sCharSet = $aLang['charset'];

            foreach($aLang as $sLangKey=>$sLangValue)
            {
                if($sLangKey!="charset")
                    $aResult[$sLangKey][$iLangId]=$sLangValue;
            }
        }

        ksort($aResult);

        //normalize
        //shop lang
        $aLangs = $this->getLanguageArray();
        foreach ($aLangs as $oLang) {
            $iLangId = $oLang->id;

            foreach($aResult as $sLangKey => $aLangValues)
            {
                if(!isset($aLangValues[$iLangId]))
                    $aResult[$sLangKey][$iLangId]="";
            }
        }

        return $aResult;
    }

    protected function _rs_translate__createFileId($sPath, $sLangAbbr)
    {
        $oConfig   = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sBasePath = $oConfig->getConfigParam('sShopDir');
        
        $id = substr($sPath, strlen($sBasePath));
        $id = str_replace($sLangAbbr, "XXX", $id);
        $id = md5($id);
        
        return $id;
    }

    public function rs_translate__getLangFilesPathArray()
    {
        $oConfig   = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sBasePath = $oConfig->getConfigParam('sShopDir');

        $aLangs = $this->getLanguageArray();

        //search for all present files
        $aLang = [];
        foreach ($aLangs as $oLang) {
            $iLangId   = $oLang->id;
            $sLangAbbr = $oLang->abbr;

            //get path to all language files
            $aPaths = $this->_getLangFilesPathArray($iLangId);
            foreach ($aPaths as $sPath) {
                if (file_exists($sPath)) {

                    $sValue = substr($sPath, strlen($sBasePath));
                    $id = $this->_rs_translate__createFileId($sPath, $sLangAbbr);

                    if(!isset($aLang[$id]))
                    {
                        $aLang[$id] = [];
                    }
                        
                    $aLang[$id][$iLangId] = $sValue;
                }
            }
        }

        return $aLang;
    }
    
    
    protected $_rs_translate_add=false;
    
    protected function _getLanguageFileData($blAdmin = false, $iLang = 0, $aLangFiles = null)
    {
        
        if(!$blAdmin)
        {
            //test if cache have to recreate
            $myUtils    = \OxidEsales\Eshop\Core\Registry::getUtils();
            $sCacheName = $this->_getLangFileCacheName($blAdmin, $iLang, $aLangFiles);
            $aLangCache = $myUtils->getLangCache($sCacheName);
            
            if(!$aLangCache)
            {
                $this->_rs_translate_add = true;
            }
        }
        
        $aLangCache = parent::_getLanguageFileData($blAdmin, $iLang, $aLangFiles);

        if($this->_rs_translate_add)
        {
            //add translate
            $aPaths = $this->_getLangFilesPathArray($iLang);
            $sLangabbr = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageAbbr($iLang);

            foreach ($aPaths as $sPath) {
                if (file_exists($sPath)) {
                    $id = $this->_rs_translate__createFileId($sPath, $sLangabbr);
                    
                    $o    = oxNew(\rs\translate\Model\rs_translate_list::class);
                    $aLangSaved = $o->getListByFileId($id);
                    
                    $aLang=[];
                    foreach($aLangSaved as $oSaved)
                    {
                        if($oSaved->rs_translate__rs_lang_id->getRawValue() == $iLang)
                        {
                            $aLang[$oSaved->rs_translate__rs_lang_key->getRawValue()] = $oSaved->rs_translate__rs_lang_value->getRawValue();
                        }
                    }
                    
                    if(!empty($aLang))
                        $aLangCache = array_merge($aLangCache, $aLang);
                }
            }
            
            $sCacheName = $this->_getLangFileCacheName($blAdmin, $iLang, $aLangFiles);
            $myUtils->setLangCache($sCacheName, $aLangCache);
        }
        
        return $aLangCache;
    }
}