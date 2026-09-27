<li class="tree-item mb-2">
    <div
        class="account-box shadow-sm d-flex justify-content-between align-items-center p-2 border rounded bg-white hover-effect">
        <div class="account-info d-flex align-items-center gap-2">

            {{-- لو الحساب ده تحته فروع، حط سهم يفتح ويقفل --}}
            @if ($account->children && $account->children->isNotEmpty())
                <i class="bx bx-chevron-down toggle-icon cursor-pointer fs-5 text-dark"></i>
                <i class="bx bxs-folder-open text-warning fs-4"></i>
            @else
                <i class="bx bx-minus text-muted ms-1" style="visibility: hidden;"></i>
                <i class="bx bx-file-blank text-secondary fs-5"></i>
            @endif

            <span class="account-code fw-bold text-primary ms-1">{{ $account->account_code ?? '---' }}</span>
            <span class="account-name fw-semibold text-dark">
                {{ app()->getLocale() == 'ar' ? $account->name_ar : $account->name_en ?? $account->name_ar }}
            </span>

            {{-- بادج لنوع الحساب (مصفوفة ألوان ذكية) --}}
            @php
                $typeColors = [
                    'asset' => 'primary',
                    'liability' => 'warning',
                    'equity' => 'info',
                    'revenue' => 'success',
                    'expense' => 'danger',
                ];
                $typeColor = $typeColors[$account->account_type] ?? 'secondary';
            @endphp
            <span
                class="badge bg-{{ $typeColor }} bg-opacity-10 text-{{ $typeColor }} border border-{{ $typeColor }} ms-2">
                {{ __('accounting.' . $account->account_type) ?? $account->account_type }}
            </span>
        </div>

        <div class="account-actions d-flex align-items-center gap-3">
            {{-- عرض الرصيد بـ 3 أرقام عشرية للريال العماني --}}
            <span class="account-balance fw-bold {{ $account->balance < 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format($account->balance, 3) }} ر.ع
            </span>

            <div class="btn-group" role="group">
                {{-- 🔥 زرار كشف الحساب (Ledger) 🔥 --}}
                @can('view_journal_entries')
                    <a href="{{ route('admin.financial-accounts.ledger', $account->id) }}"
                        class="btn btn-sm btn-light border text-dark" title="عرض كشف الحساب">
                        <i class="bx bx-list-ul"></i>
                    </a>
                @endcan

                {{-- 🔥 زرار إضافة حساب فرعي من هذا الحساب 🔥 --}}
                @can('create_financial_accounts')
                    <button type="button" class="btn btn-sm btn-light border text-success add-sub-account-btn"
                        data-bs-toggle="modal" data-bs-target="#addAccountModal" data-parent-id="{{ $account->id }}"
                        title="إضافة حساب فرعي">
                        <i class="bx bx-plus"></i>
                    </button>
                @endcan

                {{-- 🔥 زرار التعديل السحري مغلف بالصلاحية المصححة 🔥 --}}
                @can('edit_financial_accounts')
                    <button type="button" class="btn btn-sm btn-light border text-primary edit-account-btn"
                        data-bs-toggle="modal" data-bs-target="#editAccountModal" data-id="{{ $account->id }}"
                        data-code="{{ $account->account_code }}" data-name="{{ $account->name_ar }}"
                        data-type="{{ $account->account_type }}" data-balance="{{ $account->balance }}"
                        title="تعديل الحساب">
                        <i class="bx bx-edit"></i>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- الاستدعاء الذاتي (Recursive) مع مسافة بادئة بصرية --}}
    @if ($account->children && $account->children->isNotEmpty())
        {{-- إضافة ps-4 و border-start لرسم خطوط الشجرة بشكل احترافي --}}
        <ul class="tree-list mt-2 ps-4 border-start ms-3" style="list-style-type: none;">
            @foreach ($account->children as $childAccount)
                {{-- ملحوظة: تأكد إن المسار ده متطابق مع مكان الملف عندك --}}
                @include('backend.accounting.partials.account_node', ['account' => $childAccount])
            @endforeach
        </ul>
    @endif
</li>
