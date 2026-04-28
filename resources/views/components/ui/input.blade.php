@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => 'flex w-full rounded-2xl border-gray-100 bg-gray-50/50 px-4 py-3 text-base ring-offset-white file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-200 focus:border-brand-300 disabled:cursor-not-allowed disabled:opacity-50 transition-all duration-200']) }}>
