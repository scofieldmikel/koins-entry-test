<x-mail::message>
# Hi

This is a test email for campaign

{{ $campaign->user->first_name }},
We are pleased to inform you that your campaign has been sent successfully.

@if($campaign->banners)
<img src="{{ $campaign->banners }}" alt="Campaign Banner" style="max-width: 100%; height: auto;">
@endif

Thanks,<br>
KoinsMFB
</x-mail::message>
