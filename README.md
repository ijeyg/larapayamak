# Larapayamak

> Unified Laravel SMS package for Iranian providers with clean multi-gateway switching.

[![PHP Version](https://img.shields.io/badge/PHP-%5E8.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-10%20%7C%2011-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![CI](https://img.shields.io/github/actions/workflow/status/ijeyg/larapayamak/run-tests.yml?branch=main&label=CI)](https://github.com/ijeyg/larapayamak/actions/workflows/run-tests.yml)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.md)

A lightweight and practical SMS abstraction for Laravel projects that need to switch providers without rewriting business logic.

## ✨ Key Highlights
- Multi-gateway architecture for Iranian SMS providers
- Runtime gateway switching via `gateway($name)`
- Facade + Dependency Injection support
- Simple SMS and pattern/template SMS APIs
- Laravel package auto-discovery
- Contract-based provider abstraction
- CI-tested quality pipeline (Pest, PHPStan, Pint)

---

## Why Larapayamak? 🚀
If your application depends on SMS delivery (OTP, notifications, order updates), provider lock-in is risky. Larapayamak gives you one consistent API while keeping provider selection flexible.

---

## Features
- ✅ Unified API through `Ijeyg\Larapayamak\Services\SmsService`
- ✅ Facade API through `Ijeyg\Larapayamak\Facades\Larapayamak`
- ✅ Multi-gateway runtime selection via `gateway('provider')`
- ✅ Default gateway from config/env (`SMS_GATEWAY`)
- ✅ Pattern/template messaging support where provider supports it
- ✅ Package auto-discovery (service provider + facade alias)
- ✅ Clean separation using provider contract (`SmsProviderInterface`)
- ✅ Test suite with Pest + architecture checks

---

## Supported SMS Gateways

| Gateway | Simple SMS | Pattern SMS | Notes |
|---|---|---|---|
| SMS.ir (`smsir`) | ✅ | ✅ | Uses API token header (`X-API-KEY`) |
| FaraPayamak (`farapayamak`) | ✅ | ✅ | Uses Payamak REST endpoints |
| FarazSms (`farazsms`) | ✅ | ✅ | Supports recipient array in simple send |
| MeliPayamak (`melipayamak`) | ✅ | ✅ | Uses Payamak REST endpoints |
| NikSms (`niksms`) | ✅ | ❌ | Pattern method is not implemented in package |
| PayamResan (`payamresan`) | ✅ | ✅ | Uses API key and token endpoints |

---

## Installation

```bash
composer require ijeyg/larapayamak
```

Laravel auto-discovers:
- `Ijeyg\Larapayamak\LarapayamakServiceProvider`
- Facade alias: `Larapayamak`

---

## Configuration

Publish config:

```bash
php artisan vendor:publish --provider="Ijeyg\Larapayamak\LarapayamakServiceProvider" --tag="config"
```

Config file path:

```text
config/larapayamak.php
```

---

## Environment Variables

Set the default gateway:

```env
SMS_GATEWAY=smsir
```

Full example for all gateways:

```env
# Default gateway
SMS_GATEWAY=smsir

# SMS.ir
SMSIR_USERNAME=
SMSIR_TOKEN=
SMSIR_LINE=

# FaraPayamak
FARAPAYAMAK_USERNAME=
FARAPAYAMAK_PASSWORD=
FARAPAYAMAK_LINE=

# MeliPayamak
MELIPAYAMAK_USERNAME=
MELIPAYAMAK_PASSWORD=
MELIPAYAMAK_LINE=

# FarazSms
FARAZSMS_USERNAME=
FARAZSMS_PASSWORD=
FARAZSMS_LINE=

# NikSms
NIKSMS_USERNAME=
NIKSMS_PASSWORD=
NIKSMS_LINE=

# PayamResan
PAYAMRESAN_APITOKEN=
```

---

## Architecture

### Core classes
- `Ijeyg\Larapayamak\Services\SmsService`
- `Ijeyg\Larapayamak\Services\GatewayManager`
- `Ijeyg\Larapayamak\Contracts\SmsProviderInterface`
- `Ijeyg\Larapayamak\Facades\Larapayamak`

### Message flow
1. App code calls `Larapayamak` facade or injected `SmsService`
2. `SmsService` delegates to selected provider
3. Provider sends request through internal HTTP client
4. Package returns `Illuminate\Http\JsonResponse`

---

## Basic Usage

### 1) Facade usage

```php
use Ijeyg\Larapayamak\Facades\Larapayamak;

$response = Larapayamak::sendSimpleMessage('09121111111', 'Hello');
```

```php
use Ijeyg\Larapayamak\Facades\Larapayamak;

$response = Larapayamak::sendPatternMessage('09121111111', '1234', [
    'code' => '7788',
]);
```

### 2) Dependency Injection usage

```php
use Ijeyg\Larapayamak\Services\SmsService;

class SmsController
{
    public function send(SmsService $sms)
    {
        return $sms->sendSimpleMessage('09121111111', 'Welcome');
    }
}
```

### 3) Controller example

```php
<?php

namespace App\Http\Controllers;

use Ijeyg\Larapayamak\Services\SmsService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function otp(SmsService $sms): JsonResponse
    {
        $response = $sms->sendPatternMessage('09121111111', 'OTP_TEMPLATE', [
            'code' => '4532',
        ]);

        $data = $response->getData(true);

        if (($data['success'] ?? false) !== true) {
            return response()->json(['ok' => false, 'gateway' => $data], 422);
        }

        return response()->json(['ok' => true, 'gateway' => $data]);
    }
}
```

### 4) Service class example

```php
<?php

namespace App\Services;

use Ijeyg\Larapayamak\Services\SmsService;

class OtpSender
{
    public function __construct(private SmsService $sms)
    {
    }

    public function send(string $mobile, string $code): array
    {
        $response = $this->sms->sendPatternMessage($mobile, 'OTP_TEMPLATE', [
            'code' => $code,
        ]);

        return $response->getData(true);
    }
}
```

---

## Multi-Gateway Usage (Main Feature) 🔁

Runtime switching without changing your domain logic:

```php
use Ijeyg\Larapayamak\Facades\Larapayamak;

Larapayamak::gateway('smsir')->sendPatternMessage('09121111111', '1234', ['code' => '7788']);
Larapayamak::gateway('farazsms')->sendSimpleMessage('09121111111', 'Hello');
```

### Per-gateway examples

```php
Larapayamak::gateway('smsir')->sendSimpleMessage('09121111111', 'Hello from SMS.ir');
Larapayamak::gateway('smsir')->sendPatternMessage('09121111111', '1234', ['code' => '7788']);
```

```php
Larapayamak::gateway('farapayamak')->sendSimpleMessage('09121111111', 'Hello from FaraPayamak');
Larapayamak::gateway('farapayamak')->sendPatternMessage('09121111111', '1234', ['name' => 'Ali']);
```

```php
Larapayamak::gateway('farazsms')->sendSimpleMessage('09121111111', 'Hello from FarazSms');
Larapayamak::gateway('farazsms')->sendPatternMessage('09121111111', 'PATTERN_CODE', ['name' => 'Ali']);
```

```php
Larapayamak::gateway('melipayamak')->sendSimpleMessage('09121111111', 'Hello from MeliPayamak');
Larapayamak::gateway('melipayamak')->sendPatternMessage('09121111111', '1234', ['code' => '7788']);
```

```php
Larapayamak::gateway('niksms')->sendSimpleMessage('09121111111', 'Hello from NikSms');
// Pattern not implemented in package for niksms:
// Larapayamak::gateway('niksms')->sendPatternMessage(...)
```

```php
Larapayamak::gateway('payamresan')->sendSimpleMessage('09121111111', 'Hello from PayamResan');
Larapayamak::gateway('payamresan')->sendPatternMessage('09121111111', 'TemplateKey', ['p1' => 'value']);
```

---

## Error Handling

- Gateway send methods return `JsonResponse` with success state.
- On provider/network exceptions, gateway methods return HTTP `500` JSON responses.
- Invalid gateway names throw `InvalidArgumentException`.

Example:

```php
$response = Larapayamak::gateway('smsir')->sendSimpleMessage('09121111111', 'Test');
$data = $response->getData(true);

if (($data['success'] ?? false) !== true) {
    // handle failure
}
```

---

## Real-World Use Cases
- OTP / verification codes
- E-commerce order status updates
- Transaction notifications
- Marketing campaigns with provider fallback strategy

---

## OTP Example

```php
Larapayamak::gateway('smsir')->sendPatternMessage('09121111111', 'OTP_TEMPLATE', [
    'code' => '9123',
]);
```

## E-commerce Example

```php
Larapayamak::gateway('farazsms')->sendSimpleMessage(
    '09121111111',
    'Your order #1042 has been shipped.'
);
```

## Multi-Provider Strategy
Use one default provider for normal traffic and switch per action/tenant when needed:
- Default from `SMS_GATEWAY`
- Override at runtime with `gateway('...')`
- Keep your business logic provider-agnostic

---

## Testing & Quality

This package uses:
- **Pest** for tests
- **PHPStan** for static analysis
- **Laravel Pint** for code style
- **GitHub Actions** matrix CI for Laravel/PHP combinations

Useful commands:

```bash
composer test:ci
composer analyse
vendor/bin/pint --test
```

---

## Supported Versions
- PHP: `^8.2`
- Laravel: `10.x` and `11.x`
- Laravel 12: experimental/non-official (not claimed as stable support)

---

## Contributing
PRs and issues are welcome.

Recommended local workflow:

```bash
composer update --with-all-dependencies
composer analyse
composer test:ci
vendor/bin/pint
```

---

## License
MIT. See [LICENSE.md](LICENSE.md).

---
---

# مستندات فارسی

## معرفی پکیج
**Larapayamak** یک پکیج لاراول برای ارسال پیامک از چند سرویس‌دهنده ایرانی با یک API یکپارچه است.  
فلسفه پکیج ساده است: کد کسب‌وکار شما نباید با عوض شدن Provider تغییر کند.

## چرا Larapayamak؟ 🌟
- یک API ثابت برای چند سرویس‌دهنده
- سوییچ سریع درگاه در زمان اجرا با `gateway(...)`
- مناسب OTP، فروشگاه اینترنتی و پیامک‌های تراکنشی
- استفاده راحت با Facade یا Dependency Injection
- ساختار تمیز بر پایه Contract

---

## ویژگی‌ها
- پشتیبانی از چند درگاه پیامکی ایرانی
- ارسال پیامک ساده و پترن (در درگاه‌های پشتیبانی‌شده)
- انتخاب درگاه پیش‌فرض با `.env`
- تغییر درگاه به‌صورت پویا داخل کد
- Auto-discovery در لاراول
- پوشش تست و CI

---

## درگاه‌های پشتیبانی‌شده

| نام درگاه | پیامک ساده | پیامک پترن | توضیح |
|---|---|---|---|
| SMS.ir (`smsir`) | ✅ | ✅ | مناسب OTP و پیامک تراکنشی |
| FaraPayamak (`farapayamak`) | ✅ | ✅ | مبتنی بر API پایامک |
| FarazSms (`farazsms`) | ✅ | ✅ | در پیامک ساده، ورودی آرایه گیرنده هم پشتیبانی می‌شود |
| MeliPayamak (`melipayamak`) | ✅ | ✅ | ساختار نزدیک به FaraPayamak |
| NikSms (`niksms`) | ✅ | ❌ | متد پترن در خود پکیج پیاده‌سازی نشده |
| PayamResan (`payamresan`) | ✅ | ✅ | مبتنی بر API Key |

---

## نصب و راه‌اندازی

```bash
composer require ijeyg/larapayamak
```

پکیج به‌صورت خودکار Service Provider و Facade را رجیستر می‌کند.

### انتشار فایل تنظیمات

```bash
php artisan vendor:publish --provider="Ijeyg\Larapayamak\LarapayamakServiceProvider" --tag="config"
```

مسیر فایل:

```text
config/larapayamak.php
```

---

## متغیرهای محیطی

درگاه پیش‌فرض:

```env
SMS_GATEWAY=smsir
```

نمونه کامل برای همه درگاه‌ها:

```env
SMS_GATEWAY=smsir

SMSIR_USERNAME=
SMSIR_TOKEN=
SMSIR_LINE=

FARAPAYAMAK_USERNAME=
FARAPAYAMAK_PASSWORD=
FARAPAYAMAK_LINE=

MELIPAYAMAK_USERNAME=
MELIPAYAMAK_PASSWORD=
MELIPAYAMAK_LINE=

FARAZSMS_USERNAME=
FARAZSMS_PASSWORD=
FARAZSMS_LINE=

NIKSMS_USERNAME=
NIKSMS_PASSWORD=
NIKSMS_LINE=

PAYAMRESAN_APITOKEN=
```

---

## معماری پکیج

### ایده اصلی
- `SmsService` نقطه ورود اصلی برای ارسال پیامک است.
- `GatewayManager` Provider مناسب را انتخاب می‌کند.
- همه Providerها قرارداد `SmsProviderInterface` را پیاده‌سازی می‌کنند.

### روند اجرا
1. فراخوانی از Facade یا سرویس تزریق‌شده
2. انتخاب درگاه پیش‌فرض یا درگاه مشخص‌شده با `gateway()`
3. ارسال درخواست به سرویس‌دهنده
4. دریافت خروجی به‌صورت `JsonResponse`

---

## استفاده پایه

### استفاده با Facade

```php
use Ijeyg\Larapayamak\Facades\Larapayamak;

Larapayamak::sendSimpleMessage('09121111111', 'سلام');

Larapayamak::sendPatternMessage('09121111111', '1234', [
    'code' => '7788',
]);
```

### استفاده با Dependency Injection

```php
use Ijeyg\Larapayamak\Services\SmsService;

class SmsController
{
    public function send(SmsService $sms)
    {
        return $sms->sendSimpleMessage('09121111111', 'خوش آمدید');
    }
}
```

---

## استفاده از چند Gateway (ویژگی کلیدی) 🔁

```php
use Ijeyg\Larapayamak\Facades\Larapayamak;

Larapayamak::gateway('smsir')->sendPatternMessage('09121111111', '1234', ['code' => '7788']);
Larapayamak::gateway('farazsms')->sendSimpleMessage('09121111111', 'سلام از فراز');
```

### نمونه برای هر درگاه

```php
Larapayamak::gateway('smsir')->sendSimpleMessage('09121111111', 'سلام از SMS.ir');
Larapayamak::gateway('smsir')->sendPatternMessage('09121111111', '1234', ['code' => '7788']);
```

```php
Larapayamak::gateway('farapayamak')->sendSimpleMessage('09121111111', 'سلام از فراپیامک');
Larapayamak::gateway('farapayamak')->sendPatternMessage('09121111111', '1234', ['name' => 'علی']);
```

```php
Larapayamak::gateway('farazsms')->sendSimpleMessage('09121111111', 'سلام از فراز');
Larapayamak::gateway('farazsms')->sendPatternMessage('09121111111', 'PATTERN_CODE', ['name' => 'علی']);
```

```php
Larapayamak::gateway('melipayamak')->sendSimpleMessage('09121111111', 'سلام از ملی‌پیامک');
Larapayamak::gateway('melipayamak')->sendPatternMessage('09121111111', '1234', ['code' => '7788']);
```

```php
Larapayamak::gateway('niksms')->sendSimpleMessage('09121111111', 'سلام از نیک‌اس‌ام‌اس');
// متد پترن برای niksms در این پکیج پیاده‌سازی نشده است.
```

```php
Larapayamak::gateway('payamresan')->sendSimpleMessage('09121111111', 'سلام از پیام‌رسان');
Larapayamak::gateway('payamresan')->sendPatternMessage('09121111111', 'TemplateKey', ['p1' => 'value']);
```

---

## مدیریت خطا
- در خطاهای شبکه یا خطاهای سرویس‌دهنده، خروجی `JsonResponse` با `success=false` برمی‌گردد.
- اگر نام درگاه نامعتبر باشد، `InvalidArgumentException` دریافت می‌کنید.
- بهتر است همیشه مقدار `success` را چک کنید.

```php
$response = Larapayamak::gateway('smsir')->sendSimpleMessage('09121111111', 'test');
$data = $response->getData(true);

if (($data['success'] ?? false) !== true) {
    // مدیریت خطا
}
```

---

## مثال‌های واقعی

### OTP

```php
Larapayamak::gateway('smsir')->sendPatternMessage('09121111111', 'OTP_TEMPLATE', [
    'code' => '9123',
]);
```

### فروشگاه اینترنتی

```php
Larapayamak::gateway('farazsms')->sendSimpleMessage(
    '09121111111',
    'سفارش شما با کد 1042 ارسال شد.'
);
```

### استراتژی چند‌سرویس‌دهنده
اگر پایداری ارسال برای شما مهم است:
- یک درگاه پیش‌فرض در `.env` داشته باشید.
- برای سناریوهای خاص (مثل OTP یا پیامک تبلیغاتی)، درگاه را با `gateway(...)` تغییر دهید.
- منطق دامنه را مستقل از Provider نگه دارید.

---

## تست‌ها و کیفیت کد

```bash
composer test:ci
composer analyse
vendor/bin/pint --test
```

ابزارهای کنترل کیفیت:
- Pest
- PHPStan
- Laravel Pint
- GitHub Actions (Matrix CI)

---

## نسخه‌های پشتیبانی‌شده
- PHP: `^8.2`
- Laravel: `10` و `11`
- Laravel 12: فعلاً رسمی نیست و در حد بررسی/تست است

---

## تغییرات
برای مشاهده تغییرات هر نسخه، فایل [CHANGELOG.md](CHANGELOG.md) را بررسی کنید.

## مشارکت‌کنندگان
برای مشارکت در توسعه، لطفاً راهنمای مشارکت را بررسی کنید و Pull Request بفرستید.  
لیست مشارکت‌کنندگان پروژه در GitHub قابل مشاهده است.

## امنیت
اگر مشکل امنیتی پیدا کردید، لطفاً آن را عمومی منتشر نکنید و از طریق ایمیل زیر اطلاع دهید:  
`javadgeravand96@gmail.com`

## لایسنس
توسعه و انتشار این پکیج تحت لایسنس [MIT](LICENSE.md) انجام می‌شود.
