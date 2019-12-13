<?php

namespace WF;

use WF\Exception\Trash;

class Parallel
{
    /**
     * Parallel constructor.
     * @param $urls
     * @param array $data
     * @throws Trash
     */
    public function __construct($urls, $data = [])
    {
        $mh = curl_multi_init();

        foreach ($urls as $i => $url) {
            $ch[$i] = curl_init($url);
            curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, 1);
            curl_multi_add_handle($mh, $ch[$i]);
        }

        do {
            $execReturnValue = curl_multi_exec($mh, $runningHandles);
        } while ($execReturnValue == CURLM_CALL_MULTI_PERFORM);

        while ($runningHandles && $execReturnValue == CURLM_OK) {
            if (curl_multi_select($mh) != -1) usleep(100);

            do {
                $execReturnValue = curl_multi_exec($mh, $runningHandles);
            } while ($execReturnValue == CURLM_CALL_MULTI_PERFORM);
        }

        if ($execReturnValue != CURLM_OK) {
            trigger_error('Curl multi read error ' . $execReturnValue, E_USER_WARNING);
        }

        foreach ($urls as $i => $url) {
            $curlError = curl_error($ch[$i]);

            if ($curlError !== '') {
                throw new Trash($curlError, $i);
            }

            $responseContent = curl_multi_getcontent($ch[$i]);
            $res[$i] = $responseContent;

            file_put_contents($data[$i], $res[$i]);

            curl_multi_remove_handle($mh, $ch[$i]);
            curl_close($ch[$i]);
        }

        curl_multi_close($mh);
    }
}