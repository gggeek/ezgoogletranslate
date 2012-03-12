{**
  Template loaded by content/edit.tpl to load the js initialization

  @param $to_lang string (ez format)
  @param $from_lang (ez format)
*}

{def $preferred_lib = ezini( 'eZJSCore', 'PreferredLibrary', 'ezjscore.ini' )}
{if array( 'yui3', 'jquery' )|contains( $preferred_lib )|not()}
    {* Prefer jQuery if something else is used globally, since it's smaller then yui3. *}
    {set $preferred_lib = 'jquery'}
{/if}
{ezscript_require( array( concat( 'ezjsc::', $preferred_lib ), concat( 'ezjsc::', $preferred_lib, 'io' ), concat( 'ezgoogletranslate_', $preferred_lib, '.js' ) ) )}
{undef $preferred_lib}

{run-once}
<script type="text/javascript">
    var from_lang = '{$from_lang|wash(javascript)}';
    var to_lang = '{$to_lang|wash(javascript)}';
{literal}
    function translateElement( element, from, to )
    {
        if ( from == undefined )
        {
            from = from_lang;
        }
        if ( to == undefined )
        {
            to = to_lang;
        }
        /// @todo test if element exists
        eztranslate( document.getElementById( element ).value, from, to,
            function( result )
            {
                if ( result != '' )
                {
                    document.getElementById( element ).value = result;
                }
            } );
    }
{/literal}
</script>
{/run-once}
