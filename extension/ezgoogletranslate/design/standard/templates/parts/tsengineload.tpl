{**
  Template loaded by content/edit.tpl to load the js initialization

  @param $to_lang string (ez format)
  @param $from_lang (ez format)

  @todo move to ezjscore for loading google api
*}

<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("language", "1");
    var to_lang = '{$to_lang|extract_left( 2 )|wash(javascript)}';
    var from_lang = '{$from_lang|extract_left( 2 )|wash(javascript)}';
{literal}
    function translateElement( element, from, to )
    {
        google.language.translate( document.getElementById( element ).value, from, to,
            function( result ) {
                if ( !result.error ) {
                    document.getElementById( element ).value = result.translation;
                }
                else
                {
                    alert('{/literal}{'Error executing translation service'|i18n( 'extension/ezgoogletranslate' )}{literal}');
                }
            } )
    }
{/literal}
</script>