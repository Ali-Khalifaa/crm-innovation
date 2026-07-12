<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<style>
  body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #F8FAFC; margin: 0; padding: 40px 20px; color: #1E293B; }
  .email-wrap { max-width: 560px; margin: 0 auto; }
  .email-card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
  .email-header { background: #2563EB; padding: 32px; text-align: center; }
  .email-header img { max-height: 48px; filter: brightness(0) invert(1); }
  .email-body { padding: 36px; }
  h1 { font-size: 22px; font-weight: 700; margin: 0 0 12px; }
  p { font-size: 15px; line-height: 1.7; color: #475569; margin: 0 0 16px; }
  .btn { display: inline-block; background: #2563EB; color: #fff; text-decoration: none; border-radius: 8px; padding: 14px 28px; font-size: 15px; font-weight: 600; margin: 8px 0 20px; }
  .info-row { background: #F8FAFC; border-radius: 10px; padding: 16px; margin-bottom: 16px; }
  .info-label { font-size: 12px; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px; }
  .info-value { font-size: 15px; font-weight: 600; color: #1E293B; margin-top: 4px; }
  .feature-list { padding: 0; margin: 0 0 20px; list-style: none; }
  .feature-list li { padding: 8px 0; border-bottom: 1px solid #F1F5F9; font-size: 14px; color: #475569; }
  .feature-list li:last-child { border: none; }
  .feature-list li::before { content: "✓ "; color: #10B981; font-weight: 700; }
  .email-footer { text-align: center; padding: 20px 36px 28px; color: #94A3B8; font-size: 12px; }
</style>
</head>
<body>
<div class="email-wrap">
  <div class="email-card">
    <div class="email-header">
      <img src="{{ asset('images/logo.png') }}" alt="CRM Innovation">
    </div>
    <div class="email-body">
      <h1>Welcome to CRM Innovation! 🎉</h1>
      <p>Hi <strong>{{ $user->name }}</strong>, your account for <strong>{{ $tenant->name }}</strong> is ready. You have a <strong>{{ config('app.crm_trial_days', 14) }}-day free trial</strong> — no credit card needed.</p>

      <a href="{{ config('app.url') }}/login" class="btn">Access Your CRM →</a>

      <div class="info-row">
        <div class="info-label">Your Account</div>
        <div class="info-value">{{ $user->email }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Company</div>
        <div class="info-value">{{ $tenant->name }}</div>
      </div>
      <div class="info-row">
        <div class="info-label">Current Plan</div>
        <div class="info-value">{{ $tenant->plan->name ?? 'Starter' }} — 14-day free trial</div>
      </div>

      <p style="margin-top:24px;font-weight:600;">What you can do with CRM Innovation:</p>
      <ul class="feature-list">
        <li>Manage contacts & customer profiles</li>
        <li>Track deals with a visual Kanban board</li>
        <li>Create and send professional invoices</li>
        <li>Monitor your business with reports & analytics</li>
        <li>Collaborate with your team</li>
      </ul>

      <p>If you have any questions, reply to this email — we're here to help.</p>
    </div>
    <div class="email-footer">
      <p>© {{ date('Y') }} CRM Innovation. All rights reserved.</p>
      <p>You're receiving this because you created an account at CRM Innovation.</p>
    </div>
  </div>
</div>
</body>
</html>
