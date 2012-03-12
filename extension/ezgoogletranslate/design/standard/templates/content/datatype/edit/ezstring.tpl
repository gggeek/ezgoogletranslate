{default attribute_base='ContentObjectAttribute'
         html_class='full'}
{def $id = 'ezcoa-'}
{if ne( $attribute_base, 'ContentObjectAttribute' )}{set $id = $id|concat($attribute_base, '-')}{/if}
{set $id = $id|concat($attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier)}
<input id="{$id}" class="{eq( $html_class, 'half' )|choose( 'box', 'halfbox' )} ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" size="70" name="{$attribute_base}_ezstring_data_text_{$attribute.id}" value="{$attribute.data_text|wash( xhtml )}" />
{if $is_translating_content}
    {include uri='design:parts/tslink.tpl' id=$id attribute=$attribute}
{/if}
{undef $id}

{/default}

