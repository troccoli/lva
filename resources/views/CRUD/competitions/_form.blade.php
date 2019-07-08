@textField([
'label' => __("Season"),
'fieldName' => 'season',
'required' => false,
'defaultValue' => isset($season) ? $season->getName() : '',
'disabled' => true
])

@hiddenField([
'fieldName' => 'season_id',
'required' => true,
'defaultValue' => isset($season) ? $season->getId() : '',
])

@textField([
'label' => __("Competition's name"),
'fieldName' => 'name',
'required' => true,
'defaultValue' => isset($competition) ? $competition->getName() : ''
])

@submitButton(['label' => $submitText])
