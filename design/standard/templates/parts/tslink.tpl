{**
  Adds the "translate" link to editing inputs

  @param string $id the html element id of the input field
  @param ezontentobjectattribute $attribute
*}

<a id="tr-{$id}" href="#" onclick="translateElement('{$id}'); return false;">{'Translate'|i18n('ezgoogletranslate')}</a>
