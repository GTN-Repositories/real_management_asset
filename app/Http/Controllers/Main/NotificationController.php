<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    function index()
    {
        return view('main.notifications.index');
    }

    function show($id)
    {
        $notification = Notification::where('id', $id)->first();
        return view('main.notifications.detail', compact('notification'));
    }
}
