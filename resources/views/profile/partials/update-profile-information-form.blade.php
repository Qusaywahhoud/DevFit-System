<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
            معلومات الحساب
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            قم بتحديث معلومات ملفك الشخصي وعنوان بريدك الإلكتروني.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('الاسم')" class="text-gray-700 dark:text-gray-200 font-bold" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-300 dark:border-gray-700 focus:border-blue-500 dark:focus:border-blue-400 rounded-xl" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('البريد الإلكتروني')" class="text-gray-700 dark:text-gray-200 font-bold" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-300 dark:border-gray-700 focus:border-blue-500 dark:focus:border-blue-400 rounded-xl" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        عنوان بريدك الإلكتروني غير موثق.

                        <button form="send-verification" class="underline text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 rounded-md focus:outline-none">
                            انقر هنا لإعادة إرسال رسالة التحقق.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-given')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            تم إرسال رابط تحقق جديد إلى عنوان بريدك الإلكتروني.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-md">
                حفظ التغييرات
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 dark:text-green-400 font-bold"
                >تم الحفظ بنجاح.</p>
            @endif
        </div>
    </form>
</section>