<?php
/**
* websocket client class
*/
class Client
{

	private $id;
	private $socket;
	private $handshake;
	private $pid;
	private $name;
	private $oclient = 'gen';

	public $filename;
	public $file;
	public $fu_ip;

	function Client($id, $socket) {
		$this->id = $id;
		$this->socket = $socket;
		$this->handshake = false;
		$this->pid = null;
	}

	public function getId() {
		return $this->id;
	}

	public function getSocket() {
		return $this->socket;
	}

	public function getHandshake() {
		return $this->handshake;
	}

	public function getName() {
		return $this->name;
	}

	public function getOClient()
	{
		return $this->oclient;
	}

	public function getPid() {
		return $this->pid;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setSocket($socket) {
		$this->socket = $socket;
	}

	public function setHandshake($handshake) {
		$this->handshake = $handshake;
	}

	public function setPid($pid) {
		$this->pid = $pid;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setOClient($value) {
		$this->oclient = $value;
	}

}
//
?>