<?php

class KoronaApi 
{
	const URL = "https://www.koronacloud.com/api";

	/**
	 * get the token
	 * Please be aware that this mathod can only be called once per api key. You need to store the returned token in a secured place.
	 * @return (string) token
         */
	public static function auth($appId, $secret, $apiKey)
	{
		$token = file_get_contents(self::URL."/v1/auth/$appId/$secret/$apiKey");

		return $token;
	}

	private $token;

	public function __construct($token) 
	{
		if (!isset($token) || empty($token))
			throw new Exception("Missing token");

		if (strlen($token) < 10)
			throw new Exception("Invalid token");

		$this->token = $token;	
	}

	public function getObjByNumber($type, $number)
	{
		$content = file_get_contents(self::URL."/v1/".$this->token."/".$type."/number/$number");
		$result = json_decode($content);

		if (isset($result->error))
			throw new Exception($result->error);

		return $result->result;
	}

	public function getObjById($type, $uuid)
	{
		$content = file_get_contents(self::URL."/v1/".$this->token."/".$type."/id/$uuid");
		$result = json_decode($content);

		if (isset($result->error))
			throw new Exception($result->error);

		return $result->result;
	}
	
	public function getUpdates($type, $revision, $limit, $offset)
	{
		$content = file_get_contents(self::URL."/v1/".$this->token."/".$type."/updates/$revision/$limit/$offset");
		$result = json_decode($content);

		if (isset($result->error))
			throw new Exception($result->error);

		return $result->resultList;
	}

	public function save($type, $obj)
	{
		return $this->post($type."/save/", $obj);
	}

	public function post($path, $obj)
	{
		$content = json_encode($obj);
		$options = array(
    				'http' => array(
        			'header'  => "Content-type: application/json\r\n",
        			'method'  => 'POST',
        			'content' => $content,
    				),
			);
		$context  = stream_context_create($options);
		$content = file_get_contents(self::URL."/v1/".$this->token."/".$path, false, $context);
		$result = json_decode($content);
		if (isset($result->error))
			throw new Exception($result->error);

		return $result->result;
	}

}


?>
