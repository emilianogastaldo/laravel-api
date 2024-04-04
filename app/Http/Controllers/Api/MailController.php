<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function message()
    {
        $mail = new ContactMessageMail('prova@email.it');
        Mail::to(env('MAIL_TO_ADDRESS'))->send($mail);

        return response(null, 204);
    }
}
