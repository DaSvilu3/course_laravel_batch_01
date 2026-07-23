# هيكل منتج رقمي بلارافيل — Laravel Product Skeleton

<div dir="rtl">

هيكل جاهز لبناء منتج رقمي متكامل: صلاحيات (مدير / مستخدم)، كتالوج خدمات ومنتجات، سلة شراء، طلبات، حجوزات، ودفع عبر **ثواني (Thawani)** — مع واجهة عربية RTL وإنجليزية.

بُني كقاعدة انطلاق لورشة **بناء منتج رقمي مع قاعدة بيانات**، ويصلح كأساس لأي مشروع تجاري في سلطنة عمان.

</div>

---

## Table of contents

- [ما الذي يوفره هذا الهيكل؟ / What's inside](#whats-inside)
- [التشغيل السريع / Quick start](#quick-start)
- [حسابات التجربة / Demo accounts](#demo-accounts)
- [إعداد ثواني / Thawani setup](#thawani-setup)
- [كيف يعمل الدفع / How payments work](#how-payments-work)
- [بنية المشروع / Project structure](#project-structure)
- [كيف تضيف شيئًا جديدًا للبيع / Adding a new sellable type](#adding-a-new-sellable-type)
- [الأدوار والصلاحيات / Roles](#roles)
- [اللغات و RTL / Languages](#languages)
- [الاختبارات / Tests](#tests)
- [النشر على السيرفر / Deployment](#deployment)

---

<h2 id="whats-inside">ما الذي يوفره هذا الهيكل؟ / What's inside</h2>

| | |
|---|---|
| **الصلاحيات / Roles** | `admin` و `user` عبر enum + middleware `role:admin` + Policies |
| **الكتالوج / Catalog** | تصنيفات، خدمات (قابلة للحجز ومدّة زمنية)، منتجات (مخزون) |
| **السلة / Cart** | سلة في الجلسة تعمل مع أي عنصر يطبّق `Purchasable` |
| **الطلبات / Orders** | طلبات + عناصر بنسخة ثابتة من الاسم والسعر + سجل حالات |
| **الدفع / Payments** | تكامل ثواني (Checkout API) + بوابة وهمية للتطوير المحلي + Webhook |
| **الحجوزات / Bookings** | تتولّد تلقائيًا بعد دفع أي خدمة قابلة للحجز |
| **لوحة الإدارة / Admin** | CRUD كامل مبني بـ Blade + Tailwind (بدون أي حزمة سحرية) |
| **اللغات / i18n** | عربي RTL (افتراضي) + إنجليزي LTR مع مبدّل لغة |
| **الاختبارات / Tests** | 83 اختبار يغطّي الصلاحيات، السلة، الدفع، وثواني |

**Stack:** Laravel 13 · PHP 8.3 · Breeze (Blade) · Tailwind CSS 3 · Alpine.js · SQLite/MySQL

---

<h2 id="quick-start">التشغيل السريع / Quick start</h2>

```bash
# 1. المتطلبات: PHP 8.3+ و Composer و Node 20+
composer install
npm install

# 2. الإعدادات
cp .env.example .env
php artisan key:generate

# 3. قاعدة البيانات (SQLite افتراضيًا — لا يحتاج أي إعداد)
touch database/database.sqlite
php artisan migrate --seed

# 4. رابط مجلد الصور
php artisan storage:link

# 5. التشغيل
npm run build     # أو: npm run dev
php artisan serve
```

افتح <http://localhost:8000>.

> بوابة الدفع تعمل بالوضع الوهمي (`PAYMENT_GATEWAY=fake`) — تستطيع تجربة دورة الشراء كاملة بدون أي مفاتيح.

### استخدام MySQL بدلًا من SQLite

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_skeleton
DB_USERNAME=root
DB_PASSWORD=
```

---

<h2 id="demo-accounts">حسابات التجربة / Demo accounts</h2>

| الدور | البريد | كلمة المرور |
|---|---|---|
| مدير / admin | `admin@example.com` | `password` |
| عميل / user | `user@example.com` | `password` |

لوحة الإدارة على `/admin`.

> ⚠️ غيّر كلمات المرور هذه قبل أي نشر حقيقي، أو احذف `UserSeeder` من `DatabaseSeeder`.

---

<h2 id="thawani-setup">إعداد ثواني / Thawani setup</h2>

<div dir="rtl">

**1. احصل على المفاتيح**

- بيئة التجربة (UAT): <https://uatmerchant.thawani.om>
- بيئة الإنتاج: <https://merchant.thawani.om>

ستحصل على مفتاحين:

- `secret_key` — يُرسل في ترويسة `thawani-api-key`، **لا يُعرض للمتصفح أبدًا**
- `publishable_key` — يُضاف لرابط صفحة الدفع، وهو عام

**2. ضعها في ملف `.env`**

</div>

```dotenv
PAYMENT_GATEWAY=thawani
THAWANI_MODE=test          # test = UAT sandbox، live = إنتاج
THAWANI_SECRET_KEY=rvtt_xxxxxxxxxxxxxxxxxxxx
THAWANI_PUBLISHABLE_KEY=HGvTMLDssJghr9tlN9gr4DVYt0qyBy
THAWANI_WEBHOOK_SECRET=    # اختياري
```

**3. Webhook (اختياري لكن موصى به)**

سجّل هذا الرابط في لوحة تاجر ثواني:

```
https://your-domain.com/webhooks/thawani
```

<div dir="rtl">

يضمن الـ Webhook اكتمال الطلب حتى لو أغلق العميل المتصفح قبل رجوعه للموقع. الرابط مستثنى من CSRF (انظر `bootstrap/app.php`).

**ملاحظات مهمة عن ثواني:**

- المبالغ تُرسل بالبيسة كأعداد صحيحة: `12.500 ر.ع = 12500`
- الحد الأدنى للعملية `0.100 ر.ع` = `100` بيسة
- اسم المنتج في طلب الـ API محدود بـ 40 حرفًا (يقصّه الكود تلقائيًا)
- روابط `success_url` و `cancel_url` يجب أن تكون روابط عامة كاملة — لن تعمل `localhost` من سيرفر ثواني، استخدم `ngrok` أو ما شابه عند اختبار الـ redirect الحقيقي

</div>

---

<h2 id="how-payments-work">كيف يعمل الدفع / How payments work</h2>

```
العميل يضغط "ادفع"
        │
        ▼
CheckoutController::store
        │  OrderService::createFromCart()  ← الأسعار تُقرأ من قاعدة البيانات، لا من الطلب
        ▼
CheckoutService::start()
        │  POST /checkout/session  →  session_id
        │  يحفظ Payment (status = pending)
        ▼
redirect إلى صفحة ثواني
        │
        ├── نجاح  →  GET /payment/success/{payment}   (رابط موقّع signed)
        └── إلغاء  →  GET /payment/cancel/{payment}   (رابط موقّع signed)
                            │
                            ▼
                 CheckoutService::settle()
                            │  GET /checkout/session/{id}  ← مصدر الحقيقة الوحيد
                            │  تحقّق من تطابق المبلغ
                            ▼
                 OrderService::markAsPaid()
                            │  خصم المخزون + إنشاء الحجوزات
                            ▼
                      OrderPaid event
```

<div dir="rtl">

**ثلاث قواعد أمنية مطبّقة في الكود:**

1. **الرجوع من البوابة ليس دليل دفع.** كل استدعاء يتحقّق من الحالة عبر الـ API قبل أي تعديل.
2. **روابط الرجوع موقّعة (`signed`)** حتى لا يستطيع أحد استدعاؤها لدفعة غيره.
3. **الأسعار تُحسب في الخادم دائمًا.** ما يُرسل من المتصفح هو معرّف العنصر والكمية فقط.

`CheckoutService::settle()` قابل للاستدعاء مرارًا بلا أثر جانبي (idempotent)، لذا يمكن للـ redirect والـ webhook أن يصلا معًا بأمان.

</div>

---

<h2 id="project-structure">بنية المشروع / Project structure</h2>

```
app/
├── Contracts/
│   ├── PaymentGateway.php        ← أي بوابة دفع تطبّق هذه الواجهة
│   └── Purchasable.php           ← أي شيء قابل للبيع يطبّق هذه الواجهة
├── Enums/                        UserRole, OrderStatus, PaymentStatus, BookingStatus, CatalogType
├── Events/OrderPaid.php          ← علّق عليه أي إجراء بعد الدفع
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                لوحة الإدارة
│   │   ├── Payments/             callbacks + webhook + البوابة الوهمية
│   │   └── Shop/                 المتجر والسلة والطلبات
│   ├── Middleware/               EnsureUserHasRole, EnsureUserIsActive, SetLocale
│   └── Requests/
├── Models/                       User, Category, Service, Product, Order, OrderItem, Payment, Booking
├── Payments/
│   ├── Gateways/ThawaniGateway.php
│   ├── Gateways/FakeGateway.php
│   ├── Data/                     CheckoutSession, PaymentVerification
│   └── PaymentManager.php
├── Policies/                     OrderPolicy, BookingPolicy
├── Services/                     OrderService, CheckoutService
└── Support/                      Cart, CartItem, Money, Locale

config/payments.php               كل إعدادات الدفع
lang/{ar,en}/                     كل النصوص
resources/views/
├── layouts/{app,guest,admin}.blade.php
├── admin/…                       لوحة الإدارة
└── shop/…                        المتجر
```

### قرار تصميمي: المال يُخزَّن بالبيسة

<div dir="rtl">

كل الأعمدة المالية أعداد صحيحة بالبيسة (`1 ر.ع = 1000 بيسة`)، لأن الأرقام العشرية (float) لا تصلح للمال: `0.1 + 0.2 !== 0.3`. وثواني نفسها تتعامل بالبيسة. التحويل يحدث في `App\Support\Money` فقط عند العرض أو عند استقبال مدخلات لوحة الإدارة.

</div>

---

<h2 id="adding-a-new-sellable-type">كيف تضيف شيئًا جديدًا للبيع / Adding a new sellable type</h2>

<div dir="rtl">

مثال: بيع اشتراكات. السلة وإتمام الطلب والدفع لن تحتاج أي تعديل.

</div>

```php
// 1. اجعل الموديل يطبّق الواجهة
class Subscription extends Model implements Purchasable
{
    public function purchasableType(): string      { return 'subscription'; }
    public function purchasableName(): string      { return $this->translate('name'); }
    public function purchasableUnitPrice(): int    { return $this->price; }      // بالبيسة
    public function isPurchasable(int $qty = 1): bool { return $this->is_active; }
    public function purchasableUrl(): string       { return route('subscriptions.show', $this); }
    public function purchasableImageUrl(): ?string { return null; }
    public function afterPurchase(int $qty): void  { /* فعّل الاشتراك هنا */ }
}

// 2. سجّله في خريطة الـ morph — app/Providers/AppServiceProvider.php
Relation::enforceMorphMap([
    'service'      => Service::class,
    'product'      => Product::class,
    'subscription' => Subscription::class,   // ← جديد
]);

// 3. أضفه لأنواع السلة — app/Support/Cart.php
public const TYPES = [
    'service'      => Service::class,
    'product'      => Product::class,
    'subscription' => Subscription::class,   // ← جديد
];
```

### إضافة بوابة دفع أخرى

```php
// 1. class جديد يطبّق PaymentGateway (createSession + verify + name)
// 2. أضف حالة في App\Payments\PaymentManager::resolve()
// 3. أضف إعداداته في config/payments.php
// 4. PAYMENT_GATEWAY=اسم_البوابة في .env
```

<div dir="rtl">

الكونترولرات تعتمد على الواجهة `PaymentGateway` وليس على ثواني مباشرة، لذا لن يتغيّر شيء آخر.

</div>

---

<h2 id="roles">الأدوار والصلاحيات / Roles</h2>

```php
// حماية مسار
Route::middleware(['auth', 'role:admin'])->group(/* … */);
Route::middleware('role:admin,manager')->group(/* عدة أدوار */);

// في الكود
$user->isAdmin();
$user->hasRole(UserRole::Admin);

// Policies — المدير يمرّ تلقائيًا عبر Gate::before في AppServiceProvider
$this->authorize('view', $order);
```

<div dir="rtl">

**لإضافة دور جديد:** أضف حالة في `App\Enums\UserRole`، وترجمتها في `lang/{ar,en}/enums.php` — لا شيء آخر.

الحسابات المعطّلة (`is_active = false`) تُسجَّل خروجها تلقائيًا عبر `EnsureUserIsActive`.

</div>

---

<h2 id="languages">اللغات و RTL / Languages</h2>

<div dir="rtl">

- اللغة الافتراضية عربية (`APP_LOCALE=ar`) واتجاه الصفحة يُضبط تلقائيًا في `SetLocale`
- الأسماء والأوصاف تُخزَّن في عمودين: `name_ar` و `name_en`، ويُقرأ الصحيح عبر `$model->name`
- التبديل عبر `/locale/ar` أو `/locale/en` (المبدّل موجود في القائمة العلوية)
- كل نصوص الواجهة في `lang/ar` و `lang/en` — لا نصوص مكتوبة داخل الـ Blade

</div>

---

<h2 id="tests">الاختبارات / Tests</h2>

```bash
php artisan test                                  # 83 اختبار
php artisan test --filter=CheckoutTest
./vendor/bin/pint                                 # تنسيق الكود
```

<div dir="rtl">

الاختبارات لا تتصل بالإنترنت: `ThawaniGatewayTest` يستخدم `Http::fake()` ويتحقّق من شكل الطلب المُرسل لثواني بالضبط، وبقية الاختبارات تستخدم البوابة الوهمية.

</div>

---

<h2 id="deployment">النشر على السيرفر / Deployment</h2>

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan migrate --force
php artisan storage:link
php artisan config:cache route:cache view:cache
```

**قائمة تحقق قبل الإطلاق:**

```dotenv
APP_ENV=production
APP_DEBUG=false          # مهم جدًا
APP_URL=https://your-domain.com
PAYMENT_GATEWAY=thawani
THAWANI_MODE=live
```

<div dir="rtl">

- ✅ احذف أو غيّر حسابات التجربة
- ✅ فعّل HTTPS — روابط الرجوع الموقّعة والـ webhook تحتاجه
- ✅ صلاحيات الكتابة على `storage/` و `bootstrap/cache/`
- ✅ اضبط الـ webhook في لوحة تاجر ثواني
- ✅ `QUEUE_CONNECTION=database` + `php artisan queue:work` إذا فعّلت إشعارات البريد

> ملاحظة: `php artisan route:cache` يثبّت المسارات وقت البناء، وصفحة البوابة الوهمية لا تُسجَّل إلا عندما تكون `PAYMENT_GATEWAY=fake` — وهذا مقصود، فهي يجب ألا توجد في الإنتاج.

</div>

---

## License

MIT — استخدمه وعدّله كما تشاء.
