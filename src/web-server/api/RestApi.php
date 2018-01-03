<?php

namespace Api;

class RestApi extends \Scrollio\Api\AbstractApi
{
	protected $endpoint_namespace = 'Service\\';

	public function __construct($request, $origin)
	{
		parent::__construct($request);
	}
}
