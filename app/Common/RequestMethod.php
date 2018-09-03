<?php

namespace Common;

/**
 *
 * @author Fabio Garcia
 */
class RequestMethod {

   public static function sendRequest($data) {
      if (!isset($data['url']) || is_null($data['url'])) {
         return false;
      }

      $request = curl_init();
      curl_setopt($request, CURLOPT_URL, $data['url']);
      curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($request, CURLOPT_CUSTOMREQUEST, $data['http_verb']);
      //To Fiddler test
      //curl_setopt($request, CURLOPT_PROXY, '127.0.0.1:8888');

      if (isset($data['body']) && !is_null($data['body'])) {
         curl_setopt($request, CURLOPT_POSTFIELDS, $data['body']);
      }

      if (isset($data['headers']) && !is_null($data['headers'])) {
         curl_setopt($request, CURLOPT_HTTPHEADER, $data['headers']);
      }

      $return['response_body'] = curl_exec($request);
      $return['http_code'] = curl_getinfo($request, CURLINFO_HTTP_CODE);

      if (curl_errno($request)) {
         error_log("Error to send request: " . __CLASS__ . '::' . __FUNCTION__ . " linha: " . __LINE__ . "  " . curl_error($request));
         return false;
      }

      curl_close($request);

      return $return;
   }

}
