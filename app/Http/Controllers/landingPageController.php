<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\userEmail;
use Illuminate\Support\Facades\Mail;

class landingPageController extends Controller
{
    public function landingPage()
    {
        return view('landingpage.landingpage');

    }
    public function sendEmail(Request $request)
    {
         $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|max:1000',
        ]);
        $tenantName = $request->name;
        $messageBody = $request->message;
        Mail::to($request->email)->send(new userEmail($tenantName, $messageBody));
        return back()->with('success', 'Your message has been sent successfully!');


    }
}
