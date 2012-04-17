/**
 * This piece of code depends on YUI 3.0 and eZJSCore ( Y.io.ez() plugin ).
 *
 * @author
 * @copyright
 * @license
 */

YUI( YUI3_config ).use( 'node', 'event', 'io-ez', function( Y )
{
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
            Y.io.ez(
                url,
                {
                    "data": "text=" + encodeURIComponent( text ),
                    "on": {
                        success: function( id, o )
                        {
                            if ( o.responseJSON && o.responseJSON.content !== '' )
                            {
                                callback( o.responseJSON.content );
                            }
                            else
                            {
                                /// @todo log error to js consoles
                                //alert( o.responseJSON.error_text );
                            }
                        }
                    }
                }
            );
        }
    }
} );