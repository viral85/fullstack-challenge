<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use DB;

class WeatherController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function getUserWeather()
    {
        $current = Carbon::now();
        $current = $current->addHours(1); //add 1 hours on current date
        $current_date_time = $current->toDateTimeString();
        $users = [];
        $userData = User::all();
        if (isset($userData) && !empty($userData)) {
            foreach ($userData as $user) {
                $latitude = $user->latitude;
                $longitude = $user->longitude;
                $wheather = $this->setWheather($latitude, $longitude);
                $weather = $wheather['weather'];
                $main = $wheather['main'];
                $users[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'weather' => $weather,
                    'main' => $main
                ];
            }
        }
        return response()->json([
            'message' => 'User list with Weather...',
            'users' => $users,
            'currentDate' => $current_date_time . " UTC"
        ]);
    }
    public function setWheather($latitude, $longitude)
    {
        if (!empty($latitude) && !empty($longitude)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid=" . USER::WHEATHER_API_KEY,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Version: 1',
                    'User-Agent: postmanruntime'
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return [];
            } else {
                $weather = json_decode($response);
                if (!empty($weather)) {
                    if (isset($weather->weather) && $weather->weather[0] && isset($weather->main)) {
                        $data = [
                            "weather" => $weather->weather[0],
                            "main" => $weather->main
                        ];
                        return $data;
                    }
                }
            }
        }
    }
}
