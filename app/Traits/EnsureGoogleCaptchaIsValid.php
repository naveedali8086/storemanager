<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait EnsureGoogleCaptchaIsValid
{

    public function isGoogleCaptchaValid(): bool
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => '6Ld8MukcAAAAALj3K8ihcWLISL6LxXH9UsW3p29d',
            'response' => $this->input('g_recaptcha_response'),
        ]);

        if ($response->ok()) {

           $response_body = $response->object();

           return $response_body->success;

        } else {
            return false;
        }

    }

}
