<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12-Feb-19
 * Time: 12:09 PM
 */

namespace App\Http;


class HttpCurl
{

    /**
     * Prepare an http post request
     * @param $url
     * @param $data
     * @param array $headers
     * @param bool $resp_header
     * @param bool $follow_redirect
     * @return bool|mixed|string
     */
    public function post($url, $data, $headers = array(), $resp_header = false, $follow_redirect = false)
    {
        return $this->httpRequest($url, 'POST', $data, $headers, $resp_header, $follow_redirect);
    }

    /**
     * Prepare an http get request
     * @param $url
     * @param $data
     * @param array $headers
     * @param bool $resp_header
     * @param bool $follow_redirect
     * @return bool|mixed|string
     */
    public function get($url, $data = [], $headers = array(), $resp_header = false, $follow_redirect = false)
    {
        return $this->httpRequest($url, 'GET', $data, $headers, $resp_header, $follow_redirect);
    }

    /**
     * Built an http request
     * @param $url
     * @param string $method
     * @param array $params
     * @param array $headers
     * @param bool $resp_header
     * @param bool $follow_redirect
     * @return bool|mixed|string
     * @throws \Exception
     */
    protected function httpRequest($url, $method = 'GET', $params = array(), $headers = array(), $resp_header = false, $follow_redirect = false)
    {

        $response = '';

        if (!is_callable('curl_init')) {
            throw new \Exception('CURL Not available in this Server.');
        }

        switch ($method) {

            case 'GET':

                $q = '';
                foreach ($params as $key => $value) {

                    $value = urlencode($value);

                    $q .= $key . '=' . $value . '&';


                }

                $req = $url;

                if ($q != '') {
                    $q = rtrim($q, '&');

                    $req = $url . '?' . $q;

                }

                try {
                    $ch = curl_init();

                    if (FALSE === $ch)
                        throw new \Exception('failed to initialize');

                    if (!empty($headers)) {

                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    }

                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
                    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_URL, $req);

                    if ($follow_redirect) {
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                    }

                    if ($resp_header) {
                        curl_setopt($ch, CURLOPT_HEADER, 1);
                    }

                    $response = curl_exec($ch);

                    if (FALSE === $response)
                        return false;

                } catch (\Exception $e) {

                    throw new \Exception($e->getCode() . ' ' . $e->getMessage());

                }

                break;

            case 'POST':

                try {
                    $ch = curl_init();

                    if (FALSE === $ch)
                        throw new \Exception('failed to initialize');

                    if (!empty($headers)) {

                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    }

                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    if ($resp_header) {
                        curl_setopt($ch, CURLOPT_HEADER, 1);
                    }

                    $response = curl_exec($ch);

                    if (FALSE === $response)
                        return false;

                } catch (\Exception $e) {

                    throw new \Exception($e->getCode() . ' ' . $e->getMessage());

                }
                break;

        }

        return $response;

    }

    public function getConfig($index)
    {
        global $config;
        return $config[$index];
    }
}
