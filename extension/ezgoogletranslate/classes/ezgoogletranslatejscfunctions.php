<?php
/**
 *
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2012
 * @license
 */

class eZGoogleTranslateJSCFunctions
{
    /**
    * We use POST for text, because there might be colons in it (and also it
    * could be longer than what browsers like for query strings)...
    * @param array $args [ string $lang_from, string $lang_to, string $provider = '' ]
    * @todo add support for translating an array of texts in a single call
    */
    static function translate( $args )
    {
        $http = eZHTTPTool::instance();
        $text = $http->postVariable('text');
        $provider = isset( $args[2] ) ? $args[2] : '';
        $ini = eZINI::instance( 'ezgoogletranslate.ini' );
        if ( $provider == '' || !in_array( $provider, $ini->variable( 'GeneralSettings', 'AvailableProviders' ) ) )
        {
            $provider =  $ini->variable( 'GeneralSettings', 'DefaultProvider' );
        }
        $from = self::getLanguageCode( $args[0], $provider );
        $to = self::getLanguageCode( $args[1], $provider );

        switch( $provider )
        {
            /// @see http://frengly.com/translationAPI
            case 'frengly':
                $url = 'http://www.syslang.com/frengly/controller?action=translateREST&' .
                    'username=' . urlencode( $ini->variable( 'FrenglyCredentials', 'Username' ) ) . '&' .
                    'password=' . urlencode( $ini->variable( 'FrenglyCredentials', 'Password' ) ) . '&' .
                    'src=' . $from . '&' .
                    'dest=' . $to . '&' .
                    'text=' . urlencode( $text );
                eZDebug::writeDebug( "Translation url: $url" , __METHOD__ );
                $text = file_get_contents( $url );
                // apparently there is no error code returned, only the number of translated parts
                $data = new SimpleXMLElement( $text );
                $hits = explode( '/', $data->stat );
                if ( $hits[0] > 0 )
                {
                    return (string)$data->translation[0];
                }
                break;

            /// @see http://code.google.com/apis/language/translate/v2/using_rest.html
            case 'googletranslate':
                $url = 'https://www.googleapis.com/language/translate/v2?' .
                    'key= ' . $ini->variable( 'GoogleCredentials', 'APIKey' ) .
                    /// @todo check if lang codes have to be translated
                    'source=' . $from . '&' .
                    'target=' . $to . '&' .
                    'format=text&' .
                    'q=' . urlencode( $text );
                eZDebug::writeDebug( "Translation url: $url" , __METHOD__ );
                $text = file_get_contents( $url );
                $data = json_decode( $text, true );
                /// @todo error checking
                if ( isset( $data['data']['translations']['translatedText'] ) )
                {
                    return $data['data']['translations']['translatedText'];
                }
                break;

            /// @see http://msdn.microsoft.com/en-us/library/ff512421.aspx
            case 'microsofttranslator':
                $url = 'http://api.microsofttranslator.com/v2/Http.svc/Translate?' .
                    'from=' . $from . '&' .
                    'to=' . $to . '&' .
                    'contentType=text/plain&' .
                    'text=' . urlencode( $text );
                eZDebug::writeDebug( "Translation url: $url" , __METHOD__ );
                $token = self::getMSAdmToken();
                if ( !$token )
                {
                     //throw new Exception( "Auth to Azure datamarket failed" );
                }
                $opts = array(
                    'http' => array(
                        'header'  => "Authorization: Bearer $token"
                    )
                );
                $context = stream_context_create( $opts );
                // ex. format: <string xmlns="http://schemas.microsoft.com/2003/10/Serialization/">etc</string>
                $text = file_get_contents( $url, false, $context );
                /// @todo error checking?
                $data = simplexml_load_string( $text );
                foreach( (array)$data[0] as $val )
                {
                    $text = $val;
                }
                return $text;

            /// @see http://mymemory.translated.net/doc/spec.php
            case 'mymemory':
                $url = 'http://mymemory.translated.net/api/get?' .
                    'langpair=' . $from . '|' . $to . '&' .
                    'q=' . urlencode( $text );
                eZDebug::writeDebug( "Translation url: $url" , __METHOD__ );
                if ( $ini->variable( 'MymemoryCredentials', 'User' ) != '' )
                {
                        $url .= '&user=' . urlencode( $ini->variable( 'MymemoryCredentials', 'User' ) ) . '&key=' . urlencode( $ini->variable( 'MymemoryCredentials', 'Key' ) );
                }
                eZDebug::writeDebug( "Translation url: $url" , __METHOD__ );
                $text = file_get_contents( $url );
                $data = json_decode( $text, true );
                if ( isset( $data['responseData']['translatedText'] ) )
                {
                    if ( !isset( $data['responseStatus'] ) || $data['responseStatus'] != 200 )
                    {
                        throw new Exception( $data['responseDetails']  );
                    }
                    return $data['responseData']['translatedText'];
                }
                break;

            default:
                eZDebug::writeError( "Translation provider '$provider' not supported", __METHOD__ );
                throw new Exception( "Translation provider '$provider' not supported"  );
        }
        return '';
    }

    /**
     * Converts eZ-style lang codes (eg. eng-GB) to the format used by the given provider
     */
    static function getLanguageCode( $locale, $provider )
    {
        switch ( $provider )
        {
            /// @see code.google.com/apis/language/translate/v2/using_rest.html#supported-WorkingResults
            case 'googletranslate':
                switch( $locale )
                {
                    case 'chi-CN':  return 'zh-CN';
                    case 'chi-HK':  return 'zh-TW';
                }
                // fall through voluntarily to ISO 639-1 (TAKE CARE ABOUT MS BELOW)

            /// @see http://msdn.microsoft.com/en-us/library/hh456380.aspx
            case 'microsofttranslator':
                switch( $locale )
                {
                    case 'chi-CN':  return 'zh-CHS';
                    case 'chi-HK':  return 'zh-CHT ';
                }
                // fall through voluntarily to ISO 639-1

            // mymemory, frengly use (apparently) ISO 639-1

            /// @todo finish conversion list for all langs where 2-letter code is not the beginning of 3-letter code
            ///       (see http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes)
            ///       right now it is complete up to letter "b"
            default:
                switch( substr( $locale, 0, 3 ) )
                {
                    /// @todo !important move data to an array
                    case 'alb': return 'sq';
                    case 'arg': return 'an';
                    case 'arm': return 'hy';
                    case 'ave': return 'ae';
                    case 'bam': return 'bm';
                    case 'baq': return 'eu';
                    case 'ben': return 'bn';
                    case 'bih': return 'bh';
                    case 'bjn': return 'bjn';
                    case 'bos': return 'bs';
                    case 'bul': return 'bg';
                    case 'bur': return 'my';

                    case 'cze': return 'cs';
                    case 'dut': return 'nl';
                    case 'ger': return 'de';
                    case 'jpn': return 'ja';
                    case 'pol': return 'pl';
                    case 'por': return 'pt';
                    case 'swe': return 'sv';
                    case 'chi':  return 'zh';

                    default:
                        return substr( $locale, 0, 2 );
                }
        }
    }

    /// @see http://msdn.microsoft.com/en-us/library/hh454950.aspx
    /// @todo cache this for no longer than 10 minutes
    protected static function getMSAdmToken()
    {
        if ( !in_array( 'https', stream_get_wrappers() ) )
        {
            eZDebug::writeError( "HTTPS stream wrapper not installed, can not get MS Auth token", __METHOD__ );
            return null;
        }

        $ini = eZINI::instance( 'ezgoogletranslate.ini' );
        $url = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/';
        $opts = array(
                   'http' => array(
                       'method'  => 'POST',
                       'header'  => 'Content-type: application/x-www-form-urlencoded',
                       'content' => http_build_query( array(
                           'client_id' => $ini->variable( 'MicrosoftCredentials', 'ClientId' ),
                           'client_secret' => $ini->variable( 'MicrosoftCredentials', 'ClientSecret' ),
                           'scope' => 'http://api.microsofttranslator.com',
                           'grant_type' => 'client_credentials'
                       ) )
                   )
               );
        $context = stream_context_create( $opts );
        $text = file_get_contents( $url, false, $context );
        $data = json_decode( $text, true );
        if ( isset( $data['access_token'] ) )
        {
            return $data['access_token'];
        }
        else
        {
            return null;
        }
    }
}

?>