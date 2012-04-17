/**
 * This piece of code depends on jQuery and eZJSCore ( jQuery.ez() plugin ).
 *
 * @author
 * @copyright
 * @license
 */

/**
 * The callback function takes 1 parameter: the translated text string
 * @todo add a failure callback
 */
window.eztranslate = function( text, from, to, callback, provider )
{
    if ( text != '' )
    {
        var url = 'googletranslate::translate::' + from + '::' + to;
        if ( provider != undefined )
        {
            url = url + '::' + provider;
        }
        jQuery.ez(
            url,
            /// @todo test: shall we urlencode this using encodeURIComponent?
            { "text": text },
            function( data )
            {
                if ( data && data.content !== '' )
                {
                    /// @todo
                    callback( data.content );
                }
                else
                {
                    /// @todo log error to js consoles
                    //alert( data.error_text );
                }
            }
        );
    }
}
