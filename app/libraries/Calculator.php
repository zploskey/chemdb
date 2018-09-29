<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

define('CRONUS_BASE', 'http://hess.ess.washington.edu/');
define('CRONUS_URI', CRONUS_BASE . 'cgi-bin/matweb');

class Calculator
{
    public static function send($submitText, $calcType)
    {
        // prepare for a curl call
        if ($calcType == 'erosion') {
            $mlmfile = 'al_be_erosion_many_v23';
        } else {
            // Assume age
            $mlmfile = 'age_input_v3';
        }

        $fields = array(
            'mlmfile'       => $mlmfile,
            'reportType'    => 'HTML',
            'resultType'    => 'long',
            'plotFlag'      => 'yes',
            'requesting_ip' => getRealIp(),
            'summary'       => 'yes', // Assume data from 1 landform, currently true.
            'text_block'    => $submitText,
        );

        $options = array(
            CURLOPT_POST           => count($fields),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query($fields),
            CURLOPT_CONNECTTIMEOUT => 90,
            CURLOPT_TIMEOUT        => 200,
        );

        // send the request with curl
        $ch = curl_init(CRONUS_URI);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        if (!$result) {
            die('Error retrieving calculator result:<br>' . curl_error($ch));
        }

        curl_close($ch);

        return $result;
    }
}
