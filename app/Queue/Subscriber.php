<?php

namespace App\Queue;

interface Subscriber {

  function subscribe(array $route_keys);

  /*
   * @return Iterable Message
   */
  function messages(): Iterable; 
}
