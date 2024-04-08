<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailSmtpCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $appSettings = Setting::find(1);        
        config([    
            'mailers.smtp.host' => $appSettings->smtp_mail_host,        
            'mailers.smtp.port' => $appSettings->smtp_mail_port,        
            'mailers.smtp.encryption' => $appSettings->smtp_mail_encryption,        
            'mailers.smtp.username' => $appSettings->smtp_mail_username,        
            'mailers.smtp.password' => $appSettings->smtp_mail_password,        
            'mailers.from.address' => $appSettings->smtp_mail_from_address,    
            'mailers.from.name' => $appSettings->smtp_mail_from_name    
        ]);
        return $next($request);
    }
}
