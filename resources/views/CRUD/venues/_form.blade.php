@textField([
'label' => __("Venue's name"),
'fieldName' => 'name',
'required' => true,
'defaultValue' => isset($venue) ? $venue->getName() : ''
])

@submitButton(['label' => $submitText])
