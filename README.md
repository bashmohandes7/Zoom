
# Zoom Package With Laravel

sample package for laravel applications to integrate with Zoom

# Hi, I'm Diaa Abdallah! 👋


## 🚀 About Me
I'm a Backend Developer...


## Installation

Install via composer

```bash
  composer require bashmohandes7/laravel-zoom-package
```

run migration
```bash
php artisan migrate
```
run vendor publish
```bash
 php artisan vendor:publish --tag="zoomconfig"
```
    
## Environment Variables

To run this package, you will need to add the following environment variables to your .env file

`ZOOM_CLIENT_ID`

`ZOOM_CLIENT_SECRET`

`ZOOM_REDIRECT_URL`

`ZOOM_BASE_URL`

## How to use ?

### generate an authorization URL where a user can click and complete the authorization:
-create a blade file that contains a link to complete the authorization
```bash
$url = "https://zoom.us/oauth/authorize?response_type=code&client_id=".CLIENT_ID."&redirect_uri=".REDIRECT_URI.";
?>
 
<a href="<?php echo $url; ?>">Zoom Authorization</a>
```
- Run the above file on the browser, click on the ‘Zoom Authorization’ link and complete the authorization.
- you should see a success message and the access token would store in your zoom_oauths table.

## Redirect Url:
- create a Route in web routes
```bash
use Bashmohandes7\ZoomService\Zoom;
Route::post('/callback',function(){
  return Zoom::zoomCallback();
});
```

## Create Zoom Meeting
```bash
- this function to create a zoom meeting , just pass meeting data and settings params:
use Bashmohandes7\ZoomService\Zoom;
$meetingData = [
  'topic' =>  'General Talk', // topic
            'type'            =>  2,
            'start_time'    => date('Y-m-dTh:i:00') . 'Z', // will start now
            'duration'        =>  40,
            'password'        =>  mt_rand(), // random password
            // 'timezone'		=> 'Africa/Cairo',
            'settings'        => [
                'host_video'            => false,
                'participant_video'        => true,
                'cn_meeting'            => false,
                'in_meeting'            => false,
                'join_before_host'        => true,
                'mute_upon_entry'        => true,
                'watermark'                => false,
                'use_pmi'                => false,
                'approval_type'            => 1,
                'registration_type'        => 1,
                'audio'                    => 'voip',
                'auto_recording'        => 'none',
                'waiting_room'            => false
            ]
];
Zoom::createMeeting($meetingData);
```
-- it will return a meeting link to join via it.

## License

The Http Client Package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
