<?php /*
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
namespace Nostress\Koongo\Model\Api\Client;

class Simple extends \Nostress\Koongo\Model\AbstractModel
{
    const RESPONSE_FEED = "feed";
    const RESPONSE_TAXONOMY = "taxonomy";
    const RESPONSE_ERROR = "error";
    const RESPONSE_ERRORS = "errors";
    const RESPONSE_INFO = "info";
    const RESPONSE_MODULE = "module";
    const RESPONSE_LICENSE = "license";
    const RESPONSE_VALIDITY = "validity";
    const RESPONSE_KEY = "key";
    const RESPONSE_COLLECTION = "collection";
    const RESPONSE_FEEDS_NEW = "feeds_new";
    const RESPONSE_FEEDS_UPDATE = "feeds_update";
    const PARAM_LICENSE = "license";
    const PARAM_SERVER = "server";
    const PARAM_SERVER_ID = "server_id";
    const PARAM_SIGN = "sign";
    const PARAM_LINK = "link";
    const PARAM_FILE_TYPE = "file_type";
    const PARAM_PLUGINS = "plugins";
    const PARAM_REQUEST_TYPE = "request_type";
    const PARAM_MODULE_NAME = "module_name";
    const TYPE_LICENSE = "license";
    const TYPE_FEEDS_INFO = "feedsInfo";
    const PARAM_API_URL_SECURE = "http://www.koongo.com/api/2.0/koongo/";
    const PARAM_API_URL_UNSECURE = "http://www.koongo.com/api/2.0/koongo/";
    const PARAM_SERVER_CONFIG_JSON_URL = "http://www.koongo.com/media/ChannelCache/ServerConfig.json";
    const PARAM_COLLECTIONS_JSON_URL = "api/collections_json_url";
    const PARAM_FEEDS_JSON_URL = "api/country_feed_json_url";
    const PARAM_UNIVERSITY_JSON_URL = "api/university_json_url";
    const PARAM_VERSION_PLUGIN_JSON_URL = "api/version_plugins_json_url";
    const PARAM_TAXONOMY_SOURCE_URL = "api/taxonomy_source_url";
    const CACHE_KEY_AVAILABLE_COLLECTIONS = "koongo_available_collections";
    const CACHE_KEY_AVAILABLE_FEEDS = "koongo_available_feeds";
    const CACHE_KEY_VERSION_PLUGINS_INFO = "koongo_version_plugins_info";
    const LICENSE_NOT_VALID = "License invalid";
    public $helper;
    protected $cache;
    protected $reader;

    public function __construct(\Nostress\Koongo\Helper\Version $versionHelper, \Magento\Framework\App\CacheInterface $cache, \Nostress\Koongo\Model\Data\Reader $reader)
    {
        $this->helper = $versionHelper;
        $this->cache = $cache;
        $this->reader = $reader;
    }

    public function getHelper()
    {
        return $this->helper;
    }

    public function getAvailableCollections()
    {
        return $this->getInfoData(self::PARAM_COLLECTIONS_JSON_URL, self::CACHE_KEY_AVAILABLE_COLLECTIONS);
    }

    public function getAvailableFeeds()
    {
        return $this->getInfoData(self::PARAM_FEEDS_JSON_URL, self::CACHE_KEY_AVAILABLE_FEEDS);
    }

    public function getUniversityInfo()
    {
        return $this->_getInfoData(self::PARAM_UNIVERSITY_JSON_URL, true);
    }

    public function getAvailableCollectionsAsOptionArray($isMultiselect = false)
    {
        $collections = $this->getAvailableCollections();
        $result = array();
        if (empty($collections) || !is_array($collections)) return $result;
        foreach ($collections as $item) {
            $result[$item["address"]] = array("label" => $item["address"], "value" => $item["code"]);
        }
        sort($result);
        if (!$isMultiselect) array_unshift($result, array("label" => __("-- Please Select --"), "value" => ""));
        return $result;
    }

    public function getAvailableFeedsAsOptionArray($collection = null)
    {
        $feeds = $this->getAvailableFeeds();
        $result = array();
        if (empty($feeds) || !is_array($feeds)) return $result;
        foreach ($feeds as $country => $cFeeds) {
            $result[$country] = [["label" => __("-- Please Select --"), "value" => ""]];
            foreach ($cFeeds as $cfeed) {
                $result[$country][] = array("label" => $cfeed["link"], "value" => $cfeed["link"]);
            }
        }
        if ($collection) {
            return $result[$collection];
        } else {
            return $result;
        }
    }

    public function getAvailableFeedsJson()
    {
        return json_encode($this->getAvailableFeedsAsOptionArray());
    }

    public function updateServerConfig($enableErrorIncrement = true)
    {
        try {
            if ($enableErrorIncrement && !$this->helper->isServerConfigUpdatable()) {
                $this->helper->incrementServerConfigError();
                return __("Server config update is not working properly! Try to update it by button above or contact support.");
            }
            $data = $this->reader->getRemoteFileContent(self::PARAM_SERVER_CONFIG_JSON_URL);
            $config = $this->decodeResponse($data);
            if (is_array($config) && count($config)) {
                $this->helper->saveModuleConfigs($config);
            } else {
                throw new\Exception("Wrong server config data!");
            }
            $this->helper->clearServerConfigError();
            return true;
        } catch (\Exception$e) {
            $blhjtulrt = "enableErrorIncrement";
            if (${$blhjtulrt}) {
                $this->helper->incrementServerConfigError();
            } else {
                $this->helper->clearServerConfigError();
            }
            $this->log($e->getMessage());
            return $e->getMessage();
        }
    }

    protected function sendServerRequest($resource, $licensed = true)
    {
        $server = $this->getServerName();
        $license = $this->getLicenseKey();
        if (empty($license) && !$licensed) {
            $license = "temp";
        }
        $sign = $this->getSign($server, $license);
        $params = array();
        $params[self::PARAM_SIGN] = $sign;
        $params[self::PARAM_LICENSE] = $license;
        $params[self::PARAM_SERVER] = $server;
        $params[self::PARAM_MODULE_NAME] = $this->helper->getModuleName();
        return $this->postApiRequest($resource, $params);
    }

    protected function postApiRequest($apiFunction, $params = array())
    {
        try {
            $response = $this->postJsonUrlRequest($this->getKoongoApiUrl() . $apiFunction, $params);
        } catch (\Exception$e) {
            $response = $this->postJsonUrlRequest($this->getKoongoApiUrl(false) . $apiFunction, $params);
        }
        return $response;
    }

    protected function processResponse($response)
    {
        $response = $this->decodeResponse($response);
        $this->checkResponseContent($response);
        return $response;
    }

    protected function updateConfig($feedConfig, $taxonomyConfig)
    {
        if (empty($feedConfig)) throw new\Exception($this->__("Feeds configuration empty"));
        $this->feedManager->updateFeeds($feedConfig);
        $this->taxonomySetupManager->updateTaxonomySetup($taxonomyConfig);
        $this->profileManager->updateProfilesFeedConfig();
        return $this->getLinks($feedConfig);
    }

    protected function updateInfo($info)
    {
        if (empty($info)) return;
        $pluginInfo = array();
        $moduleInfo = array();
        if (isset($info[self::RESPONSE_MODULE])) $moduleInfo = $info[self::RESPONSE_MODULE];
        $this->helper->processModuleInfo($moduleInfo);
        return;
    }

    protected function checkResponseContent($response)
    {
        $error = "";
        if (!empty($response[self::RESPONSE_ERROR])) {
            throw new\Exception($response[self::RESPONSE_ERROR]);
        } else if (!empty($response[self::RESPONSE_ERRORS])) {
            throw new\Exception(implode(", ", $response[self::RESPONSE_ERRORS]));
        }
    }

    protected $_xdfsdfskltyllk = "du45itg6df4kguyk";

    protected function checkResponseEmpty($response, $error)
    {
        if (!isset($response) || empty($response)) {
            throw new\Exception(__("Invalid or empty server response.") . __("Curl error: ") . $error);
        }
    }

    protected function getLinks($feedConfig)
    {
        $links = array();
        foreach ($feedConfig as $config) {
            if (isset($config[self::PARAM_LINK]) && !in_array($config[self::PARAM_LINK], $links)) $links[] = $config[self::PARAM_LINK];
        }
        return $links;
    }

    protected $_xdfsdfskmfowlt54b4 = "kd6fg54";

    protected function decodeResponse($response)
    {
        $response = json_decode($response, true);
        return $response;
    }

    protected function getServerName()
    {
        return $this->helper->getServerName();
    }

    protected function getLicenseKey()
    {
        return $this->helper->getLicenseKey();
    }

    protected function getServerId()
    {
        return $this->helper->getServerId();
    }

    protected function checkLicense()
    {
        if (!$this->helper->isLicenseValid()) {
            throw new\Exception(__("Your License is not valid"));
        }
    }

    protected function getSign($server, $license)
    {
        return md5(sha1($this->_xdfsdfskltyllk . $server . $license . "\$this->_xdfsdfskmfowlt54b4"));
    }

    protected function getKoongoApiUrl($secured = true)
    {
        if ($secured) return self::PARAM_API_URL_SECURE; else return self::PARAM_API_URL_UNSECURE;
    }

    protected function postUrlRequest($request_url, $post_params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
        $result = curl_exec($ch);
        $this->checkResponseEmpty($result, curl_error($ch));
        curl_close($ch);
        return $result;
    }

    protected function postJsonUrlRequest($request_url, $post_params)
    {
        $post_params = json_encode($post_params);
        return $this->postUrlRequest($request_url, $post_params);
    }

    protected function getInfoData($type, $cacheKey)
    {
        $json = $this->cache->load($cacheKey);
        if (empty($json)) {
            $json = $this->_getInfoData($type);
            if (!empty($json)) $this->cache->save($json, $cacheKey);
        }
        $data = $this->decodeResponse($json);
        return $data;
    }

    protected function _getInfoData($type, $decode = false)
    {
        try {
            $url = $this->helper->getModuleConfig($type);
            $data = $this->reader->getRemoteFileContent($url);
            if ($decode) $data = $this->decodeResponse($data);
        } catch (\Exception$e) {
            $this->log($e->getMessage());
            $data = array();
        }
        return $data;
    }
}

?>