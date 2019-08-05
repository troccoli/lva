@textField([
'label' => __("Competition"),
'fieldName' => 'competition',
'required' => false,
'defaultValue' => isset($competition) ? $competition->getName() . ' ' . $competition->getSeason()->getName() : '',
'disabled' => true
])

@hiddenField([
'fieldName' => 'competition_id',
'required' => true,
'defaultValue' => isset($competition) ? $competition->getId() : '',
])

@textField([
'label' => __("Division's name"),
'fieldName' => 'name',
'required' => true,
'defaultValue' => isset($division) ? $division->getName() : ''
])

@textField([
'label' => __("Division's display order"),
'fieldName' => 'display_order',
'required' => true,
'defaultValue' => isset($division) ? $division->getOrder() : 1
])

@submitButton(['label' => $submitText])
