<?php
    class CAD_WebTools
    {

        public function getUrlContents($url)
        {
            $crl = curl_init();

            $timeout = 5;
            curl_setopt ($crl, CURLOPT_URL,$url);
            curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
            $ret = curl_exec($crl);
            curl_close($crl);

            return $ret;
        }

        public function getUrlZendObject($url)
        {
            $sock = fsockopen($url, 80);
            $req = "GET / HTTP/1.1\r\n" .
                "Host: " . $url . "\r\n" .
                "Connection: close\r\n" .
                "\r\n";

            $str = '';
            fwrite($sock, $req);
            while ($buff = fread($sock, 1024))
            {
                $str .= $buff;
            }
            $response = Zend_Http_Response::fromString($str);

            return $response;
        }

        public function getContent($url, $timeout=60)
        {
            $client = new Zend_Http_Client($url, array('timeout' => $timeout));

            $content = $client->request();

            if($content->isError())
            {
                return null;
            }
            // 		return $content->getRawBody();
            return $content->getBody();
        }

        public function getElementsByClassName(DOMDocument $DOMDocument, $ClassName)
        {
            $Elements = $DOMDocument -> getElementsByTagName("*");
            $Matched = array();

            foreach($Elements as $node)
            {
                if( ! $node -> hasAttributes())
                    continue;

                $classAttribute = $node -> attributes -> getNamedItem('class');

                if( ! $classAttribute)
                    continue;

                $classes = explode(' ', $classAttribute -> nodeValue);

                if(in_array($ClassName, $classes))
                    $Matched[] = $node;
            }

            return $Matched;
        }

    }
