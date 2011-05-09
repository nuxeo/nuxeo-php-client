<?php

	/**
 	* phpAutomationClient class
 	*
 	* Class which initialise the php client whith an URL
 	* 
 	* @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 	*/
	class PhpAutomationClient {
		private $url;
		
		public function PhpAutomationCLient($url = 'http://localhost:8080/nuxeo/site/automation'){
			$this->url = $url;
		}
		
		/**
	 	* getSession function
	 	*
	 	* Open a session from a phpAutomatioClient
	 	* 
	 	* @var        $username : username for your session
	 	* 			  $password : password matching the usename
	 	* @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
	 	*/
		public function GetSession($username = 'Administrator', $password = 'Administrator'){
			$this->session = $username . ":" . $password;
			$session = new Session($this->url, $this->session);
			return $session;
		}
	}
	
	/**
 	* Session class
 	*
 	* Class which stocks username,password, and open requests
 	* 
 	* @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 	*/
	class Session {
		
		private $urlLoggedIn;
		private $headers;
		private $requestContent;
		
		public function Session( $url, $session, $headers = "Content-Type:application/json+nxrequest") {
			$this->urlLoggedIn = str_replace("http://", "", $url);
			if (strpos($url,'https') !== false){
				$this->urlLoggedIn = "https://" . $session . "@" . $this->urlLoggedIn;
			}elseif(strpos($url,'http') !== false){
				$this->urlLoggedIn = "http://" . $session . "@" . $this->urlLoggedIn;
			}else{
				echo 'that is a strange url .......';
				exit;
			}
			$this->headers = $headers;
		}
		
		/**
	 	* newRequest function
	 	*
	 	* Create a request from a session
	 	* 
	 	* @var        $requestType : type of request you want to execute (such as Document.Create 
	 	* 			  for exemple)
	 	* @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
	 	*/
		public function NewRequest($requestType){
			$newRequest = new Request($this->urlLoggedIn, $this->headers, $requestType);
			return $newRequest;
		}
	}
	
	/**
 	* Request class
 	*
 	* Request class contents all the functions needed to initialise a request and send it
 	* 
 	* @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 	*/
	class Request {
		
		private $finalRequest;
		private $url;
		private $headers;
		private $method;
		private $iterationNumber;
		private $HEADER_NX_SCHEMAS;
		private $blobList;
		private $X_NXVoidOperation;
		
		
		public function Request($url, $headers = "Content-Type:application/json+nxrequest", $requestId) {
			$this->url = $url . "/" . $requestId;
			$this->headers = $headers;
			$this->finalRequest = '{}';
			$this->method = 'POST';
			$this->iterationNumber = 0;
			$this->HEADER_NX_SCHEMAS = 'X-NXDocumentProperties:';
			$this->blobList = null;
			$this->X_NXVoidOperation = 'X-NXVoidOperation: true';
		}
		
		/**
		 * Set X-NXVoidOperation header
		 * 
		 * This header is used for the blob upload, it's noticing if the blob must be send back to the
		 * client. If not used, i might be great to not using it because it will save time and connection
		 * cappacity
		 *
		 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
		 */
		public function SetX_NXVoidOperation($headerValue = '*'){
			$this->X_NXVoidOperation = 'X-NXVoidOperation:'. $headerValue;
		}
		
		/**
 		* SetSchema function
 		*
 		* Set the schemas in order to obtain file properties
 		* 
 		* @var		  $schema : name the schema you want to obtain
 		* @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 		*/
		public function SetSchema($schema = '*'){
			$this->headers = array($this->headers, $this->HEADER_NX_SCHEMAS . $schema);
			return $this;
		}
		
		/**
 		* Set function
 		*
 		* This function is used to load data in the request (such as input, context and params fields)
 		* 
 		* @var		  $requestType : contents name of the field
 		* 			  $requestContentOrVarName : contents the name of the var or the content of the field 
 		* 										 in the case of an input field
 		* 			  $requestVarVallue : vallue of the var define in $requestContentTypeOrVarName(if needed)
 		* @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 		*/
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
		
		/**
 		 * SinglePart function
 		 * 
 		 * Function used to send a singlepart request, such as queries, or create doc function
 		 * 
 		 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 		 */
		private function SinglePart(){
			
			if($this->blob){
				$this->finalRequest['input'] = 'blob: ' . $this->blob;
			}
			
			$this->finalRequest = json_encode($this->finalRequest);
			
			
			$this->finalRequest = str_replace('\/', '/', $this->finalRequest);
			
			print_r(json_decode($this->finalRequest, true));
			
			echo '<br /><br />requete : ' . $this->finalRequest . '<br /> <br />';
						
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
		
		/**
 		* MultiPart function
 		* 
 		* This function is used to send a multipart request (blob + request) to Nuxeo EM, such as the
 		* AttachBlob request
 		* 
 		* @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 		*/
		private function MultiPart(){
			
			if (sizeof($this->blobList) >= 1 AND !isset($this->finalRequest['params']['xpath']))
				$this->finalRequest['params']['xpath'] = 'files:files';
			
			$this->finalRequest = json_encode($this->finalRequest);
			
			$this->finalRequest = str_replace('\/', '/', $this->finalRequest);
						
			$this->headers = array($this->headers, 'Content-ID: request');
			
			$requestheaders = 'Content-Type: application/json+nxrequest; charset=UTF-8'."\r\n".
							  'Content-Transfer-Encoding: 8bit'."\r\n".
							  'Content-ID: request'."\r\n".
							  'Content-Length:'.strlen($this->finalRequest)."\r\n"."\r\n";
			
			$value = sizeof($this->blobList);
							
			$boundary = '====Part=' . time() . '='.(int)rand(0, 1000000000). '===';
			
			$data = "--" . $boundary . "\r\n" .
	     			$requestheaders . 
	     			$this->finalRequest . "\r\n" ."\r\n";
			
			for ($cpt = 0; $cpt < $value; $cpt++){
				
				$data = $data . "--" . $boundary . "\r\n" ;
				
				$blobheaders = 'Content-Type:'.$this->blobList[$cpt][1]."\r\n".
						       'Content-ID: input'. "\r\n" .
				               'Content-Transfer-Encoding: binary'."\r\n" .
				       	       'Content-Disposition: attachment;filename='. $this->blobList[$cpt][0].
				       	       "\r\n" ."\r\n";
				
				$data = "\r\n". $data .
	                	$blobheaders.
	                	$this->blobList[$cpt][2] . "\r\n"."\r\n";
			}
			
			$data = $data ."--" . $boundary."--";
			
			echo 'data: ' . $data;
			
            $final = array('http'=> array(
            					'method' => 'POST',
            					'content' => $data));
            
			$final['http']['header'] = 'Accept: application/json+nxentity, */*'. "\r\n".
	                				   'Content-Type: multipart/related;boundary="'.$boundary.
	                				   '";type="application/json+nxrequest";start="request"'. "\r\n". $this->X_NXVoidOperation;
			
            $final = stream_context_create($final);
            
            $fp = @fopen($this->url, 'rb', false, $final);
            
            $answer = @stream_get_contents($fp);

			$answer = json_decode($answer, true);
			  
			return $answer;
    	}
    	
    	public function LoadBlob($adresse, $contentType  = 'application/binary'){
    		if(!$this->blobList){
    			$this->blobList = array();
    		}
    		$eadresse = explode("/", $adresse);
    		
    		$fp = fopen($adresse, "r");
    		
    		if (!$fp)
				echo 'error loading the file';

			$futurBlob = stream_get_contents($fp);
    		$this->blobList[] = array(end($eadresse), $contentType, print_r($futurBlob, true));
    		
    		return $this;
    	}
    	
    	/**
 		* Send_request function
 		*
 		* This function is used to send any kind of request to Nuxeo EM
 		*
 		* @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
 		*/
		public function SendRequest(){
			if (!$this->blobList)
				$this->SinglePart();
			else
				$this->MultiPart();
		}
		
	}
	
	/**
	 * Document class
	 *
	 * hold a return document
	 * 
	 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
	 */
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
			
		Public function Output(){
			$value = sizeof($this->object);
			
			for ($test = 0; $test <$value; $test++){
				echo '<td>' . key($this->object) . ' : ' . current($this->object) . '</td>';
				next($this->object);
			}
			
			if ($this->properties !== NULL){
				$value = sizeof($this->properties);
				for ($test = 0; $test <$value; $test++){
					echo '<td>' . key($this->properties) . ' : ' . current($this->properties) . '</td>';
					next($this->properties);
				}
			}
		}
		
		public function Get($schemaNamePropertyName){
			if (array_key_exists($schemaNamePropertyName, $this->properties)){
				echo key($this->properties) .  ' : ' . current($this->properties) . '<br />';
			}
		}
	}
	
	/**
	 * Documents class
	 *
	 * hold an Array of Document
	 * 
	 * @author     Arthur GALLOUIN for NUXEO agallouin@nuxeo.com
	 */
	class Documents {
		
		private $documentsList;
		
		public function Documents($newDocList){
			$this->documentsList = null;
			$test = true;
			if (!empty($newDocList['entries'])){
				while (false !== $test) {
					$this->documentsList[] = new Document(current($newDocList['entries']));
					$test = each($newDocList['entries']);
				}
				$test = sizeof($this->documentsList);
				unset($this->documentsList[$test-1]);
			}
			elseif(!empty($newDocList['uid'])){
				$this->documentsList[] = new Document($newDocList);
			}else{
				echo 'file not found';
			}
		}
		
		public function Output(){
			$value = sizeof($this->documentsList);
			echo '<table>';
			for ($test = 0; $test < $value; $test ++){
				echo '<tr>';
				current($this->documentsList)->Output();
				next($this->documentsList);
				echo '</tr>';
			}
			echo '</table>';
		}
	}
?>