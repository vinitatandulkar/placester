<?php

/**
 * Web service interface with placester remote properties storage
 */

/*
response structure:

object(stdClass)[163]
  'half_baths' => int 0
  'price' => float 1250
  'bedrooms' => int 2
  'location' => 
    object(stdClass)[143]
      'address' => string '131 Marion St' (length=13)
      'city' => string 'East Boston' (length=11)
      'zip' => string '02128' (length=5)
      'unit' => string '1' (length=1)
      'state' => string 'MA' (length=2)
      'coords' => 
        object(stdClass)[142]
          'latitude' => float 42.376889
          'longitude' => float -71.036665
  'available_on' => string '02-02-2011' (length=10)
  'amenities' => 
    array
      empty
  'contact' => 
    object(stdClass)[141]
      'phone' => string '+16177345050' (length=12)
      'email' => string 'inquiries-p-a2a134e8-04bf63@placester.net' (length=41)
  'url' => string 'http://placester.com/listing/lead/4d4b04b1abe10f50f4000003/4d4b10ffabe10f55b7000561/' (length=84)
  'id' => string '4d4b10ffabe10f55b7000561' (length=24)
  'images' => 
    array
      0 => 
        object(stdClass)[140]
          'url' => string 'http://placester.com/listing/image/4d4b04b1abe10f50f4000003/4d49fd5adf093a112b028f17.jpg' (length=88)
          'order' => int 0
  'bathrooms' => int 2
  'description' => string 'Great Deal'
*/

/*
 * "Field not valid" exception
 */
class ValidationException extends Exception
{
    /* Object containing error messages for each not valid field, i.e.
     * $validation_data->zip will contain error message for 'zip' field
     */
    public $validation_data;



    /*
     * Constructor
     *
     * @param string $message
     * @param object $validation_data
     */
    function __construct($message, $validation_data)
    {
        parent::__construct($message);
        $this->validation_data = $validation_data;
    }
}



define( 'PLACESTER_TIMEOUT_SEC', 10 );



/*
 * Returns fields acceptable as filter parameters
 *
 * @return array
 */
function placester_filter_parameters_from_http()
{
    $acceptable =
        array
        (
            'agency_id',
            'available_on',
            'bathrooms',
            'bedrooms',
            array('box', 'min_latitude'),
            array('box' , 'max_latitude'),
            array('box' , 'min_longitude'),
            array('box' , 'max_longitude'),
            'half_baths',
            'limit',
            'listing_types',
            array('location' , 'city'),
            array('location' , 'state'),
            array('location' , 'zip'),
            'max_bathrooms',
            'max_bedrooms',
            'max_half_baths',
            'max_price',
            'min_bathrooms',
            'min_bedrooms',
            'min_half_baths',
            'min_price',
            'offset',
            'property_type',
            'purchase_types',
            'sort_by',
            'sort_type',
            'zoning_types',

            'is_featured',
            'is_new'
        );

    $filter_request = array();
    foreach ($acceptable as $key)
    {
        if (is_array($key))
        {
            $request = $_REQUEST;
            $output_key = '';
            for ($n = 0; $n < count($key); $n++)
            {
                $k = $key[$n];
                if (!isset($request[$k]))
                    break;

                $output_key = $output_key . ($n <= 0 ? $k : '[' . $k . ']');
                if ($n < count($key) - 1)
                    $request = $request[$k];
                else
                    $filter_request[$output_key] = $request[$k];
            }
        }
        elseif (isset($_REQUEST[$key]))
            $filter_request[$key] = $_REQUEST[$key];
    }

    return $filter_request;
}



/*
 * Returns list of properties
 *
 * @param array $parameters - http parameters for api
 * @return array
 */
function placester_property_list($parameters)
{
    // Prepare parameters

    $url = 'http://api.placester.com/v1.0/properties.json';
        
    $request = $parameters;
    $request['api_key'] = placester_get_api_key();

    // Override is_featured & is_new
    if (isset($request['is_featured']))
    {
        unset($request['is_featured']);
        $request['ids'] = placester_properties_featured_ids();
    }
    if (isset($request['is_new']))
    {
        unset($request['is_new']);
        $request['ids'] = placester_properties_new_ids();
    }

    // Do request
    return placester_send_request($url, $request);
}



/*
 * Returns property
 *
 * @param array $parameters - http parameters for api
 * @return object
 */
function placester_property_get($id)
{
    // Prepare parameters
    $url = 'http://api.placester.com/v1.0/properties/' . $id . '.json';
    $request = array('api_key' => placester_get_api_key());

    // Do request
    return placester_send_request($url, $request);
}



function placester_apikey_info($api_key)
{
    
    $url = 'http://api.placester.com/v1.0/organizations/whoami.json';
    $request = array('api_key' => $api_key);

    return placester_send_request($url, $request);
}



/*
 * Creates property
 *
 * @param array $p - http parameters for api
 * @return array
 */
function placester_property_add($p)
{
    $request =
        array
        (
            'api_key' => placester_get_api_key(),
            'property_type' => $p->property_type,
            'listing_types' => $p->listing_types,
            'zoning_types' => $p->zoning_types,
            'purchase_types' => $p->purchase_types,
            'bedrooms' => $p->bedrooms,
            'bathrooms' => $p->bathrooms,
            'half_baths' => $p->half_baths,
            'price' => $p->price,
            'available_on' => $p->available_on,
            'url' => placester_get_property_value($p, 'url'),
            'description' => $p->description,
            'location[address]' => $p->location->address,
            'location[neighborhood]' => $p->location->neighborhood,
            'location[city]' => $p->location->city,
            'location[state]' => $p->location->state,
            'location[zip]' => $p->location->zip,
            'location[unit]' => $p->location->unit,
            'location[coords][latitude]' => $p->location->coords->latitude,
            'location[coords][longitude]' => $p->location->coords->longitude
        );

    $url = 'http://api.placester.com/v1.0/properties.json';
        
    // Do request
    return placester_send_request($url, $request, 'POST');
}



/*
 * Modifies property
 *
 * @param string $id
 * @param array $p - http parameters for api
 * @return array
 */
function placester_property_set($id, $p)
{
    $request = 
        array
        (
            'api_key' => placester_get_api_key(),
            'property_type' => $p->property_type,
            'listing_types' => $p->listing_types,
            'zoning_types' => $p->zoning_types,
            'purchase_types' => $p->purchase_types,
            'bedrooms' => $p->bedrooms,
            'bathrooms' => $p->bathrooms,
            'half_baths' => $p->half_baths,
            'price' => $p->price,
            'available_on' => $p->available_on,
            'url' => placester_get_property_value($p, 'url'),
            'description' => $p->description,
            'location[address]' => $p->location->address,
            'location[neighborhood]' => $p->location->neighborhood,
            'location[city]' => $p->location->city,
            'location[state]' => $p->location->state,
            'location[zip]' => $p->location->zip,
            'location[unit]' => $p->location->unit,
            'location[coords][latitude]' => $p->location->coords->latitude,
            'location[coords][longitude]' => $p->location->coords->longitude
        );

    $url = 'http://api.placester.com/v1.0/properties/' . $id . '.json';

    // Do request
    return placester_send_request($url, $request, 'PUT');
}



/*
 * Bulk change of property urls
 *
 * @param string $url_format
 * @param array $p - http parameters for api
 * @return array
 */
function placester_property_seturl_bulk($url_format, $filter)
{
    $request = $filter;
    $request['api_key'] = placester_get_api_key();
    $request['url_format'] = $url_format;

    $url = 'http://api.placester.com/v1.0/properties/urls.json';

    // Do request
    return placester_send_request($url, $request, 'PUT');
}



/*
 * Adds image to property
 *
 * @param string $property_id
 * @param string $file_name
 * @param string $file_mime_type
 * @param string $file_tmpname
 * @return array
 */
function placester_property_image_add($property_id, $file_name, 
    $file_mime_type, $file_tmpname)
{
    $url = 'http://api.placester.com/v1.0/properties/media/image/' . 
        $property_id . '.json';
    $request = array('api_key' => placester_get_api_key());

    $ret = placester_send_request_multipart($url, $request, $file_name, 
        $file_mime_type, $file_tmpname);
    placester_clear_cache();
    return $ret;
}



/*
 * Deletes image from property
 *
 * @param string $property_id
 * @param string $image_url
 * @return array
 */
function placester_property_image_delete($property_id, $image_url)
{
    $request = 
        array
        (
            'api_key' => placester_get_api_key(),
            'url' => $image_url
        );

    $url = 'http://api.placester.com/v1.0/properties/media/image/' . 
        $property_id . '.json';

    // Do request
    return placester_send_request($url, $request, 'DELETE');
}


/*
 * Adds new user
 *
 * @param object $user
 * @return array
 */
function placester_user_add($user)
{
    $request =
        array
        (
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'website' => $user->website,
            'phone' => $user->phone,
            'location[address]' => $user->location->address,
            'location[city]' => $user->location->city,
            'location[zip]' => $user->location->zip,
            'location[state]' => $user->location->state,
            'location[unit]' => $user->location->unit
        );
    placester_cut_empty_fields($request);

    $url = 'http://api.placester.com/v1.0/users/setup.json';
        
    // Do request
    return placester_send_request($url, $request, 'POST');
}



/*
 * Returns user by id
 *
 * @param string $company_id
 * @param string $user_id
 * @return object
 */
function placester_user_get($company_id, $user_id)
{
    $url = 'http://api.placester.com/v1.0/users';
    $request = 
        array
        (
            'api_key' => placester_get_api_key(),
            'agency_id' => $company_id,
            'user_id' => $user_id
        );

    return placester_send_request($url, $request);
}



/*
 * Modifies user
 *
 * @param object $user
 * @return array
 */
function placester_user_set($user)
{
    $request =
        array
        (
            'api_key' => placester_get_api_key(),
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'website' => $user->website,
            'phone' => $user->phone,
            'location[address]' => $user->location->address,
            'location[city]' => $user->location->city,
            'location[zip]' => $user->location->zip,
            'location[state]' => $user->location->state,
            'location[unit]' => $user->location->unit
        );
    placester_cut_empty_fields($request);

    $url = 'http://api.placester.com/v1.0/users.json';
        
    // Do request
    return placester_send_request($url, $request, 'PUT');
}



/*
 * Returns current company
 *
 * @return array
 */
function placester_company_get()
{
    $url = 'http://api.placester.com/v1.0/organizations.json';
    $request = array('api_key' => placester_get_api_key());

    return placester_send_request($url, $request);
}



/*
 * Modifies company
 *
 * @param object $company
 * @return array
 */
function placester_company_set($id, $company)
{
    $request =
        array
        (
            'api_key' => placester_get_api_key(),
            'name' => $company->name,
            'phone' => $company->phone,
            'settings[use_polygons]' => false,
            'settings[enable_campaigns]' => false,
            'settings[require_approval]' => false,
            'location[address]' => $company->location->address,
            'location[city]' => $company->location->city,
            'location[zip]' => $company->location->zip,
            'location[state]' => $company->location->state,
            'location[unit]' => $company->location->unit
        );

    placester_cut_empty_fields($request);
    $url = 'http://api.placester.com/v1.0/organizations/' . $id . '.json';

    // Do request
    return placester_send_request($url, $request, 'PUT');
}


/**
 *      Checks Theme Compatibility
 */
function placester_theme_check($theme)
{
    $request =
        array
        (
            'hash' => $theme->hash,
            'domain' => $theme->domain,
            'theme_name' => $theme->name,
        );
    placester_cut_empty_fields($request);

    $url = 'http://api.placester.com/v1.0/theme/license.json';
        
    try {
        placester_send_request($url, $request, 'POST');        
    } catch (Exception $e) {
        
    }

}

/*
 * Returns list of locations
 *
 * @return array
 */
function placester_location_list()
{
    $request = array('api_key' => placester_get_api_key());

    $url = 'http://api.placester.com/v1.0/properties/locations.json';

    // Do request
    return placester_send_request($url, $request, 'GET');
}



/*
 * Utils
 */

/*
 * Sends HTTP request and parses genercic elements of API response
 *
 * @param string $url
 * @param array $request
 * @param string $method
 * @return array
 */
function placester_send_request($url, $request, $method = 'GET')
{
    $affects_cache = ($method != 'GET');

    $request_string = '';
    foreach ($request as $key => $value)
    {
        if (is_array($value))
        {
            foreach ($value as $v)
                $request_string .= (strlen($request_string) > 0 ? '&' : '') . 
                    urlencode($key) . '[]=' . urlencode($v);
        }
        else
            $request_string .= (strlen($request_string) > 0 ? '&' : '') . 
                $key . '=' . urlencode($value);
    }

    if ($affects_cache)
        $response = false;
    else
    {
        $signature = base64_encode(sha1($url . $request_string, true));
        $transient_id = 'pl_' . $signature;
        
        $response = get_transient($transient_id);
    }

    if ($response === false)
    {
        if ($method == 'POST' || $method == 'PUT')
        {
            $response = wp_remote_post($url, 
                array
                (
                    'body' => $request, 
                    'timeout' => PLACESTER_TIMEOUT_SEC,
                    'method' => $method
                ));
        }
        else if ($method == 'DELETE')
        {
            $request['_method'] = 'DELETE';
            $response = wp_remote_post($url, 
                array
                (
                    'body' => $request, 
                    'timeout' => PLACESTER_TIMEOUT_SEC,
                    'method' => 'POST'
                ));
        }
        else {
            $response = wp_remote_get($url . '?' . $request_string, 
                array
                (
                    'timeout' => PLACESTER_TIMEOUT_SEC
                ));
        }
        
        
        /**
         *      Defines the caching behavior.
         *      
         *      Only cache get requests, requests without errors, and valid responses.
         */
        if ($affects_cache && !isset($response->errors) && $response['headers']["status"] === 200) {
                placester_clear_cache();
        }            
    }

    // throw http-level exception if no response
    if (isset($response->errors))
        throw new Exception(json_encode($response->errors));
    if ($response['response']['code'] == '204')
        return null;

    $o = json_decode($response['body']);
    if (!isset($o))
        throw new Exception($response['response']['message']);

    if (!isset($o->code))
    {}
    else if ($o->code == '201')
    {}
    else if (isset ($o->validations))
        throw new ValidationException($o->message, $o->validations);
    else
        return false;
        // throw new Exception($o->message);

    if (isset($transient_id)) {
        set_transient($transient_id, $response, 3600 * 48);
    }


    return $o;
}



/*
 * Sends multipart HTTP request and parses genercic elements of API response.
 * Used to upload file
 *
 * @param string $url
 * @param array $request
 * @param string $file_name
 * @param string $file_mime_type
 * @param string $file_tmpname
 * @return array
 */
function placester_send_request_multipart($url, $request, $file_name, $file_mime_type, $file_tmpname)
{
    $binary_length = filesize($file_tmpname);
    $binary_data = fread(fopen($file_tmpname, "r"), $binary_length);

    $eol = "\r\n";
    $data = '';
     
    $mime_boundary = md5(time());
     
    foreach ($request as $key => $value)
    {
        $data .= '--' . $mime_boundary . $eol;
        $data .= 'Content-Disposition: form-data; name="' . $key . '"' . $eol . $eol;
        $data .= $value . $eol;
    }

    $data .= '--' . $mime_boundary . $eol;
    $data .= 'Content-Disposition: form-data; name="file"; filename="' . $file_name . '"' . $eol;
    $data .= 'Content-Type: ' . $file_mime_type . $eol;
    $data .= 'Content-Length: ' . $binary_length . $eol;
    $data .= 'Content-Transfer-Encoding: binary' . $eol . $eol;
    $data .= $binary_data . $eol;
    $data .= "--" . $mime_boundary . "--" . $eol . $eol; // Finish with two eols
     
    $params = array('http' => array(
                      'method' => 'POST',
                      'header' => 'Content-Type: multipart/form-data; boundary=' . $mime_boundary . $eol,
                      'content' => $data
                   ));
     
    $ctx = stream_context_create($params);
  
    $handle = @fopen($url, 'r', false, $ctx);
    if ( ! $handle )
    	return false;
        // throw new Exception('http_request_failed');

    stream_set_timeout( $handle, PLACESTER_TIMEOUT_SEC );

    $response = stream_get_contents($handle);
    fclose($handle);

    $o = json_decode($response);

    if (!isset($o->code))
    {}
    else if ($o->code == '201')
    {}
    else if ($o->code == '300')
        return false;
        //throw new ValidationException($o->message, $o->validations);
    else
        return false;
        // throw new Exception($o->message);

    return $o;
}



function placester_clear_cache()
{
    global $wpdb;
    $wpdb->query(
        'DELETE FROM  ' . $wpdb->prefix . 'options '.
        "WHERE option_name LIKE '_transient_pl_%'");

}