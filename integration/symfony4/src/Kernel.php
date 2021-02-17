<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel {
  use MicroKernelTrait;

  public function registerBundles(): iterable {
    return [new \Symfony\Bundle\FrameworkBundle\FrameworkBundle()];
  }

  public function getProjectDir(): string {
    return \dirname(__DIR__);
  }

  protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void {
    $container->prependExtensionConfig('framework', ['test' => true]);
  }

  protected function configureRoutes(RouteCollectionBuilder $routes): void {
    $routes->add('/', 'App\Controller\NuxeoController::index');
  }
}
