<x-guest-layout>
    <div style="max-width: 520px; margin: 40px auto; background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); text-align: center;">
        <h1 style="font-size: 28px; font-weight: 800; color: #111827; margin-bottom: 16px;">
            {{ __('auth.pending_company') }}
        </h1>

        <div style="font-size: 52px; margin-bottom: 16px;">⏳</div>

        <h2 style="font-size: 22px; font-weight: 700; color: #1f2937; margin-bottom: 12px;">
            {{ __('auth.pending_title') }}
        </h2>

        <p style="color: #6b7280; font-size: 16px; line-height: 1.8; margin-bottom: 24px;">
            {{ __('auth.pending_body') }}
        </p>

        <a href="{{ route('login') }}"
           style="display: inline-block; background: #1d4ed8; color: white; padding: 12px 18px; border-radius: 10px; text-decoration: none; font-weight: 700;">
            {{ __('auth.pending_back_login') }}
        </a>
    </div>
</x-guest-layout>
