<?php
/**
 * Created by PhpStorm.
 * User: lazar
 * Date: 22.12.14
 * Time: 19:02
 */

require_once 'yawApi.class.php';

$cacheCfg = [
    'host'  =>  'localhost',
    'db'    =>  'weather',
    'user'  =>  'root',
    'pass'  =>  'root',
    'table' =>  'yaw_cache',

    'weather_ttl'   =>  240,
    'cities_ttl'    =>  86400
];

$weather = new YandexWeatherApi(yandexWeatherCache::CACHE_TYPE_FS, $cacheCfg);

$countries = $weather->getCountriesList();
$citiesList = $weather->getCitiesList('Австралия');

$weather->getCityIdByName('Харьков');
$weather->getCityIdByName('Одесса');
$weather->getCityIdByName('Львов');
$weather->getCityIdByName('Чернигов');

$kievId = $weather->getCityIdByName('Киев');
echo $weather->getWeather($kievId);