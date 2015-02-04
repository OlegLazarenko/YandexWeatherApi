<?php
/**
 * Created by PhpStorm.
 * User: lazar
 * Date: 24.12.14
 * Time: 11:50
 */

class YandexWeatherData {
    public $country_id;
    public $part;
    public $link;
    public $part_id;
    public $lat;
    public $slug;
    public $city;
    public $climate;
    public $country;
    public $region;
    public $lon;
    public $zoom;
    public $id;
    public $source;
    public $exactname;
    public $geoid;

    /**
     * @var WeatherFact
     */
    public $fact;

    /**
     * @var WeatherFact
     */
    public $yesterday;

    /**
     * @var WeatherInformer[]
     */
    public $informer;

    /**
     * @var WeatherDay[]
     */
    public $days;

    public function __tostring()
    {
        $res = meta::toString($this->city, __CLASS__, $this, 0);

        $res .= $this->fact;
        $res .= $this->yesterday;

        $res .= implode('',$this->informer);
        $res .= implode('',$this->days);

        return $res;
    }
} 

class WeatherFact
{
    public $station_ru;
    public $station_en;
    public $station_distance;

    public $observation_time;
    public $uptime;

    public $temperature;
    public $temperature_color;
    public $temperature_plate;

    public $weather_condition_code;

    public $image;
    public $image_type;
    public $image_v2;
    public $image_v2_color;
    public $image_v2_type;
    public $image_v3;
    public $image_v3_type;

    public $weather_type_ru;
    public $weather_type_short_ru;
    public $weather_type_tt;
    public $weather_type_short_tt;
    public $weather_type_tr;
    public $weather_type_short_tr;
    public $weather_type_kz;
    public $weather_type_short_kz;
    public $weather_type_ua;
    public $weather_type_short_ua;
    public $weather_type_by;
    public $weather_type_short_by;

    public $wind_direction;
    public $wind_speed;

    public $humidity;

    public $pressure;
    public $pressure_units;
    public $mslp_pressure;
    public $mslp_pressure_units;

    public $daytime;

    public $season;
    public $season_type;

    public $ipad_image;

    public function __tostring()
    {
        return meta::toString('fact', __CLASS__, $this, 1);
    }
}

class WeatherInformer
{
    public $temperature;
    public $color;
    public $type;

    public function __tostring()
    {
        return meta::toString('informer', __CLASS__, $this, 1);
    }
}

class WeatherDay
{
    public $date;

    public $sunrise;
    public $sunset;
    public $moon_phase;
    public $moon_phase_code;
    public $moonrise;
    public $moonset;

    public $biomet_index;
    public $biomet_geomag;
    public $biomet_low_press;
    public $biomet_message_codes = array();

    /**
     * @var WeatherDayPart[]
     */
    public $parts;

    /**
     * @var WeatherHour
     */
    public $hours;

    public function __tostring()
    {
        $res = meta::toString($this->date, __CLASS__, $this, 1);

        $res .= implode('', $this->parts);

        if (isset($this->hours))
        {
            $res .= implode('', $this->hours);
        }

        return $res;
    }
}

class WeatherDayPart
{
    public $typeid;
    public $type;

    public $temperature;
    public $temperature_from;
    public $temperature_to;
    public $temperature_data_avg;
    public $temperature_data_avg_bgcolor;
    public $temperature_data_from;
    public $temperature_data_to;

    public $weather_condition_code;

    public $image;
    public $image_type;
    public $image_v2;
    public $image_v2_color;
    public $image_v2_type;
    public $image_v3;
    public $image_v3_type;

    public $weather_type_ru;
    public $weather_type_short_ru;
    public $weather_type_tt;
    public $weather_type_short_tt;
    public $weather_type_tr;
    public $weather_type_short_tr;
    public $weather_type_kz;
    public $weather_type_short_kz;
    public $weather_type_ua;
    public $weather_type_short_ua;
    public $weather_type_by;
    public $weather_type_short_by;

    public $wind_direction;
    public $wind_speed;

    public $humidity;

    public $pressure;
    public $pressure_units;
    public $mslp_pressure;
    public $mslp_pressure_units;

    public function __tostring()
    {
        return meta::toString($this->type, __CLASS__, $this, 2);
    }
}

class WeatherHour
{
    public $at;
    public $temperature;
    public $weather_condition_code;

    public $image;
    public $image_type;
    public $image_v2;
    public $image_v2_color;
    public $image_v2_type;
    public $image_v3;
    public $image_v3_type;

    public function __tostring()
    {
        return meta::toString($this->at, __CLASS__, $this, 2);
    }
}

class meta
{
    static function toString($headerText, $className, $obj, $level = 0)
    {
        $class = new ReflectionClass($className);
        $properties = $class->getProperties();

        $tabs = str_repeat("\t", $level);

        $data = '<pre>';
        $data .= "$tabs<b style='font-size: 1.1em;'>[$headerText]</b><br>";
        foreach ($properties as $property)
        {
            $value = $obj->{$property->getName()};

            if (!is_array($value))
            {
                $data .= $tabs . $property->getName() . ' = ' . $value . '<br>';
            }
        }
        $data .= '</pre>';

        return $data;
    }
}