<?php
/*
Magento Module developed by NoStress Commerce

 NOTICE OF LICENSE

 This source file is subject to the Open Software License (OSL 3.0)
 that is bundled with this package in the file LICENSE.txt.
 It is also available through the world-wide-web at this URL:
 http://opensource.org/licenses/osl-3.0.php
 If you did of the license and are unable to
 obtain it through the world-wide-web, please send an email
 to info@nostresscommerce.cz so we can send you a copy immediately.

 @copyright Copyright (c) 2015 NoStress Commerce (http://www.nostresscommerce.cz)

*/
namespace Nostress\Koongo\Helper;

class Version extends \Nostress\Koongo\Helper\Data
{
    const HTTPS = "https://";
    const HTTP = "http://";
    const URL_DELIMITER = "/";
    const PORT_DELIMITER = ":";
    const VRE = "/[0-9]+.[0-9]+/";
    const NEW_LICENSE_URL = "license/new_url";
    const NEW_LICENSE_EE_URL = "license/new_url_ee";
    const RENEW_LICENSE_URL = "license/renew_url";
    const RENEW_LICENSE_SUFFIX = "license/renew_suffix";
    const TRIAL_CODE = "license/trial_code";
    const EDITION_CE = "CE";
    const EDITION_EE = "EE";
    const LATEST_VERSION = "latest_version";
    const VALIDITY = "validity";
    const PATH_LICENSE_CONFIG = "koongo_license/";
    const PATH_LICENSE_KEY = "general/license_key";
    const PATH_LICENSE_VALIDITY = "general/license_validity";
    const CACHE_KEY_EDITION = "edition_key";
    protected $_ts = "l41rt";
    protected $_s = 'mage547nostress123';
    const TD = "28-02-2013";
    const STEP = 2;
    const ENDL = 7;
    const DL = 6;
    const SHF = 5;
    const DDLM = "-";
    const ASX = 2;
    const AEH = 4;
    const YR = 20;
    const TP = "tr14l";
    const LICENSE_NOT_VALID = "License invalid";
    const LICENSE_STATUS_CONTAINER = "license-status-container";
    protected $_server_name = null;
    protected $_edition = null;
    protected $_module_version = null;

    public function getLicenseConfig($key, $flag = false, $asArray = false, $storeId = null)
    {
        $path = self::PATH_LICENSE_CONFIG . $key;
        return $this->_getConfig($path, $flag, $asArray, $storeId);
    }

    public function setLicenseConfig($key, $value)
    {
        $path = self::PATH_LICENSE_CONFIG . $key;
        return $this->_setConfig($path, $value);
    }

    public function isModuleValid()
    {
        return $this->isVersionValid() && $this->isLicenseValid();
    }

    public function isVersionValid()
    {
        return true;
    }

    public function getEdition()
    {
        if (!$this->_edition) {
            $result = $this->cache->load(self::CACHE_KEY_EDITION);
            if (empty($result)) {
                $versions = $this->systemPackage->getAllowedEnterpriseVersions(0);
                $result = empty($versions) ? self::EDITION_CE : self::EDITION_EE;
                $this->cache->save($result, self::CACHE_KEY_EDITION);
            }
            $this->_edition = $result;
        }
        return $this->_edition;
    }

    public function isLicenseEmpty()
    {
        $licenseKey = $this->getLicenseKey();
        if (empty($licenseKey)) return true; else return false;
    }

    public function isLicenseValid($chackDate = false)
    {
        if ($this->isLicenseEmpty()) {
            return false;
        } else if ($chackDate && $this->isLicenseKeyValid() && $this->isDateValid()) {
            return true;
        } else if (!$chackDate && $this->isLicenseKeyValid()) {
            return true;
        } else {
            return false;
        }
    }

    public function getServerName()
    {
        if (!$this->_server_name) {
            $url = $this->storeManager->getStore($this->getDefaultStoreId())->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true);
            $serverName = str_replace(self::HTTPS, "", $url);
            $serverName = str_replace(self::HTTP, "", $serverName);
            $portDelimiterIndex = strpos($serverName, self::PORT_DELIMITER);
            $index = strpos($serverName, self::URL_DELIMITER);
            if ($index !== false && $portDelimiterIndex != false) {
                if ($portDelimiterIndex < $index) $index = $portDelimiterIndex;
            } else if ($portDelimiterIndex != false) {
                $index = $portDelimiterIndex;
            }
            if ($index !== false) $serverName = substr($serverName, 0, $index);
            $this->_server_name = $serverName;
        }
        return $this->_server_name;
    }

    public function getServerId()
    {
        $name = $this->getServerName();
        $id = trim(base64_encode($name), "=");
        return $id;
    }

    public function isDateValid()
    {
        $date = $this->gLD();
        $ld = $this->_datetimeFormat->getDateTime($date);
        $now = $this->_datetimeFormat->getDateTime("today");
        if ($ld >= $now) return true; else return false;
    }

    public function isLicenseKeyT()
    {
        $storeKey = $this->getLicenseKey();
        $genKey = $this->generateLicenseKey(true);
        if (($bb = $this->cmpKeyz($genKey, $storeKey))) return $bb;
        return false;
    }

    public function getLicenseKeyStatusHtml($addContainer = true)
    {
        $message = "";
        if (!$this->getLicenseKey()) {
            $message = $this->getLicenseHtml("Buy New License", __("Please enter the License key"));
        } else if (!$this->isLicenseKeyValid()) {
            $message .= __("Not Valid");
            if ($this->isLicenseKeyT()) $message .= " - " . __("Trial period expired on %1.", $this->gLD()) . " ";
            $message = $this->getLicenseHtml("Buy New License", $message);
        } else if (!$this->isDateValid()) {
            $message .= __("Module Support & Updates period expired on %1 ", $this->gLD());
            $renwLicenseUrl = $this->getRenewLicenseUrl($this->getLicenseKey());
            $message = $this->getLicenseHtml("Renew Support & Updates", $message, $renwLicenseUrl);
        }
        if (empty($message)) {
            $message = __("OK") . " - " . __("Module Support & Updates period expires on %1 ", $this->gLD());
            $message = "<span style='color:green;font-weight:bold'>" . $message . "</span>";
        }
        if ($addContainer) {
            $message = "<span id='" . self::LICENSE_STATUS_CONTAINER . "'>$message</span>";
        }
        return $message;
    }

    protected function getLicenseHtml($label, $message, $licenseUrl = null, $params = "")
    {
        if ($licenseUrl === null) {
            $licenseUrl = $this->getNewLicenseUrl();
        }
        $message .= " - <a target=\"_blank\" href=\"" . $licenseUrl . $params . "\">" . __($label) . "</a>";
        $message = "<span style='color:red;font-weight:bold'>" . $message . "</span>";
        return $message;
    }

    public function getNewLicenseUrl()
    {
        $key = $this->getEdition() == self::EDITION_EE ? self::NEW_LICENSE_EE_URL : self::NEW_LICENSE_URL;
        return $this->getModuleConfig($key);
    }

    public function processLicenseData($lincenseData)
    {
        if (isset($lincenseData[self::VALIDITY]) && $lincenseData[self::VALIDITY] != self::LICENSE_NOT_VALID) {
            $this->setLicenseConfig(self::PATH_LICENSE_VALIDITY, trim($lincenseData[self::VALIDITY]));
            return true;
        } else {
            return false;
        }
    }

    public function isLicenseKeyValid()
    {
        $storeKey = $this->getLicenseKey();
        $genKey = $this->generateLicenseKey();
        if ($a = $this->cmpKeyz($genKey, $storeKey) && ($this->isDateValid() || !$this->isDateT())) {
            return $a;
        }
        $genKey = $this->generateLicenseKey(true);
        if (($bb = $this->cmpKeyz($genKey, $storeKey)) && $this->isDateValid()) {
            return $bb;
        }
        return false;
    }

    public function isDateT()
    {
        $date = $this->gLD();
        $ld = $this->_datetimeFormat->getDateTime($date);
        $tDate = $this->_datetimeFormat->getDateTime(self::TD);
        if ($tDate >= $ld) return true; else return false;
    }

    public function gLD()
    {
        $validity = $this->getLicenseConfig(self::PATH_LICENSE_VALIDITY);
        if (empty($validity) || $this->isLicenseKeyT()) $validity = $this->dtProcess($this->getLicenseKey());
        return $validity;
    }

    public function getLicenseKey()
    {
        return $this->getLicenseConfig(self::PATH_LICENSE_KEY);
    }

    public function saveLicenseKey($key)
    {
        $this->setLicenseConfig(self::PATH_LICENSE_KEY, $key);
        $this->setLicenseConfig(self::PATH_LICENSE_VALIDITY, "");
    }

    public function resetLicenseDate()
    {
        $validity = $this->getLicenseConfig(self::PATH_LICENSE_VALIDITY);
        if (!isset($validity)) return;
        $this->setLicenseConfig(self::PATH_LICENSE_VALIDITY, "");
        $this->_coreConfig->reinit();
    }

    public function generateLicenseKey($t = false)
    {
        $serverName = $this->getServerName();
        $p = self::TP;
        $s = $this->_ts;
        if (!$t) {
            $p = $s = "";
        }
        $res = md5(sha1($p . $serverName . $this->getModuleName() . $this->_s . $s));
        return $res;
    }

    public function getModuleVersion()
    {
        if (!$this->_module_version) {
            $conf = $this->moduleList->getOne($this->_getModuleName());
            $this->_module_version = $conf["setup_version"];
        }
        return $this->_module_version;
    }

    public function getModuleName()
    {
        return $this->_getModuleName() . "_M2_" . $this->getEdition();
    }

    protected function dtProcess($input)
    {
        $date = "";
        $addCh = true;
        $inputLength = strlen($input);
        for ($i = 1; $i <= $inputLength; $i += 2) {
            $date .= $input[$i];
            if (strlen($date) >= self::ENDL) break;
        }
        $date = $date >> self::SHF;
        $date = $this->addZeros($date, self::DL);
        $date = $this->frD($date);
        return $date;
    }

    protected function frD($date)
    {
        $date = (string)$date;
        $rd = "";
        for ($i = 0; $i < self::DL; $i++) {
            $rd .= $date[$i];
            if (!$i) continue;
            if (!(($i + 1) % self::ASX) && ($i + 1) != self::DL) $rd .= self::DDLM;
            if (!(($i + 1) % self::AEH)) $rd .= self::YR;
        }
        return $rd;
    }

    protected function addZeros($date, $length)
    {
        while (strlen($date) != $length) $date = "0" . $date;
        return $date;
    }

    protected function cmpKeyz($key1, $key2)
    {
        if (!isset($key1) || !isset($key2) || empty($key1)) return false;
        $length = strlen($key1);
        if ($length != strlen($key2)) {
            return false;
        }
        for ($i = 0; $i < $length; $i += self::STEP) {
            if ($key1[$i] != $key2[$i]) return false;
        }
        return true;
    }

    public function getLicenseInvalidMessage()
    {
        $licenseKey = $this->getLicenseKey();
        $date = $this->gLD();
        $url = $this->getRenewLicenseUrl($licenseKey);
        $dateString = "<span style='color:red;font-weight:bold'>" . $date . "</span>";
        $message = __("Your Support & Updates period has expired on %1. You can renew Support & Updates by following ", $dateString);
        $message .= "<a target=\"_blank\" href=\"" . $url . "\">" . __("this link") . ".</a>";
        return $message;
    }

    public function getTrialLicenseInvalidMessage()
    {
        $message = __("Trial License Not Valid");
        $message .= " - " . __("Trial period expired on %1.", $this->gLD()) . " ";
        $message = $this->getLicenseHtml("Buy New License", $message);
        return $message;
    }

    public function getTrialLicenseKeyInvalidMessage()
    {
        $message = __("License Not Valid");
        $message = $this->getLicenseHtml("Buy New License", $message);
        return $message;
    }

    public function getActivationMessage()
    {
        $message = __("Please activate module with trial or live license!");
        $url = $this->getHelp("activation");
        if ($url) {
            $message .= " <a href='$url' target='_blank'>How to activate?</a>";
        }
        return $message;
    }

    protected function getRenewLicenseUrl($licenseKey)
    {
        $prefix = $this->getModuleConfig(self::RENEW_LICENSE_URL);
        $suffix = $this->getModuleConfig(self::RENEW_LICENSE_SUFFIX);
        $url = $prefix . str_replace("{{license_key}}", $licenseKey, $suffix);
        return $url;
    }
}

?>