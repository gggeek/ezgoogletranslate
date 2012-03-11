/**
 * This piece of code depends on jQuery and eZJSCore ( jQuery.ez() plugin ).
 *
 * @author
 * @copyright
 * @license
 */

function eztranslate( text, from, to, callback, provider )
{
    var url = 'googletranslate::translate::' + from + '::' + to;
    if ( provider != undefined )
    {
        url = url + '::' + provider;
    }
    jQuery.ez(
        url,
        /// @todo test: shall we urlencode this?
        { "text": text },
        function( data )
        {
            if ( data && data.content !== '' )
            {
                alert( data.content );
                /// @todo
            }
            else
            {
                alert( data.content.error_text );
            }
        }
    );
    return false;
}
