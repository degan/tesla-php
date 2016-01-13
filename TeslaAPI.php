<?php
/**
 * TeslaAPI
 *
 * @author Devin Egan <github@devinegan.com>
 */

class TeslaAPI
{

    private $clientID = "{CLIENT ID}";           // Get current Client ID and Secret from apiary
    private $clientSecret = "{CLIENT SECRET}";
    private $url = "https://owner-api.teslamotors.com";
    private $id;
    private $token;

    /**
     * @param     $email
     * @param     $password
     * @param int $id
     */
    public function __construct($email, $password, $id = 0)
    {
        $this->id = $id;
        $this->api_auth($email, $password);
        $this->api_validate_auth();
    }

    /**
     * @param $email
     * @param $password
     */
    private function api_auth($email, $password)
    {
        try {
            $params = http_build_query(array('grant_type' => 'password', 'client_id' => $this->clientID, 'client_secret' => $this->clientSecret, 'email' => $email, 'password' => $password));
            $ch = curl_init($this->url . "/oauth/token");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
            $output = curl_exec($ch);
            $data = json_decode($output, true);
            $this->token = $data['access_token'];
        } catch (Exception $e) {
            die('API Auth Failed! Exception: ' . $e);
        }
    }

    /**
     * @param string $command
     * @param array  $params
     *
     * @return mixed
     */
    public function api_call($command = "", $params = array())
    {
        if (empty($command)) {
            $api_url = $this->url . "/api/1/vehicles";
        } else {
            if (!count($params)) {
                $api_url = $this->url . "/api/1/vehicles/" . $this->id . $command;
            } else {
                $http_params = http_build_query($params);
                $api_url = $this->url . "/api/1/vehicles/" . $this->id . $command . "?" . $http_params;
            }
        }

        try {
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $this->token, "Accept: application/json"));
            $output = curl_exec($ch);
        } catch (Exception $e) {
            die("API Call Failed! Exception: " . $e);
        }
        return json_decode($output, true);
    }

    /**
     * @throws Exception
     */
    private function api_validate_auth()
    {
        $result = $this->api_call();
        if (isset($result['response'][0]['id_s']) && $result['response'][0]['id_s'] > 0) {
            if ($this->id < 1) {
                $this->id = $result['response'][0]['id_s']; // id_s is a stringified version of id since the numeric id is now a BIGINT
            }
        } else {
            Throw New Exception('auth fail.');
        }
    }

    /**
     * @return mixed
     */
    public function get_vehicles()
    {
        return $this->api_call();
    }

    /**
     * @return mixed
     */
    public function get_mobile_enabled()
    {
        return $this->api_call("/mobile_enabled");
    }

    /**
     * @return mixed
     */
    public function get_charge_state()
    {
        return $this->api_call("/data_request/charge_state");
    }

    /**
     * @return mixed
     */
    public function get_climate_state()
    {
        return $this->api_call("/data_request/climate_state");
    }

    /**
     * @return mixed
     */
    public function get_drive_state()
    {
        return $this->api_call("/data_request/drive_state");
    }

    /**
     * @return mixed
     */
    public function get_gui_settings()
    {
        return $this->api_call("/data_request/gui_settings");
    }

    /**
     * @return mixed
     */
    public function get_vehicle_state()
    {
        return $this->api_call("/data_request/vehicle_state");
    }

    /**
     * @return mixed
     */
    public function get_wake_up()
    {
        return $this->api_call("/wake_up");
    }

    /**
     * @return mixed
     */
    public function get_charge_port_door_open()
    {
        return $this->api_call("/command/charge_port_door_open");
    }

    /**
     * @return mixed
     */
    public function get_charge_standard()
    {
        return $this->api_call("/command/charge_standard");
    }

    /**
     * @return mixed
     */
    public function get_charge_max_range()
    {
        return $this->api_call("/command/charge_max_range");
    }

    /**
     * @return mixed
     */
    public function get_charge_start()
    {
        return $this->api_call("/command/charge_start");
    }

    /**
     * @return mixed
     */
    public function get_charge_stop()
    {
        return $this->api_call("/command/charge_stop");
    }

    /**
     * @return mixed
     */
    public function get_flash_lights()
    {
        return $this->api_call("/command/flash_lights");
    }

    /**
     * @return mixed
     */
    public function get_honk_horn()
    {
        return $this->api_call("/command/honk_horn");
    }

    /**
     * @return mixed
     */
    public function get_door_unlock()
    {
        return $this->api_call("/command/door_unlock");
    }

    /**
     * @return mixed
     */
    public function get_door_lock()
    {
        return $this->api_call("/command/door_lock");
    }

    /**
     * @return mixed
     */
    public function get_auto_conditioning_start()
    {
        return $this->api_call("/command/auto_conditioning_start");
    }

    /**
     * @return mixed
     */
    public function get_auto_conditioning_stop()
    {
        return $this->api_call("/command/auto_conditioning_stop");
    }

    /**
     * @return mixed
     */
    public function get_remote_start()
    {
        return $this->api_call("/command/remote_start_drive", array('password' => $password));
    }

    /**
     * @return mixed
     */
    public function get_trunk_open()
    {
        return $this->api_call("/command/trunk_open");
    }

    /**
     * @return string
     */
    public function get_google_map()
    {
        $drive_state = $this->get_drive_state();
        return "http://maps.google.com/?q=" . $drive_state['response']['latitude'] . "," . $drive_state['response']['longitude'];
    }

    /**
     * @param $percent
     *
     * @return mixed
     */
    public function set_charge_limit($percent = 75)
    {
        return $this->api_call("/command/set_charge_limit", array('percent' => $percent));
    }

    /**
     * @param $driver_temp
     * @param $pass_temp
     *
     * @return mixed
     */
    public function set_set_temps($driver_temp, $pass_temp)
    {
        return $this->api_call("/command/set_temps", array('driver_temp' => $driver_temp, 'passenger_temp' => $pass_temp));
    }

    /**
     * @param string $state
     *
     * @return mixed
     */
    public function set_sun_roof_control($state = "close")
    {
        return $this->api_call("/command/sun_roof_control", array('state' => $state));
    }

}
