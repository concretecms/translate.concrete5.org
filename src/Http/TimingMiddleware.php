<?php
namespace Concrete5\Translate\Http;

use Concrete\Core\Http\Middleware\DelegateInterface;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;

class TimingMiddleware implements MiddlewareInterface
{
    /**
     * Process the request and return a response.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface $frame
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $start = microtime(true);
        $response = $frame->next($request);
        $end = microtime(true);

        if (!$response->headers->has('Content-Type') || strpos($response->headers->get('Content-Type'), 'text/html') === 0) {
            $body = $response->getContent();
            $body = str_replace('{{ RENDER_TOTAL }}', $this->getTimeString($start, $end), $body);
            $response->setContent($body);
        }

        return $response;
    }

    /**
     * Get the timing string.
     *
     * @param $start
     * @param $end
     *
     * @return string
     */
    private function getTimeString($start, $end)
    {
        // Rounded to two decimal places
        $ms = ceil(($end - $start) * 100000) / 100;

        return "{$ms}ms";
    }
}
