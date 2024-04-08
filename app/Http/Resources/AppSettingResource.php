<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AppSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'email' => $this->email,
            'address' => $this->address,
            'phone' => $this->phone,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'currency' => $this->currency,
            'timezone' => $this->timezone,
            'color_primary' => $this->color_primary,
            'color_secondary' => $this->color_secondary,
            'meta_title' => $this->meta_title,
            'meta_tags' => $this->meta_tags,
            'meta_description' => $this->meta_description,
            'logo_url' => $this->logo_url ? url(Storage::url($this->logo_url)) : '',
            'favicon_url' => $this->favicon_url ? url(Storage::url($this->favicon_url)) : '',
            'facebook_url' => $this->facebook_url ? $this->facebook_url : '',
            'twitter_url' => $this->twitter_url ? $this->twitter_url : '',
            'instagram_url' => $this->instagram_url ? $this->instagram_url : '',
            'linkedin_url' => $this->linkedin_url ? $this->linkedin_url : '',
            'youtube_url' => $this->youtube_url ? $this->youtube_url : '',
            
            'google_client_secret'=>$this->google_client_secret,
            'twitter_client_secret'=>$this->twitter_client_secret,
            'twitter_client_id'=>$this->twitter_client_id,
            'github_client_secret'=>$this->github_client_secret,
            'github_client_id'=>$this->github_client_id,
            'facebook_client_secret'=>$this->facebook_client_secret,
            'facebook_client_id'=>$this->facebook_client_id,
            'google_client_secret'=>$this->google_client_secret,
            'google_client_id'=>$this->google_client_id,
            'twitter_callback_url_doctor'=>$this->twitter_callback_url_doctor,
            'twitter_callback_url_patient'=>$this->twitter_callback_url_patient,
            'twitter_callback_url_member'=>$this->twitter_callback_url_member,
            'facebook_callback_url_member'=>$this->facebook_callback_url_member,
            'facebook_callback_url_doctor'=>$this->facebook_callback_url_doctor,
            'facebook_callback_url_patient'=>$this->facebook_callback_url_patient,
            'google_callback_url_member'=>$this->google_callback_url_member,
            'google_callback_url_patient'=>$this->google_callback_url_patient,
            'google_callback_url_doctor'=>$this->google_callback_url_doctor,
            'github_callback_url_member'=>$this->github_callback_url_member,
            'github_callback_url_patient'=>$this->github_callback_url_patient,
            'github_callback_url_doctor'=>$this->github_callback_url_doctor,
            
            'email_provider'=>$this->email_provider,
            'smtp_mail_host'=>$this->smtp_mail_host,
            'smtp_mail_port'=>$this->smtp_mail_port,
            'smtp_mail_username'=>$this->smtp_mail_username,
            'smtp_mail_password'=>$this->smtp_mail_password,
            'smtp_mail_encryption'=>$this->smtp_mail_encryption,
            'smtp_mail_from_address'=>$this->smtp_mail_from_address,
            'smtp_mail_from_name'=>$this->smtp_mail_from_name,
            'sendgrid_key'=>$this->sendgrid_key,
            'mailgun_key'=>$this->mailgun_key,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
