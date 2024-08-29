@props(['disabled' => false, 'options', 'placeholder' => "Select your option"])

<select required {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => 'appearance-none invalid:text-gray-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) !!}>
    <option value="">{{ $placeholder }}</option>
    @foreach($options as $option)
        <option value="{{ $option->getKey() }}">{{ $option->getName() }}</option>
    @endforeach
</select>
