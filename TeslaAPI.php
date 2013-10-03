<?php
/**
 * TeslaAPI
 *
 * @author Devin Egan <github@devinegan.com>
 */

class TeslaAPI
{

    private $url = "https://portal.vn.teslamotors.com";
    private $cookie_file = "/tmp/tesla-api-cookie";
    private $id;

    /**
     * @param     $email
     * @param     $password
     * @param int $id
     */
    public function __construct($email, $password, $id = 0)
    {
        $this->id = $id;
        if (!is_file($this->cookie_file)) {
            $this->api_auth($email, $password);
        } 
        $this->api_validate_auth();
    }

    /**
     * @param $email
     * @param $password
     */
    private function api_auth($email, $password)
    {
        try {
            $params = http_build_query(array('user_session' => array('email' => $email, 'password' => $password)));
            $ch = curl_init($this->url . "/login");
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
            $output = curl_exec($ch);
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
            $api_url = $this->url . "/vehicles";
        } else {
            if ($command === "mobile_enabled") {
                $api_url = $this->url . "/vehicles/" . $this->id . "/mobile_enabled";
            } else {
                if (!count($params)) {
                    $api_url = $this->url . "/vehicles/" . $this->id . "/command/" . $command;
                } else {
                    $http_params = http_build_query($params);
                    $api_url = $this->url . "/vehicles/" . $this->id . "/command/" . $command . "?" . $http_params;
                }
            }
        }

        try {
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
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
        if (isset($result[0]['id']) && $result[0]['id'] > 0) {
            if ($this->id < 1) {
                $this->id = $result[0]['id'];
            }
        } else {
            unlink($this->cookie_file);
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
        return $this->api_call("mobile_enabled");
    }

    /**
     * @return mixed
     */
    public function get_charge_state()
    {
        return $this->api_call("charge_state");
    }

    /**
     * @return mixed
     */
    public function get_climate_state()
    {
        return $this->api_call("climate_state");
    }

    /**
     * @return mixed
     */
    public function get_drive_state()
    {
        return $this->api_call("drive_state");
    }

    /**
     * @return mixed
     */
    public function get_gui_settings()
    {
        return $this->api_call("gui_settings");
    }

    /**
     * @return mixed
     */
    public function get_vehicle_state()
    {
        return $this->api_call("vehicle_state");
    }

    /**
     * @return mixed
     */
    public function get_charge_port_door_open()
    {
        return $this->api_call("charge_port_door_open");
    }

    /**
     * @return mixed
     */
    public function get_charge_standard()
    {
        return $this->api_call("charge_standard");
    }

    /**
     * @return mixed
     */
    public function get_charge_max_range()
    {
        return $this->api_call("charge_max_range");
    }

    /**
     * @return mixed
     */
    public function get_charge_start()
    {
        return $this->api_call("charge_start");
    }

    /**
     * @return mixed
     */
    public function get_charge_stop()
    {
        return $this->api_call("charge_stop");
    }

    /**
     * @return mixed
     */
    public function get_flash_lights()
    {
        return $this->api_call("flash_lights");
    }

    /**
     * @return mixed
     */
    public function get_honk_horn()
    {
        return $this->api_call("honk_horn");
    }

    /**
     * @return mixed
     */
    public function get_door_unlock()
    {
        return $this->api_call("door_unlock");
    }

    /**
     * @return mixed
     */
    public function get_door_lock()
    {
        return $this->api_call("door_lock");
    }

    /**
     * @return mixed
     */
    public function get_auto_conditioning_start()
    {
        return $this->api_call("auto_conditioning_start");
    }

    /**
     * @return mixed
     */
    public function get_auto_conditioning_stop()
    {
        return $this->api_call("auto_conditioning_stop");
    }

    /**
     * @return string
     */
    public function get_google_map()
    {
        $drive_state = $this->get_drive_state();
        return "http://maps.google.com/?q=" . $drive_state['latitude'] . "," . $drive_state['longitude'];
    }

    /**
     * @param $driver_temp
     * @param $pass_temp
     *
     * @return mixed
     */
    public function set_set_temps($driver_temp, $pass_temp)
    {
        return $this->api_call("set_temps", array('driver_temp' => $driver_temp, 'passenger_temp' => $pass_temp));
    }

    /**
     * @param string $state
     *
     * @return mixed
     */
    public function set_sun_roof_control($state = "close")
    {
        return $this->api_call("sun_roof_control", array('state' => $state));
    }

}
