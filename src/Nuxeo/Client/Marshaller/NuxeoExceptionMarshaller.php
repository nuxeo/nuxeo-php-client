<?php
/**
 * (C) Copyright 2017 Nuxeo SA (http://nuxeo.com/) and contributors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Nuxeo\Client\Marshaller;


use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\VisitorInterface;
use Nuxeo\Client\Spi\NotImplementedException;

use Nuxeo\Client\Spi\NuxeoException;

class NuxeoExceptionMarshaller implements NuxeoMarshaller {

  /**
   * @param $in
   * @param VisitorInterface $visitor
   * @param DeserializationContext $context
   * @return NuxeoException
   * @throws \ReflectionException
   */
  public function read($in, VisitorInterface $visitor, DeserializationContext $context) {
    $exception = new NuxeoException($in['message']);

    if(array_key_exists('exception', $in)) {
      $backtrace = $in['exception']['stackTrace'];
      $location = array_shift($backtrace);

      $exception
        ->setLocation($location['fileName'], $location['lineNumber'])
        ->setTrace(array_map(function($trace) {
          return array(
            'line' => $trace['lineNumber'],
            'file' => $trace['fileName'],
            'class' => $trace['className'],
            'function' => $trace['methodName'],
          );
        }, $backtrace));
    }

    return $exception;
  }

  /**
   * @param $object
   * @param VisitorInterface $visitor
   * @param SerializationContext $context
   * @throws \Nuxeo\Client\Spi\NotImplementedException
   */
  public function write($object, VisitorInterface $visitor, SerializationContext $context) {
    throw new NotImplementedException();
  }

}
