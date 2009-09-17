<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Calculator {

    function send($submitText, $calcType) {
        // prepare for a curl call
        $fields = array(
            'requesting_ip' => getRealIp(),
            'mlmfile' => 'al_be_' . $calcType . '_many_v22',
            'text_block' => $submitText,
        );

        $options = array(
            CURLOPT_POST => count($fields),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POSTFIELDS => http_build_query($fields),
            CURLOPT_CONNECTTIMEOUT => 90,
            CURLOPT_TIMEOUT => 200,
        );

        
        // send the request with curl
        $ch = curl_init(CRONUS_URI);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        if (!$result) {
            die('Error retrieving calculator result:<br>' . curl_error($ch));
        }
        
        curl_close($ch);
        $rep = '<head><base href="' . CRONUS_BASE . '" />';
        $html = str_replace('<head>', $rep, $result);
        return $html;
    }

}