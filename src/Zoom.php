<?php

namespace Bashmohandes7\ZoomService;

use Bashmohandes7\ZoomService\Models\ZoomOauth;
use GuzzleHttp\Client;

class Zoom
{
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

    public static function zoomCallback()
    {
        try {
            $client = new Client(['base_uri' => config('zoomconfig.base_url')]);
            $response = $client->request('POST', '/oauth/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(config('zoomconfig.client_id') . ':' . config('zoomconfig.client_secret')),
                ],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => request()->query('code'),
                    'redirect_uri' => config('zoomconfig.redirect_url'),
                ],
            ]);
            $token = json_decode($response->getBody()->getContents(), true);

            (new self())->updateAccessToken(json_encode($token));
            echo "Access token inserted successfully.";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function createMeeting($meeting_data = [])
    {
        $client = new Client(['base_uri' => config('zoomconfig.base_url')]);

        $arrToken = (new self())->getAccessToken();
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
                $refresh_token = (new self())->getRefreshToken();
                $client = new Client(['base_uri' => 'https://zoom.us']);
                $response = $client->request('POST', '/oauth/token', [
                    "headers" => [
                        "Authorization" => "Basic " . base64_encode(config('zoomconfig.client_id') . ':' . config('zoomconfig.client_secret'))
                    ],
                    'form_params' => [
                        "grant_type" => "refresh_token",
                        "refresh_token" => $refresh_token
                    ],
                ]);
                (new self())->updateAccessToken($response->getBody());
                return (new self())->createMeeting($meeting_data = []);
            } else {
                echo $e->getMessage();
            }
        }
    }
}
