@textField([
'label' => __("Season's year"),
'fieldName' => 'year',
'required' => true,
'defaultValue' => $season->name ?? ''
])

@submitButton(['label' => $submitText])
