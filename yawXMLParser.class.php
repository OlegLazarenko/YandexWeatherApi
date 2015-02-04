<?php
/**
 * Created by PhpStorm.
 * User: lazar
 * Date: 27.12.14
 * Time: 19:32
 */

require_once 'yawData.class.php';

class YandexWeatherXMLParser
{
    static function parsWeather(SimpleXMLElement $weatherNode)
    {
        $weather = new YandexWeatherData();

        $weather->country_id = (string) $weatherNode['country_id'];
        $weather->part = (string) $weatherNode['part'];
        $weather->link = (string) $weatherNode['link'];
        $weather->part_id = (string) $weatherNode['part_id'];
        $weather->lat = (string) $weatherNode['lat'];
        $weather->slug = (string) $weatherNode['slug'];
        $weather->city = (string) $weatherNode['city'];
        $weather->climate = (string) $weatherNode['climate'];
        $weather->country = (string) $weatherNode['country'];
        $weather->region = (string) $weatherNode['region'];
        $weather->lon = (string) $weatherNode['lon'];
        $weather->zoom = (string) $weatherNode['zoom'];
        $weather->id = (string) $weatherNode['id'];
        $weather->source = (string) $weatherNode['source'];
        $weather->exactname = (string) $weatherNode['exactname'];
        $weather->geoid = (string) $weatherNode['geoid'];

        return $weather;
    }

    static function parsWeatherFact(SimpleXMLElement $section)
    {
        $weather = new WeatherFact();

        if ($section->station[0]['lang'] == 'ru')
        {
            $weather->station_ru = (string) $section->station[0];
            $weather->station_en = (string) $section->station[1];
        }
        else
        {
            $weather->station_en = (string) $section->station[0];
            $weather->station_ru = (string) $section->station[1];
        }

        $weather->station_distance = (string) $section->station[0]['distance'];

        $weather->observation_time = (string) $section->observation_time;
        $weather->uptime = (string) $section->uptime;

        $weather->temperature = (string) $section->temperature;
        $weather->temperature_color = (string) $section->temperature['color'];
        $weather->temperature_plate = (string) $section->temperature['plate'];

        $weather->weather_condition_code = (string) $section->weather_condition['code'];

        $weather->image = (string) $section->image;
        $weather->image_type = (string) $section->image['type'];
        $weather->image_v2 = (string) $section->{'image-v2'};
        $weather->image_v2_color = (string) $section->{'image-v2'}['color'];
        $weather->image_v2_type = (string) $section->{'image-v2'}['type'];
        $weather->image_v3 = (string) $section->{'image-v3'};
        $weather->image_v3_type = (string) $section->{'image-v3'}['type'];

        $weather->weather_type_ru = (string) $section->weather_type;
        $weather->weather_type_short_ru = (string) $section->weather_type_short;
        $weather->weather_type_tt = (string) $section->weather_type_tt;
        $weather->weather_type_short_tt = (string) $section->weather_type_short_tt;
        $weather->weather_type_tr = (string) $section->weather_type_tr;
        $weather->weather_type_short_tr = (string) $section->weather_type_short_tr;
        $weather->weather_type_kz = (string) $section->weather_type_kz;
        $weather->weather_type_short_kz = (string) $section->weather_type_short_kz;
        $weather->weather_type_ua = (string) $section->weather_type_ua;
        $weather->weather_type_short_ua = (string) $section->weather_type_short_ua;
        $weather->weather_type_by = (string) $section->weather_type_by;
        $weather->weather_type_short_by = (string) $section->weather_type_short_by;

        $weather->wind_direction = (string) $section->wind_direction;
        $weather->wind_speed = (string) $section->wind_speed;

        $weather->humidity = (string) $section->humidity;

        $weather->pressure = (string) $section->pressure;
        $weather->pressure_units = (string) $section->pressure['units'];
        $weather->mslp_pressure = (string) $section->mslp_pressure;
        $weather->mslp_pressure_units = (string) $section->mslp_pressure['units'];

        $weather->daytime = (string) $section->daytime;

        $weather->season = (string) $section->season;
        $weather->season_type = (string) $section->season['type'];

        $weather->ipad_image = (string) $section->ipad_image;

        return $weather;
    }

    static function parsWeatherInformer(SimpleXMLElement $temperature)
    {
        $weatherInformer = new WeatherInformer();

        $weatherInformer->temperature = (string) $temperature;
        $weatherInformer->color = (string) $temperature['color'];
        $weatherInformer->type = (string) $temperature['type'];

        return $weatherInformer;
    }

    static function parsWeatherDay(SimpleXMLElement $day)
    {
        $weatherDay = new WeatherDay();

        $weatherDay->date = (string) $day['date'];
        $weatherDay->sunrise = (string) $day->sunrise;
        $weatherDay->sunset = (string) $day->sunset;
        $weatherDay->moon_phase = (string) $day->moon_phase;
        $weatherDay->moon_phase_code = (string) $day->moon_phase['code'];
        $weatherDay->moonrise = (string) $day->moonrise;
        $weatherDay->moonset = (string) $day->moonset;
        $weatherDay->biomet_index = (string) $day->biomet['index'];
        $weatherDay->biomet_geomag = (string) $day->biomet['geomag'];
        $weatherDay->biomet_low_press = (string) $day->biomet['low_press'];
        $weatherDay->biomet_low_press = (string) $day->biomet['low_press'];

        if ($day->biomet->message instanceof SimpleXMLElement)
        {
            foreach ($day->biomet->message as $message)
            {
                $weatherDay->biomet_message_codes[] = (string) $message['code'];
            }
        }

        foreach ($day->day_part as $dayPart)
        {
            $weatherDay->parts[] = self::parsWeatherDayPart($dayPart);
        }

        foreach ($day->hour as $hour)
        {
            $weatherDay->hours[] = self::parsWeatherHour($hour);
        }

        return $weatherDay;
    }

    static function parsWeatherDayPart(SimpleXMLElement $dayPart)
    {
        $weatherDayPart = new WeatherDayPart();

        $weatherDayPart->typeid = (string) $dayPart['typeid'];
        $weatherDayPart->type = (string) $dayPart['type'];

        $weatherDayPart->temperature = (string) $dayPart->temperature;
        $weatherDayPart->temperature_from = (string) $dayPart->temperature_from;
        $weatherDayPart->temperature_to = (string) $dayPart->temperature_to;
        $weatherDayPart->temperature_data_avg = (string) $dayPart->{'temperature-data'}->avg;
        $weatherDayPart->temperature_data_avg_bgcolor = (string) $dayPart->{'temperature-data'}->avg['bgcolor'];
        $weatherDayPart->temperature_data_from = (string) $dayPart->{'temperature-data'}->from;
        $weatherDayPart->temperature_data_to = (string) $dayPart->{'temperature-data'}->to;

        $weatherDayPart->weather_condition_code = (string) $dayPart->weather_condition['code'];

        $weatherDayPart->image = (string) $dayPart->image;
        $weatherDayPart->image_type = (string) $dayPart->image['type'];
        $weatherDayPart->image_v2 = (string) $dayPart->{'image-v2'};
        $weatherDayPart->image_v2_color = (string) $dayPart->{'image-v2'}['color'];
        $weatherDayPart->image_v2_type = (string) $dayPart->{'image-v2'}['type'];
        $weatherDayPart->image_v3 = (string) $dayPart->{'image-v3'};
        $weatherDayPart->image_v3_type = (string) $dayPart->{'image-v3'}['type'];

        $weatherDayPart->weather_type_ru = (string) $dayPart->weather_type;
        $weatherDayPart->weather_type_short_ru = (string) $dayPart->weather_type_short;
        $weatherDayPart->weather_type_tt = (string) $dayPart->weather_type_tt;
        $weatherDayPart->weather_type_short_tt = (string) $dayPart->weather_type_short_tt;
        $weatherDayPart->weather_type_tr = (string) $dayPart->weather_type_tr;
        $weatherDayPart->weather_type_short_tr = (string) $dayPart->weather_type_short_tr;
        $weatherDayPart->weather_type_kz = (string) $dayPart->weather_type_kz;
        $weatherDayPart->weather_type_short_kz = (string) $dayPart->weather_type_short_kz;
        $weatherDayPart->weather_type_ua = (string) $dayPart->weather_type_ua;
        $weatherDayPart->weather_type_short_ua = (string) $dayPart->weather_type_short_ua;
        $weatherDayPart->weather_type_by = (string) $dayPart->weather_type_by;
        $weatherDayPart->weather_type_short_by = (string) $dayPart->weather_type_short_by;

        $weatherDayPart->wind_direction = (string) $dayPart->wind_direction;
        $weatherDayPart->wind_speed = (string) $dayPart->wind_speed;

        $weatherDayPart->humidity = (string) $dayPart->humidity;

        $weatherDayPart->pressure = (string) $dayPart->pressure;
        $weatherDayPart->pressure_units = (string) $dayPart->pressure['units'];
        $weatherDayPart->mslp_pressure = (string) $dayPart->mslp_pressure;
        $weatherDayPart->mslp_pressure_units = (string) $dayPart->mslp_pressure['units'];

        return $weatherDayPart;
    }

    static function parsWeatherHour(SimpleXMLElement $hour)
    {
        $weatherHour = new WeatherHour();

        $weatherHour->at = (string) $hour['at'];
        $weatherHour->temperature = (string) $hour->temperature;
        $weatherHour->weather_condition_code = (string) $hour->weather_condition['code'];

        $weatherHour->image = (string) $hour->image;
        $weatherHour->image_type = (string) $hour->image['type'];
        $weatherHour->image_v2 = (string) $hour->{'image-v2'};
        $weatherHour->image_v2_color = (string) $hour->{'image-v2'}['color'];
        $weatherHour->image_v2_type = (string) $hour->{'image-v2'}['type'];
        $weatherHour->image_v3 = (string) $hour->{'image-v3'};
        $weatherHour->image_v3_type = (string) $hour->{'image-v3'}['type'];

        return $weatherHour;
    }
} 