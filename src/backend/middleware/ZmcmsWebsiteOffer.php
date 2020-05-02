<?php
namespace Zmcms\WebsiteOffer\Backend\Middleware;
use Closure;use Session;use URL;class ZmcmsWebsiteOffer
{
	public function handle($request, Closure $next){
		return $next($request);
	}
}
