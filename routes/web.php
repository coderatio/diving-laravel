<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::get('links', 'LinksController@index')->name('links.index');
Route::get('create', 'LinksController@create')->name('links.create');
Route::post('store', 'LinksController@store')->name('links.store');
Route::get('links/{id}/edit', 'LinksController@edit')->name('links.edit');
Route::put('links/{id}/update', 'LinksController@update')->name('links.update');
Route::post('links/delete', 'LinksController@destroy')->name('links.delete');

// This is a test route. You can copy into a laravel project and test.
Route::get('github/{username}/events/{type?}', function ($username, $type = '') {
    // You can manually set the type as 'push', 'pull-request', 'issue-commit', 'watch' e.t.c
    // to get their respective details.
    $event = getGitHubUserEventDetails($username, $type);
    echo "Got <b>{$event->events->count()}</b> {$event->type}.";
});


/**
 * This function takes a user github username and event type.
 * Note: Event type can be provided with/without 'Event'.
 * e.g PushEvent can be gotten as push/push-event/push_event
 *
 * The function will return an event type details if provided
 * But, if not provided, it will return all events.
 *
 * @param string $username
 * @param string $eventType (optional)
 * @return object|string
 */
function getGitHubUserEventDetails(string $username, string $eventType = '')
{
    $curl = new \App\Http\HttpCurl();
    try {
        $eventType = \Illuminate\Support\Str::studly(ucfirst($eventType));
        $eventType = str_contains($eventType, 'Event') ? $eventType : "{$eventType}Event";

        $apiUrl = "https://api.github.com/users/{$username}/events/public";
        $jsonData = $curl->get($apiUrl);
        $decodedData = json_decode($jsonData, true);

        if ($eventType == 'Event') {
            return (object) ['type' => 'Events', 'events' => collect($decodedData)];
        }

        $singleEventDetails = collect($decodedData)->filter(function ($event) use ($eventType) {
            return $event['type'] == $eventType ? $event : [];
        });

        return (object)['type' => $eventType, 'events' => $singleEventDetails];

    } catch (Exception $exception) {
        return "Failed to get details: {$exception->getMessage()}";
    }
}
