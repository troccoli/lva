@textField([
'label' => __("Season's year"),
'fieldName' => 'year',
'required' => true,
'defaultValue' => isset($season) ? $season->getYear() : ''
])

@submitButton(['label' => $submitText])
