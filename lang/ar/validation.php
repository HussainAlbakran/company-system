<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'يجب قبول حقل :attribute.',
    'accepted_if' => 'يجب قبول حقل :attribute عندما يكون :other يساوي :value.',
    'active_url' => 'يجب أن يكون حقل :attribute رابطاً صحيحاً.',
    'after' => 'يجب أن يكون حقل :attribute تاريخاً لاحقاً لـ :date.',
    'after_or_equal' => 'يجب أن يكون حقل :attribute تاريخاً لاحقاً لـ :date أو مطابقاً له.',
    'alpha' => 'يجب أن يحتوي حقل :attribute على أحرف فقط.',
    'alpha_dash' => 'يجب أن يحتوي حقل :attribute على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_num' => 'يجب أن يحتوي حقل :attribute على أحرف وأرقام فقط.',
    'any_of' => 'حقل :attribute غير صالح.',
    'array' => 'يجب أن يكون حقل :attribute مصفوفة.',
    'ascii' => 'يجب أن يحتوي حقل :attribute على أحرف ورموز أبجدية رقمية أحادية البايت فقط.',
    'before' => 'يجب أن يكون حقل :attribute تاريخاً سابقاً لـ :date.',
    'before_or_equal' => 'يجب أن يكون حقل :attribute تاريخاً سابقاً لـ :date أو مطابقاً له.',
    'between' => [
        'array' => 'يجب أن يحتوي حقل :attribute على عدد من العناصر بين :min و :max.',
        'file' => 'يجب أن يكون حجم ملف :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute بين :min و :max.',
        'string' => 'يجب أن يكون طول حقل :attribute بين :min و :max حرفاً.',
    ],
    'boolean' => 'يجب أن تكون قيمة حقل :attribute صحيحة أو خاطئة.',
    'can' => 'يحتوي حقل :attribute على قيمة غير مصرّح بها.',
    'confirmed' => 'تأكيد حقل :attribute غير متطابق.',
    'contains' => 'حقل :attribute يفتقد قيمة مطلوبة.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'يجب أن يكون حقل :attribute تاريخاً صالحاً.',
    'date_equals' => 'يجب أن يكون حقل :attribute تاريخاً مطابقاً لـ :date.',
    'date_format' => 'يجب أن يطابق حقل :attribute الصيغة :format.',
    'decimal' => 'يجب أن يحتوي حقل :attribute على :decimal منزلة/منازل عشرية.',
    'declined' => 'يجب رفض حقل :attribute.',
    'declined_if' => 'يجب رفض حقل :attribute عندما يكون :other يساوي :value.',
    'different' => 'يجب أن يكون حقل :attribute و :other مختلفين.',
    'digits' => 'يجب أن يتكون حقل :attribute من :digits أرقام.',
    'digits_between' => 'يجب أن يتكون حقل :attribute من عدد أرقام بين :min و :max.',
    'dimensions' => 'أبعاد صورة حقل :attribute غير صالحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'doesnt_contain' => 'يجب ألا يحتوي حقل :attribute على أي من القيم التالية: :values.',
    'doesnt_end_with' => 'يجب ألا ينتهي حقل :attribute بأحد القيم التالية: :values.',
    'doesnt_start_with' => 'يجب ألا يبدأ حقل :attribute بأحد القيم التالية: :values.',
    'email' => 'يجب أن يكون حقل :attribute بريداً إلكترونياً صالحاً.',
    'encoding' => 'يجب أن يكون ترميز حقل :attribute هو :encoding.',
    'ends_with' => 'يجب أن ينتهي حقل :attribute بأحد القيم التالية: :values.',
    'enum' => 'قيمة :attribute المحددة غير صالحة.',
    'exists' => 'قيمة :attribute المحددة غير صالحة.',
    'extensions' => 'يجب أن يكون امتداد ملف :attribute أحد الامتدادات التالية: :values.',
    'file' => 'يجب أن يكون حقل :attribute ملفاً.',
    'filled' => 'يجب تعبئة حقل :attribute بقيمة.',
    'gt' => [
        'array' => 'يجب أن يحتوي حقل :attribute على أكثر من :value عناصر.',
        'file' => 'يجب أن يكون حجم ملف :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute أكبر من :value.',
        'string' => 'يجب أن يكون طول حقل :attribute أكبر من :value حرفاً.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :value عناصر أو أكثر.',
        'file' => 'يجب أن يكون حجم ملف :attribute أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute أكبر من أو تساوي :value.',
        'string' => 'يجب أن يكون طول حقل :attribute أكبر من أو يساوي :value حرفاً.',
    ],
    'hex_color' => 'يجب أن يكون حقل :attribute لوناً سداسياً عشرياً صالحاً.',
    'image' => 'يجب أن يكون حقل :attribute صورة.',
    'in' => 'قيمة :attribute المحددة غير صالحة.',
    'in_array' => 'يجب أن توجد قيمة حقل :attribute ضمن :other.',
    'in_array_keys' => 'يجب أن يحتوي حقل :attribute على مفتاح واحد على الأقل من المفاتيح التالية: :values.',
    'integer' => 'يجب أن يكون حقل :attribute عدداً صحيحاً.',
    'ip' => 'يجب أن يكون حقل :attribute عنوان IP صالحاً.',
    'ipv4' => 'يجب أن يكون حقل :attribute عنوان IPv4 صالحاً.',
    'ipv6' => 'يجب أن يكون حقل :attribute عنوان IPv6 صالحاً.',
    'json' => 'يجب أن يكون حقل :attribute نص JSON صالحاً.',
    'list' => 'يجب أن يكون حقل :attribute قائمة.',
    'lowercase' => 'يجب أن يكون حقل :attribute بأحرف صغيرة.',
    'lt' => [
        'array' => 'يجب أن يحتوي حقل :attribute على أقل من :value عناصر.',
        'file' => 'يجب أن يكون حجم ملف :attribute أقل من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute أقل من :value.',
        'string' => 'يجب أن يكون طول حقل :attribute أقل من :value حرفاً.',
    ],
    'lte' => [
        'array' => 'يجب ألا يحتوي حقل :attribute على أكثر من :value عناصر.',
        'file' => 'يجب أن يكون حجم ملف :attribute أقل من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute أقل من أو تساوي :value.',
        'string' => 'يجب أن يكون طول حقل :attribute أقل من أو يساوي :value حرفاً.',
    ],
    'mac_address' => 'يجب أن يكون حقل :attribute عنوان MAC صالحاً.',
    'max' => [
        'array' => 'يجب ألا يحتوي حقل :attribute على أكثر من :max عناصر.',
        'file' => 'يجب ألا يتجاوز حجم ملف :attribute :max كيلوبايت.',
        'numeric' => 'يجب ألا تكون قيمة حقل :attribute أكبر من :max.',
        'string' => 'يجب ألا يتجاوز طول حقل :attribute :max حرفاً.',
    ],
    'max_digits' => 'يجب ألا يحتوي حقل :attribute على أكثر من :max أرقام.',
    'mimes' => 'يجب أن يكون حقل :attribute ملفاً من النوع: :values.',
    'mimetypes' => 'يجب أن يكون حقل :attribute ملفاً من النوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :min عناصر على الأقل.',
        'file' => 'يجب ألا يقل حجم ملف :attribute عن :min كيلوبايت.',
        'numeric' => 'يجب ألا تقل قيمة حقل :attribute عن :min.',
        'string' => 'يجب ألا يقل طول حقل :attribute عن :min حرفاً.',
    ],
    'min_digits' => 'يجب أن يحتوي حقل :attribute على :min أرقام على الأقل.',
    'missing' => 'يجب أن يكون حقل :attribute غير موجود.',
    'missing_if' => 'يجب أن يكون حقل :attribute غير موجود عندما يكون :other يساوي :value.',
    'missing_unless' => 'يجب أن يكون حقل :attribute غير موجود ما لم يكن :other يساوي :value.',
    'missing_with' => 'يجب أن يكون حقل :attribute غير موجود عند وجود :values.',
    'missing_with_all' => 'يجب أن يكون حقل :attribute غير موجود عند وجود :values.',
    'multiple_of' => 'يجب أن تكون قيمة حقل :attribute من مضاعفات :value.',
    'not_in' => 'قيمة :attribute المحددة غير صالحة.',
    'not_regex' => 'صيغة حقل :attribute غير صالحة.',
    'numeric' => 'يجب أن يكون حقل :attribute رقماً.',
    'password' => [
        'letters' => 'يجب أن يحتوي حقل :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن يحتوي حقل :attribute على حرف كبير وحرف صغير على الأقل.',
        'numbers' => 'يجب أن يحتوي حقل :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن يحتوي حقل :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'ظهرت قيمة :attribute المعطاة في تسريب بيانات. يُرجى اختيار :attribute مختلف.',
    ],
    'present' => 'يجب أن يكون حقل :attribute موجوداً.',
    'present_if' => 'يجب أن يكون حقل :attribute موجوداً عندما يكون :other يساوي :value.',
    'present_unless' => 'يجب أن يكون حقل :attribute موجوداً ما لم يكن :other يساوي :value.',
    'present_with' => 'يجب أن يكون حقل :attribute موجوداً عند وجود :values.',
    'present_with_all' => 'يجب أن يكون حقل :attribute موجوداً عند وجود :values.',
    'prohibited' => 'حقل :attribute محظور.',
    'prohibited_if' => 'حقل :attribute محظور عندما يكون :other يساوي :value.',
    'prohibited_if_accepted' => 'حقل :attribute محظور عند قبول :other.',
    'prohibited_if_declined' => 'حقل :attribute محظور عند رفض :other.',
    'prohibited_unless' => 'حقل :attribute محظور ما لم يكن :other ضمن :values.',
    'prohibits' => 'حقل :attribute يمنع وجود :other.',
    'regex' => 'صيغة حقل :attribute غير صالحة.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'يجب أن يحتوي حقل :attribute على مدخلات لـ: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other يساوي :value.',
    'required_if_accepted' => 'حقل :attribute مطلوب عند قبول :other.',
    'required_if_declined' => 'حقل :attribute مطلوب عند رفض :other.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other ضمن :values.',
    'required_with' => 'حقل :attribute مطلوب عند وجود :values.',
    'required_with_all' => 'حقل :attribute مطلوب عند وجود :values.',
    'required_without' => 'حقل :attribute مطلوب عند غياب :values.',
    'required_without_all' => 'حقل :attribute مطلوب عند غياب جميع :values.',
    'same' => 'يجب أن يطابق حقل :attribute :other.',
    'size' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :size عناصر.',
        'file' => 'يجب أن يكون حجم ملف :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute مساوية لـ :size.',
        'string' => 'يجب أن يكون طول حقل :attribute :size حرفاً.',
    ],
    'starts_with' => 'يجب أن يبدأ حقل :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون حقل :attribute نصاً.',
    'timezone' => 'يجب أن يكون حقل :attribute منطقة زمنية صالحة.',
    'unique' => 'قيمة :attribute مستخدمة مسبقاً.',
    'uploaded' => 'فشل رفع :attribute.',
    'uppercase' => 'يجب أن يكون حقل :attribute بأحرف كبيرة.',
    'url' => 'يجب أن يكون حقل :attribute رابطاً صالحاً.',
    'ulid' => 'يجب أن يكون حقل :attribute معرّف ULID صالحاً.',
    'uuid' => 'يجب أن يكون حقل :attribute معرّف UUID صالحاً.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'رسالة مخصصة',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        // عام / مستخدمون
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'role' => 'الصلاحية',
        'title' => 'العنوان',
        'description' => 'الوصف',
        'notes' => 'الملاحظات',
        'status' => 'الحالة',
        'type' => 'النوع',
        'date' => 'التاريخ',
        'amount' => 'المبلغ',
        'quantity' => 'الكمية',
        'cost' => 'التكلفة',
        'vendor' => 'المورّد',
        'address' => 'العنوان',
        'phone' => 'رقم الجوال',
        'code' => 'الرمز',
        'attachment' => 'المرفق',
        'document' => 'المستند',
        'file' => 'الملف',
        'image' => 'الصورة',
        'message' => 'الرسالة',
        'subject' => 'الموضوع',
        'content' => 'المحتوى',
        'reason' => 'السبب',
        'is_active' => 'حالة التفعيل',
        'approval_status' => 'حالة الاعتماد',

        // العقود والمشاريع
        'contract_no' => 'رقم العقد',
        'contract_date' => 'تاريخ العقد',
        'client_name' => 'اسم العميل',
        'main_contractor' => 'المقاول الرئيسي',
        'project_name' => 'اسم المشروع',
        'project_location' => 'موقع المشروع',
        'project_value' => 'قيمة المشروع',
        'project_duration' => 'مدة المشروع',
        'project_id' => 'المشروع',
        'expected_start_date' => 'تاريخ البداية المتوقع',
        'actual_start_date' => 'تاريخ البداية الفعلي',
        'expected_end_date' => 'تاريخ النهاية المتوقع',
        'contract_file' => 'ملف العقد',
        'payment_type' => 'طريقة الدفع',
        'full_payment_amount' => 'مبلغ الدفعة الكاملة',
        'first_payment_title' => 'اسم الدفعة الأولى',
        'first_payment_percentage' => 'نسبة الدفعة الأولى',
        'first_payment_amount' => 'مبلغ الدفعة الأولى',
        'first_payment_due_date' => 'تاريخ استحقاق الدفعة الأولى',

        // الموظفون والموارد البشرية
        'employee_id' => 'الموظف',
        'employee' => 'الموظف',
        'employee_number' => 'الرقم الوظيفي',
        'job_title' => 'المسمى الوظيفي',
        'department_id' => 'القسم',
        'department' => 'القسم',
        'factory_id' => 'المصنع',
        'manager_id' => 'المدير',
        'user_id' => 'المستخدم',
        'hire_date' => 'تاريخ التوظيف',
        'contract_start_date' => 'تاريخ بداية العقد',
        'contract_end_date' => 'تاريخ نهاية العقد',
        'salary' => 'الراتب',
        'housing_allowance' => 'بدل السكن',
        'transportation_allowance' => 'بدل المواصلات',
        'travel_allowance' => 'بدل السفر',
        'risk_allowance' => 'بدل المخاطر',
        'transfer_allowance' => 'بدل التنقل',
        'overtime_allowance' => 'بدل العمل الإضافي',
        'leave_balance' => 'رصيد الإجازات',
        'residency_expiry_date' => 'تاريخ انتهاء الإقامة',
        'passport_number' => 'رقم جواز السفر',
        'passport_expiry_date' => 'تاريخ انتهاء جواز السفر',
        'create_system_account' => 'إنشاء حساب نظام',
        'account_name' => 'اسم الحساب',
        'account_email' => 'البريد الإلكتروني للحساب',
        'account_password' => 'كلمة مرور الحساب',
        'account_role' => 'صلاحية الحساب',

        // الإجازات
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'days' => 'عدد الأيام',

        // البريد الإداري
        'recipients' => 'المستلمون',
        'message_type' => 'نوع الرسالة',

        // العهد المالية والفواتير
        'issued_at' => 'تاريخ الإصدار',
        'purchase_description' => 'وصف المشتريات',
        'amount_spent' => 'المبلغ المصروف',
        'settlement_date' => 'تاريخ التسوية',
        'invoice_day' => 'يوم الفاتورة',
        'invoice_month' => 'شهر الفاتورة',
        'invoice_year' => 'سنة الفاتورة',
        'supplier_name' => 'اسم المورّد',
        'supplier_tax_number' => 'الرقم الضريبي للمورّد',
        'classification' => 'التصنيف',
        'tax_amount' => 'مبلغ الضريبة',
        'lines' => 'البنود',

        // المشتريات والأصول
        'purchase_date' => 'تاريخ الشراء',
        'asset_type' => 'نوع الأصل',
        'vehicle_type' => 'نوع المركبة',
        'plate_number' => 'رقم اللوحة',
        'registration_number' => 'رقم التسجيل',
        'registration_expiry_date' => 'تاريخ انتهاء التسجيل',
        'inspection_expiry_date' => 'تاريخ انتهاء الفحص',
        'color' => 'اللون',
        'serial_number' => 'الرقم التسلسلي',

        // الرواتب
        'payroll_register_id' => 'سجل الرواتب',
        'adjustments' => 'التعديلات',
        'overtime_hours' => 'ساعات العمل الإضافي',
        'leave_deduction_days' => 'أيام خصم الإجازة',
        'other_deduction' => 'خصم آخر',
    ],

];
