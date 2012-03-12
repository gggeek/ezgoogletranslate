{default attribute_base='ContentObjectAttribute'
         html_class='full'}
{def $id = 'ezcoa-'}
{if ne( $attribute_base, 'ContentObjectAttribute' )}{set $id = $id|concat($attribute_base, '-')}{/if}
{set $id = $id|concat($attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier)}
<textarea id="{$id}" cols="70" rows="{$attribute.contentclass_attribute.data_int1}">{$attribute.content|wash}</textarea>
{if $is_translating_content}
    {include uri='design:parts/tslink.tpl' id=$id attribute=$attribute}
{/if}
{undef $id}

{/default}
