<?php

if(!function_exists('getZoomUrl')) {
    function getZoomUrl()
    {
        return config('zoomconfig.base_url');
    }
}
if(!function_exists('getZoomClinetId')) {
    function getZoomClinetId()
    {
        return config('zoomconfig.clinet_id');
    }
}
if(!function_exists('getZoomClinetSecret')) {
    function getZoomClinetSecret()
    {
        return config('zoomconfig.clinet_secret');
    }
}
if(!function_exists('getZoomRedirectUrl')) {
    function getZoomRedirectUrl()
    {
        return config('zoomconfig.redirect_url');
    }
}