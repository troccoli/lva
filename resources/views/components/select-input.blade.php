@props(['disabled' => false, 'options', 'currentOption' => '', 'placeholder' => "Select your option"])

<select required {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => 'appearance-none invalid:text-gray-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) !!}>
    @empty($currentOption)
    <option value="">{{ $placeholder }}</option>
    @endempty
    @foreach($options as $option)
        <option
            value="{{ $option->getKey() }}"
            {{ !empty($currentOption) && $option->getKey() === $currentOption ? 'selected' : '' }}
        >
            {{ $option->getName() }}
        </option>
    @endforeach
</select>
