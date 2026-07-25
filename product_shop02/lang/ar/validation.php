<?php

/*
 * Arabic messages for the validation rules used in this project.
 * Anything not listed here falls back to Laravel's English message.
 */

return [

    'accepted' => 'يجب قبول :attribute.',
    'after' => 'يجب أن يكون :attribute تاريخاً بعد :date.',
    'alpha_dash' => 'يجب أن يحتوي :attribute على حروف وأرقام وشرطات فقط.',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'boolean' => 'حقل :attribute يجب أن يكون صحيحاً أو خاطئاً.',
    'confirmed' => 'حقل تأكيد :attribute غير مطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'حقل :attribute ليس تاريخاً صحيحاً.',
    'email' => 'يجب أن يكون :attribute بريداً إلكترونياً صحيحاً.',
    'exists' => 'قيمة :attribute المحددة غير موجودة.',
    'image' => 'يجب أن يكون :attribute صورة.',
    'in' => 'قيمة :attribute غير صحيحة.',
    'integer' => 'يجب أن يكون :attribute رقماً صحيحاً.',
    'numeric' => 'يجب أن يكون :attribute رقماً.',
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'يجب أن يكون :attribute نصاً.',
    'unique' => 'قيمة :attribute مستخدمة من قبل.',
    'url' => 'يجب أن يكون :attribute رابطاً صحيحاً.',
    'uploaded' => 'فشل رفع :attribute.',

    'min' => [
        'array' => 'يجب أن يحتوي :attribute على :min عنصر على الأقل.',
        'file' => 'يجب أن يكون حجم :attribute :min كيلوبايت على الأقل.',
        'numeric' => 'يجب ألا تقل قيمة :attribute عن :min.',
        'string' => 'يجب ألا يقل :attribute عن :min حرف.',
    ],

    'max' => [
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عنصر.',
        'file' => 'يجب ألا يزيد حجم :attribute عن :max كيلوبايت.',
        'numeric' => 'يجب ألا تزيد قيمة :attribute عن :max.',
        'string' => 'يجب ألا يزيد :attribute عن :max حرف.',
    ],

    'password' => [
        'letters' => 'يجب أن تحتوي :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن تحتوي :attribute على حرف كبير وحرف صغير.',
        'numbers' => 'يجب أن تحتوي :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن تحتوي :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'ظهرت :attribute في تسريب بيانات. الرجاء اختيار كلمة مرور أخرى.',
    ],

    'attributes' => [
        'name' => 'الاسم',
        'name_ar' => 'الاسم بالعربية',
        'name_en' => 'الاسم بالإنجليزية',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'phone' => 'رقم الهاتف',
        'role' => 'الصلاحية',
        'price' => 'السعر',
        'stock' => 'المخزون',
        'quantity' => 'الكمية',
        'category_id' => 'التصنيف',
        'starts_at' => 'الموعد',
        'status' => 'الحالة',
        'image' => 'الصورة',
        'slug' => 'المعرّف',
        'notes' => 'الملاحظات',
    ],

];
