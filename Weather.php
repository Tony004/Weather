<?php

    class Weather{
        private $apiKey;
        private $lat;
        private $lon;

        public function __construct($lat, $lon, $apiKey){
            $this->lat = $lat;
            $this->lon = $lon;
            $this->apiKey = $apiKey;
        }

        //Подключение Weather API
        public function connect_weather(){
            $lat = $this->lat;
            $lon = $this->lon;
            $apiKey = $this->apiKey;
            $url = "https://api.openweathermap.org/data/2.5/onecall?lat=".$lat."&lon=".$lon."&units=metric&lang=ru&exclude=hourly, daily&appid=".$apiKey;

            //Создание запроса
            $ch = curl_init();

            //Настройка
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);

            // Отправляем запрос и получаем ответ
            $data = json_decode(curl_exec($ch));

            // Закрываем запрос
            curl_close($ch);

            return $data;
        }

        //Нахождение нужного нам значения из нескольких дней
        //Получает array, array
        //Возвращает минимальный в array_days
        private function find_min($array_days, $array_diff){
            $min = min($array_diff);

            for($i = 0; $i < count($array_diff); $i++){
                if($array_diff[$i] == $min){
                    $min_key = $i;
                }
            }

            return $array_days[$min_key];
        }

        //Находим нужный день, собираем данные в массив
        //Получает array, array
        //Возвращает array
        private function longest_light_day($array_days, $array_diff){
            $desired_day = $this->find_min($array_days, $array_diff);

            $d = new DateTime();

            $result_day = $d->setTimestamp($desired_day)->format('d.m.Y');
            $light_diff = round(min($array_diff) / 3600, 1);

            $result_array = ["day" => $result_day, "day_long" => $light_diff];

            return $result_array;
        }

        //Осуществляем поиск самомго длинного светового дня на длине $days
        //Получает object, int
        //Возвращает array
        public function find_light_day_distance($data, $days){

            if($days < 1 || $days > 7){
                return "Error";
            }

            // Проверяем данные за количество дней

            $counter = 0;
            $array_days = [];
            $array_diff = [];

            foreach($data->daily as $key=>$elem){
                if($counter < $days){
                    $array_days[] = $elem->sunrise;
                    $array_diff[] = $elem->sunset - $elem->sunrise;
                }
                $counter++;
            }

            $result = $this->longest_light_day($array_days, $array_diff);

            return $result;
        }

        // Берем данные для минимальной разницы температур и собираем в массив
        // Получает array, array
        // Возвращает array
        private function shortest_temp_diff($array_days, $array_diff){
            $desired_day = $this->find_min($array_days, $array_diff);

            $d = new DateTime();
            $result_day = $d->setTimestamp($desired_day)->format('d.m.Y');

            $night_temp_diff = min($array_diff);

            $result_array = ["day" => $result_day, "temp_diff" => $night_temp_diff];

            return $result_array;
        }

        //Осуществляет поиск минимальной разницы температур на длине days
        // Получает object, int
        // Возвращает array
        public function find_min_night_temp_diff($data, $days){
            if($days < 1 || $days > 7){
                return "Error";
            }

            $counter = 0;
            $array_days = [];
            $array_diff = [];

            foreach($data->daily as $key=>$elem){
                if($counter < $days){
                    $array_days[] = $elem->sunrise;
                    $diff_temp = $elem->temp->night - $elem->feels_like->night;
                    $array_diff[] = abs($diff_temp);
                }
                $counter++;
            }

            $result = $this->shortest_temp_diff($array_days, $array_diff);

            return $result;
        }
    }
