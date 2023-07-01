<?php

namespace Bashmohandes7\Zoom;

use bashmohandes7\Zoom\Models\ZoomOauth;
use GuzzleHttp\Client;

class Zoom
{
    public const zoomUrl = getZoomUrl(); // Replace this with the actual value
    public const zoomClientID = getZoomClinetId(); // Replace this with the actual value
    public const zoomClientSecret = getZoomClinetSecret(); // Replace this with the actual value
    public const zoomRedirectUrl = getZoomRedirectUrl();

    public function checkConfigValue($configValue, $errorMessage)
    {
        if (empty($configValue)) {
            throw new \Exception($errorMessage);
        }

        $this->checkConfigValue(self::zoomUrl, 'Please provide Zoom URL');
        $this->checkConfigValue(self::zoomClientID, 'Please provide Zoom Client ID');
        $this->checkConfigValue(self::zoomClientSecret, 'Please provide Zoom Client Secret');
        $this->checkConfigValue(self::zoomRedirectUrl, 'Please provide Zoom Redirect Url');
    }
    public function isTableEmpty()
    {
        $result = ZoomOauth::where('provider', 'zoom')
            ->select('id')->first();
        if ($result) {
            return false;
        }
        return true;
    }
    public function getAccessToken()
    {
        $result = ZoomOauth::where('provider', 'zoom')
            ->select('provider_value')
            ->first();

        if ($result) {
            return json_decode($result->provider_value);
        }
        return null;
    }
    public function getRefreshToken()
    {
        $result = $this->getAccessToken();
        return $result->refresh_token;
    }
    public function updateAccessToken($token)
    {
        if ($this->isTableEmpty()) {
            ZoomOauth::create([
                'provider' => 'zoom',
                'provider_value' => $token
            ]);
        } else {
            ZoomOauth::where('provider', 'zoom')
                ->update(['provider_value' => $token]);
        }
    }

    public function zoomCallback()
    {
        try {
            $client = new Client(['base_uri' => self::zoomUrl]);
            $response = $client->request('POST', '/oauth/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(self::zoomClinetId . ':' . self::zoomClinetSecret)
                ],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => request()->query('code'),
                    'redirect_uri' => self::zoomRedirectUrl
                ],
            ]);
            $token = json_decode($response->getBody()->getContents(), true);

            $this->updateAccessToken(json_encode($token));
            echo "Access token inserted successfully.";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function createMeeting($meeting_data = [])
    {
        $client = new Client(['base_uri' => self::zoomUrl]);

        $arrToken = $this->getAccessToken();
        $accessToken = $arrToken->access_token;
        try {
            $response = $client->request('POST', '/v2/users/me/meetings', [
                "headers" => [
                    "Authorization" => "Bearer $accessToken"
                ],
                'json' => $meeting_data,
            ]);

            $data = json_decode($response->getBody());
            $allData = [
                'join_url' => $data->join_url
            ];
            return $allData;
        } catch (\Exception $e) {
            if (401 == $e->getCode()) {
                $refresh_token = $this->getRefreshToken();
                $client = new Client(['base_uri' => 'https://zoom.us']);
                $response = $client->request('POST', '/oauth/token', [
                    "headers" => [
                        "Authorization" => "Basic " . base64_encode(self::zoomClientID . ':' . self::zoomClientSecret)
                    ],
                    'form_params' => [
                        "grant_type" => "refresh_token",
                        "refresh_token" => $refresh_token
                    ],
                ]);
                $this->updateAccessToken($response->getBody());
                return $this->createMeeting();
            } else {
                echo $e->getMessage();
            }
        }
    }
}