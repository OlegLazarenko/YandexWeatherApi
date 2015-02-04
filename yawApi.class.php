<?php
/**
 * Created by PhpStorm.
 * User: lazar
 * Date: 22.12.14
 * Time: 19:04
 */

require_once 'yawData.class.php';
require_once 'yawXMLParser.class.php';
require_once 'yawCache.class.php';

class YandexWeatherApi
{
    const CITIES_LIST_URL = 'https://pogoda.yandex.ru/static/cities.xml';
    const CITY_URL_TPL = 'http://export.yandex.ru/weather-ng/forecasts/%s.xml';

    private $cacheTTLWeather = 240; // 4m
    private $cacheTTLCities = 86400; // 24h

    /**
     * @var yandexWeatherCache
     */
    private $cache;

    public function __construct($cacheType = yandexWeatherCache::CACHE_TYPE_FS, $cacheParam)
    {
        $this->cache = new yandexWeatherCache($cacheType, $cacheParam);
        $this->cacheTTLCities = $cacheParam['cities_ttl'];
        $this->cacheTTLWeather = $cacheParam['weather_ttl'];
    }

    public function __destruct()
    {
        $this->cache = null;
    }

    public function getCountriesList()
    {
        $cache = $this->cache->getCache("countries_list", $this->cacheTTLCities);
        if ($cache !== false)
        {
            return $cache;
        }

        $xml = new DOMDocument();
        $xml->load(self::CITIES_LIST_URL);

        $xpath = new DOMXPath($xml);
        $domCountries = $xpath->query('/cities/country');

        foreach ($domCountries as $countryNode)
            $countries[] = $countryNode->getAttribute('name');

        $this->cache->setCache("countries_list", $countries);

        return $countries;
    }

    public function getCitiesList($country = '')
    {
        $cache = $this->cache->getCache("cities_list_$country", $this->cacheTTLCities);
        if ($cache !== false)
        {
            return $cache;
        }

        $xml = new DOMDocument();
        $xml->load(self::CITIES_LIST_URL);

        $xpath = new DOMXPath($xml);
        $domCities = $xpath->query("/cities/country[@name='$country']/city");

        foreach ($domCities as $cityNode)
            $cities[] = $cityNode->nodeValue;

        $this->cache->setCache("cities_list_$country", $cities);

        return $cities;
    }

    public function getCityIdByName($name)
    {
        $cache = $this->cache->getCache("city_id_list", $this->cacheTTLCities);
        if ($cache !== false && isset($cache[$name]))
        {
            return $cache[$name];
        }

        $xml = new DOMDocument();
        $xml->load(self::CITIES_LIST_URL);

        $xpath = new DOMXPath($xml);
        $domIds = $xpath->query("/cities/country/city[.='$name']/@id");

        $cache[$name] = $domIds->item(0)->nodeValue;
        $this->cache->setCache("city_id_list", $cache);

        return $domIds->item(0)->nodeValue;
    }

    public function getWeather($cityId)
    {
        $cache = $this->cache->getCache("city$cityId", $this->cacheTTLWeather);
        if ($cache !== false)
        {
            return $cache;
        }

        $cityXml = simplexml_load_file($this->getCityXmlUrl($cityId));

        $weather = yandexWeatherXMLParser::parsWeather($cityXml);
        $weather->fact = yandexWeatherXMLParser::parsWeatherFact($cityXml->fact);
        $weather->yesterday = yandexWeatherXMLParser::parsWeatherFact($cityXml->yesterday);

        foreach ($cityXml->informer->temperature as $temperature)
        {
            $weather->informer[] = yandexWeatherXMLParser::parsWeatherInformer($temperature);
        }

        foreach ($cityXml->day as $dayNode)
        {
            $weather->days[] = yandexWeatherXMLParser::parsWeatherDay($dayNode);
        }

        $this->cache->setCache("city$cityId", $weather);

        return $weather;
    }

    protected function getCityXmlUrl($cityId)
    {
        return sprintf(self::CITY_URL_TPL, $cityId);
    }
}