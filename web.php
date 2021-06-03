<?php

Route::group(['middleware' => ['auth','verified']], function () {
  Route::post('/api/convos/add', '\Rapyd\Messenger\Convos@send')->name('rapyd.convos.add');
});
