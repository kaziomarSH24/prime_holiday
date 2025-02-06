@component('mail::message')
{{-- # @if($isAdmin) New Enquiry Received @else Thank You for Your Enquiry, {{ $formData['first_name'] }} {{ $formData['last_name'] }} @endif --}}

@if($isAdmin)
Dear Admin,

A new enquiry has been submitted. Below are the details:
@else
Dear {{ $formData['first_name'] }},

Thank you for getting in touch. <br>
Our team will attend to your enquiry and get back to you shortly. <br>
Below are the details of your enquiry:
@endif

---

@if($isAdmin)
### Customer Details:
@else
### Your Details:
@endif
- **Full Name:** {{ $formData['title'] }} {{ $formData['first_name'] }} {{ $formData['last_name'] }}
- **Email:** {{ $formData['email'] }}
- **Telephone:** {{ $formData['telephone'] }}


---

@if ($isAdmin)
### Customer Enquiry:
@else
### Your Enquiry:
@endif
{{ $formData['enquiry'] }}

---

@if($isAdmin)
Please follow up with the customer as soon as possible.
{{-- @else
If you have any further questions, feel free to reply to this email. --}}
@endif

{{-- @if($isAdmin)
@component('mail::button', ['url' => config('app.url') . '/admin/dashboard'])
Visit Admin Panel
@endcomponent
@else
@component('mail::button', ['url' => config('app.url')])
Visit Our Website
@endcomponent
@endif --}}


Regards,<br>
**{{ config('app.name') }}** Team. <br>
{{ config('app.url') }}

@endcomponent
