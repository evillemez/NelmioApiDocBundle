<?php

/*
 * This file is part of the NelmioApiDocBundle.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\ApiDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ApiDocController extends Controller
{
    public function indexAction()
    {
        $extractedDoc = $this->get('nelmio_api_doc.extractor.api_doc_extractor')->all();
        $htmlContent  = $this->get('nelmio_api_doc.formatter.html_formatter')->format($extractedDoc);

        return new Response($htmlContent, 200, array('Content-Type' => 'text/html'));
    }
    
    public function swaggerJsAction()
    {
        $extractedDoc = $this->get('nelmio_api_doc.extractor.api_doc_extractor')->all();
        
        //return new Response(sprintf("<pre>%s</pre>", print_r($extractedDoc, true)), 200, array('Content-Type' => 'text/html'));
        
        $jsonContent  = $this->get('nelmio_api_doc.formatter.swagger_formatter')->format($extractedDoc);

        return new Response($jsonContent, 200, array('Content-Type' => 'application/json'));
    }
}
