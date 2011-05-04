<?php

	class phpAutomationClient {
		private $url;
		private $session;
		
		public function phpAutomationCLient($url = 'http://localhost:8080/nuxeo/site/automation'){
			$this->url = $url;
			$this->session = NULL;
		}
		
		public function getSession($username = 'Administrator', $password = 'Administrator'){
			$this->session = $username . ":" . $password;
			$session = new Session($this->url, $this->session);
			return $session;
		}
	}
	
	class Session {
		
		private $urlLoggedIn;
		private $headers;
		private $requestContent;
		
		public function Session( $url, $session, $headers = "Content-Type:application/json+nxrequest") {
			$this->urlLoggedIn = str_replace("http://", "", $url);
			$this->urlLoggedIn = "http://" . $session . "@" . $this->urlLoggedIn;
			$this->headers = $headers;
		}
		
		public function newRequest($requestType){
			$newRequest = new Request($this->urlLoggedIn, $this->headers, $requestType);
			return $newRequest;
		}
	}
	
	class Request {
		
		private $finalRequest;
		private $url;
		private $headers;
		private $method;
		private $iterationNumber;
		private $HEADER_NX_SCHEMAS;
		private $blob;
		
		public function Request($url, $headers = "Content-Type:application/json+nxrequest", $requestId) {
			$this->url = $url . "/" . $requestId;
			$this->headers = $headers;
			$this->finalRequest = '{}';
			$this->method = 'POST';
			$this->iterationNumber = 0;
			$this->HEADER_NX_SCHEMAS = 'X-NXDocumentProperties:';
		}
		
		public function setSchema($schema = '*'){
			$this->headers = array($this->headers, $this->HEADER_NX_SCHEMAS . $schema);
			return $this;
		}
		
		public function Set($requestType, $requestContentOrVarName, $requestVarVallue =  NULL){
				
				if ($requestVarVallue !== NULL){
					if ($this->iterationNumber === 0){
						$this->finalRequest = array(
		  							$requestType=> array( $requestContentOrVarName => $requestVarVallue)
		  				);
					}else if ($this->iterationNumber === 1) {
						$this->finalRequest[$requestType] = array($requestContentOrVarName => $requestVarVallue);
					}else if ($this->iterationNumber === 2){
						$this->finalRequest[$requestType][$requestContentOrVarName] = $requestVarVallue;
					}
					
					$this->iterationNumber = 2;
				}else{
					if ($this->iterationNumber === 0){
						$this->finalRequest = array(
		  							$requestType=> $requestContentOrVarName
		  				);
					}else{
						$this->finalRequest[$requestType] = $requestContentOrVarName;
					}
					
					if ($this->iterationNumber === 0)
						$this->iterationNumber = 1;
				}
  				
  				return $this;
		}
		
		public function Send_request(){
			
			//print_r($this->finalRequest);
			
			$this->finalRequest = json_encode($this->finalRequest);
			
			$this->finalRequest = str_replace('\/', '/', $this->finalRequest);
			
			echo 'print'.  $this->finalRequest;
			
			$params = array('http' => array(
			              'method' => $this->method,
			              'content' => $this->finalRequest
			            ));
			  if ($this->headers !== null) {
			    $params['http']['header'] = $this->headers;
			  }
			  
			  $ctx = stream_context_create($params);
			  
			  $fp = @fopen($this->url, 'rb', false, $ctx);
			  
			  $answer = @stream_get_contents($fp);

			  $answer = json_decode($answer, true);
			  
			  return $answer;
		}
	}
	
	class Document extends Documents{
		
		Private $object;
		Private $properties;
		
		Public function Document ($newDocument = NULL) {
			$this->object = $newDocument;
			if (array_key_exists('properties', $this->object))
				$this->properties = $this->object['properties'];
			else
				$this->properties = null;				
		}
			
		Public function affichage(){
			$value = sizeof($this->object);
			for ($test = 0; $test <$value; $test++){
				echo key($this->object) . ' ' . current($this->object) . '<br />';
				next($this->object);
			}
			
			if ($this->properties !== NULL){
				$value = sizeof($this->properties);
				for ($test = 0; $test <$value; $test++){
					echo key($this->properties) . ' ' . current($this->properties) . '<br />';
					next($this->properties);
				}
			}
		}
		
		public function get($schemaNamePropertyName){
			if (array_key_exists($schemaNamePropertyName, $this->properties)){
				echo key($this->properties) .  ' ' . current($this->properties) . '<br />';
			}
		}
	}
	
	class Documents {
		private $documentsList;
		
		public function Documents($newDocList){
			$this->documentsList = null;
			$test = 'ahahahaha';
			if (!empty($newDocList['entries'])){
				while (false !== $test) {
					$this->documentsList[] = new Document(current($newDocList['entries']));
					$test = each($newDocList['entries']);
				}
				$test = sizeof($this->documentsList);
				unset($this->documentsList[$test-1]);
			}
			else{
				echo 'file not found';
			}
		}
		
		public function affichage(){
			$value = sizeof($this->documentsList);
			for ($test = 0; $test < $value; $test ++){
				current($this->documentsList)->affichage();
				next($this->documentsList);
				echo '<br />';
			}
		}
	}
	
	
	
?>