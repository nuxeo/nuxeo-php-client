<?php

namespace Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase;

class NuxeoTest extends TestCase {
  /**
   * @return void
   */
  public function test_nuxeo() {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('bc7b4e34-b002-4f8f-b98c-e6aafa3be5df');
  }

  /**
   * Creates the application.
   *
   * @return \Illuminate\Foundation\Application
   */
  public function createApplication() {
    $app = require __DIR__ . '/../../bootstrap/app.php';

    $app->make(Kernel::class)->bootstrap();

    return $app;
  }
}
