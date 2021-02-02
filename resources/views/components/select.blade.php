<select {{ $attributes->merge(['class' => 'py-2 pl-3 pr-8 border-gray-300 rounded focus:ring-green-500 focus:border-green-500 sm:text-sm']) }}>
    {{ $slot }}
</select>
