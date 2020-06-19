<?php

class UrlValidation
{
    public function validate( $value, $parameters)
    {
        // Check if the URL provided in $value is valid and has the same hostname
        // passed in $parameters->host
        return $this->isValidUrl($value, $parameters->host, 'host');
    }

    function isValidUrl($url, $parameter, $property)
    {
        // first do some quick sanity checks:
        if (!$url || !is_string($url)) {
            return false;
        }

        $url = filter_var($url, FILTER_SANITIZE_URL);

        // --- Validate url -> if protocol is not present, it will return 'false'
        // if (!filter_var($url, FILTER_VALIDATE_URL)) {
        //     return false;
        // } 

        // If url doesn't have a protocol, add one. We need this to check the response
        // code later
        if(substr($url, 0, 4) != 'http'){
            $url = 'http://' . $url;
        }

        if(parse_url($url)[$property] != $parameter){
             return false;
        }
        
        if($this->getHttpResponseCode_using_getheaders($url) != 200){ 
            return false;
        }
        
        return true;
    }

    function getHttpResponseCode_using_getheaders($url, $followredirects = true)
    {        
        if (!$url || !is_string($url)) {
            return false;
        }
        $headers = @get_headers($url);
        if ($headers && is_array($headers)) {
            if ($followredirects) {                
                $headers = array_reverse($headers);
            }
            foreach ($headers as $hline) {               
                if (preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches)) { 
                    $code = $matches[1];
                    return $code;
                }
            }           
            return false;
        }        
        return false;
    }
}