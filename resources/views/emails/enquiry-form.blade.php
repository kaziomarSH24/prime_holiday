@component('mail::message')
# @if($isAdmin) New Enquiry Received @else Thank You for Your Enquiry, {{ $formData['first_name'] }} {{ $formData['last_name'] }} @endif

@if($isAdmin)
Dear Admin,

A new enquiry has been submitted. Below are the details:
@else
Dear {{ $formData['first_name'] }},

Thank you for contacting us. We have received your enquiry and our team will get back to you shortly. Below are the details of your submission:
@endif

---

### Enquiry Details:
- **Full Name:** {{ $formData['title'] }} {{ $formData['first_name'] }} {{ $formData['last_name'] }}
- **Email:** {{ $formData['email'] }}
- **Telephone:** {{ $formData['telephone'] }}


---

### Message:
{{ $formData['enquiry'] }}

---

@if($isAdmin)
Please follow up with the client as soon as possible.
@else
If you have any further questions, feel free to reply to this email.
@endif

@component('mail::button', ['url' => config('app.url')])
@if($isAdmin)
Visit Admin Panel
@else
Visit Our Website
@endif
@endcomponent

Thanks,
**{{ config('app.name') }}**
@endcomponent
