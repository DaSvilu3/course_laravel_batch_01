# هيكل منتج رقمي بلارافيل — Laravel Product Skeleton

<div dir="rtl">

هيكل انطلاق (Starter Kit) لبناء **منتج SaaS** بلارافيل: صلاحيات (مدير / مستخدم)، نظام **باقات واشتراكات** مع فوترة، بوابة دفع **ثواني (Thawani)**، وحماية الميزات حسب الباقة — بالإضافة إلى كتالوج خدمات/منتجات كمثال عملي جاهز. واجهة عربية RTL وإنجليزية.

بُني كقاعدة انطلاق لورشة **بناء منتج رقمي مع قاعدة بيانات**، ويصلح كأساس لأي منتج SaaS في سلطنة عمان.

> هذا **هيكل عام** لتبني عليه منتجك، وليس منتجًا نهائيًا. الكتالوج (خدمات/منتجات) موجود كمثال يوضّح كيف تُبنى الميزات فوق الهيكل — احذفه أو استبدله بمجال منتجك.

</div>

---

## Table of contents

- [ما الذي يوفره هذا الهيكل؟ / What's inside](#whats-inside)
- [التشغيل السريع / Quick start](#quick-start)
- [حسابات التجربة / Demo accounts](#demo-accounts)
- [طبقة الـ SaaS: الباقات والاشتراكات / The SaaS layer](#saas-layer)
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
| **الاشتراكات / SaaS** | باقات + اشتراكات + فترة تجريبية + تجديد/إلغاء + حماية الميزات حسب الباقة |
| **محرّك دفع عام / Payments** | يفوتر أي `Payable` (طلب **أو** اشتراك) عبر ثواني + بوابة وهمية + Webhook |
| **الفوترة الدورية / Billing** | أمر مجدول يرسل تذكيرات التجديد ويُنهي الاشتراكات المنتهية |
| **الكتالوج (مثال) / Catalog** | تصنيفات، خدمات (قابلة للحجز)، منتجات (مخزون) — كمثال يوضّح البناء |
| **السلة / Cart** | سلة في الجلسة تعمل مع أي عنصر يطبّق `Purchasable` |
| **لوحة الإدارة / Admin** | CRUD كامل (باقات، اشتراكات، كتالوج، طلبات) بـ Blade + Tailwind |
| **اللغات / i18n** | عربي RTL (افتراضي) + إنجليزي LTR مع مبدّل لغة |
| **الاختبارات / Tests** | 101 اختبار يغطّي الصلاحيات، الاشتراكات، الدفع، وثواني |

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

يُنشئ `PlanSeeder` ثلاث باقات جاهزة: **مجاني**، **برو** (15.000 ر.ع/شهر مع تجربة 14 يوم)، و**الأعمال** (40.000 ر.ع/شهر).

---

<h2 id="saas-layer">طبقة الـ SaaS: الباقات والاشتراكات / The SaaS layer</h2>

<div dir="rtl">

هذا هو قلب الهيكل كمنتج SaaS. المستخدم يشترك في **باقة** فيحصل على **اشتراك** نشط، وتُفتح له الميزات المحمية.

**دورة الاشتراك:**

</div>

```
/plans  (صفحة الأسعار العامة)
   │  المستخدم يختار باقة
   ▼
SubscriptionService::subscribe()      ← ينشئ اشتراكًا pending (نسخة ثابتة من السعر)
   │
   ├── باقة مجانية (السعر = 0)  →  تُفعّل فورًا، بلا دفع
   └── باقة مدفوعة  →  CheckoutService::start($subscription)  →  ثواني
                                    │  نفس محرّك الدفع المستخدم للطلبات
                                    ▼
                         بعد نجاح الدفع (رابط موقّع أو webhook)
                                    ▼
                         SubscriptionService::activate()
                                    │  يفتح مدة اشتراك (شهر/سنة)، أو يمددها عند التجديد
                                    ▼
                              SubscriptionStarted event
```

### حماية الميزات حسب الباقة

```php
// حماية مسار كامل بالاشتراك
Route::get('members', ...)->middleware('subscribed');          // أي باقة نشطة
Route::get('pro-area', ...)->middleware('subscribed:pro');      // باقة "pro" تحديدًا
Route::get('x', ...)->middleware('subscribed:pro,business');    // إحدى الباقتين

// في الكود / في Blade — قراءة حدود وميزات الباقة
$user->subscribed();                       // هل لديه اشتراك نشط؟
$user->onPlan('pro');                      // على باقة معيّنة؟
$user->hasFeature('api_access');           // ميزة مُفعّلة؟ (flag منطقي)
$user->planFeature('max_projects', 0);     // قيمة حد (‎-1‎ = غير محدود)
```

<div dir="rtl">

المدير (`admin`) يتجاوز حاجز الاشتراك دائمًا. غير المشتركين يُحوَّلون لصفحة الأسعار.

مثال حيّ: المسار `/members` محميّ بـ `subscribed` — جرّبه بحساب بلا اشتراك.

**تعريف الباقات:** من لوحة الإدارة `/admin/plans`. الميزات تُكتب سطرًا لكل `key: value`:

</div>

```
max_projects: 20
api_access: true
support: priority
```

<div dir="rtl">

الأرقام تصبح أعدادًا، و`true/false` تصبح قيمًا منطقية، و`-1` تعني «غير محدود». تُقرأ عبر `$plan->feature('max_projects')`.

### التجديد والانتهاء (الفوترة الدورية)

نموذج **التجديد اليدوي**: لا نحفظ بطاقات ولا نسحب تلقائيًا. أمر مجدول يومي:

</div>

```bash
php artisan subscriptions:process            # تشغيل فعلي
php artisan subscriptions:process --dry-run   # تقرير فقط
```

<div dir="rtl">

يقوم بأمرين: (1) إرسال تذكير قبل انتهاء المدة بـ `SUBSCRIPTION_REMINDER_DAYS` يوم، (2) إنهاء الاشتراكات التي انتهت مدتها. العميل يجدّد يدويًا من صفحة `/billing` عبر دفعة ثواني جديدة.

الأمر مُسجَّل في الجدولة (`routes/console.php`)، فتأكد أن الـ scheduler يعمل على السيرفر:

</div>

```cron
* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
```

**إعدادات (`.env`):**

```dotenv
SUBSCRIPTION_REMINDER_DAYS=3   # التذكير قبل الانتهاء بكم يوم
SUBSCRIPTION_GRACE_DAYS=0      # أيام سماح بعد الانتهاء قبل الإيقاف
```

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

<div dir="rtl">

محرّك الدفع **عام**: يفوتر أي شيء يطبّق `App\Contracts\Payable` — سواء كان `Order` (طلب متجر) أو `Subscription` (اشتراك SaaS). `CheckoutService` وسائق ثواني لا يعرفان الفرق، والتنفيذ بعد الدفع يُفوَّض للـ Payable نفسه (`handlePaymentPaid`). لتفوتر شيئًا جديدًا (شحن رصيد، تبرع…) طبّق الواجهة فقط — الدفع لا يتغيّر.

</div>

```
العميل يضغط "ادفع"  (طلب أو اشتراك)
        │
        ▼
CheckoutService::start(Payable)
        │  الأسعار تُقرأ من قاعدة البيانات عبر paymentTotal()، لا من الطلب
        │  POST /checkout/session  →  session_id
        │  يحفظ Payment متعدّد الأشكال (payable_type/payable_id)
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
                 $payable->handlePaymentPaid()
                            ├─ Order        → markAsPaid (مخزون + حجوزات) → OrderPaid
                            └─ Subscription → activate (فتح/تمديد المدة)  → SubscriptionStarted
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
│   ├── Payable.php               ← أي شيء يُفوتَر (Order, Subscription)
│   ├── PaymentGateway.php        ← أي بوابة دفع تطبّق هذه الواجهة
│   └── Purchasable.php           ← أي شيء قابل للبيع في السلة
├── Console/Commands/
│   └── ProcessSubscriptions.php  ← التذكيرات + إنهاء الاشتراكات (مجدول يوميًا)
├── Enums/                        UserRole, SubscriptionStatus, BillingInterval,
│                                 OrderStatus, PaymentStatus, BookingStatus, CatalogType
├── Events/                       OrderPaid, SubscriptionStarted
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                لوحة الإدارة (باقات، اشتراكات، كتالوج…)
│   │   ├── Billing/             اشتراك المستخدم / التجديد / الإلغاء
│   │   ├── Payments/             callbacks + webhook + البوابة الوهمية
│   │   └── Shop/                 المتجر والسلة والطلبات والأسعار
│   ├── Middleware/               EnsureUserHasRole, EnsureSubscribed, EnsureUserIsActive, SetLocale
│   └── Requests/
├── Models/                       User, Plan, Subscription, Order, OrderItem, Payment,
│                                 Category, Service, Product, Booking
├── Payments/
│   ├── Gateways/                 ThawaniGateway, FakeGateway
│   ├── Data/                     CheckoutSession, PaymentVerification
│   └── PaymentManager.php
├── Policies/                     OrderPolicy, BookingPolicy
├── Services/                     CheckoutService, OrderService, SubscriptionService
└── Support/                      Cart, CartItem, Money, Locale

config/payments.php               إعدادات الدفع
config/subscriptions.php          إعدادات التذكير والسماح
lang/{ar,en}/                     كل النصوص
resources/views/
├── layouts/{app,guest,admin}.blade.php
├── admin/…                       لوحة الإدارة
├── billing/…                     صفحة الاشتراك
└── shop/…                        المتجر + صفحة الأسعار (plans)
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
php artisan test                                  # 101 اختبار
php artisan test --filter=SubscriptionTest
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
- ✅ شغّل الـ scheduler (cron) حتى تعمل تذكيرات وإنهاء الاشتراكات
- ✅ `QUEUE_CONNECTION=database` + `php artisan queue:work` إذا فعّلت إشعارات البريد

> ملاحظة: `php artisan route:cache` يثبّت المسارات وقت البناء، وصفحة البوابة الوهمية لا تُسجَّل إلا عندما تكون `PAYMENT_GATEWAY=fake` — وهذا مقصود، فهي يجب ألا توجد في الإنتاج.

</div>

---

## License

MIT — استخدمه وعدّله كما تشاء.
