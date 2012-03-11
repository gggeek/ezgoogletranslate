/**
 * This piece of code depends on YUI 3.0 and eZJSCore ( Y.io.ez() plugin ).
 *
 * @author
 * @copyright
 * @license
 */

YUI( YUI3_config ).use('node', 'event', 'io-ez', function( Y )
{
    /**
     */
    window.eztranslate = function( text, from, to, callback, provider )
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
                            var data = o.responseJSON.content;
                            /// @todo
                        }
                        else
                        {
                            alert( o.responseJSON.error_text );
                        }
                    }
                }
            }
        );
        return false;
    }
} );