<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new ApiBundle\ApiBundle(),
            new AppBundle\AppBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Sensio\Bundle\DistributionBundle\SensioDistributionBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle(),
            new Symfony\Bundle\DebugBundle\DebugBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
            new TangoMan\CallbackBundle\TangoManCallbackBundle(),
            new TangoMan\CSVReaderBundle\TangoManCSVReaderBundle(),
            new TangoMan\JWTBundle\TangoManJWTBundle(),
            new TangoMan\ListManagerBundle\TangoManListManagerBundle(),
            new TangoMan\MenuBundle\TangoManMenuBundle(),
            new TangoMan\PaginationBundle\TangoManPaginationBundle(),
            new TangoMan\RoleBundle\TangoManRoleBundle(),
            new TangoMan\TruncateHtmlBundle\TangoManTruncateHtmlBundle(),
            new TangoMan\UserBundle\TangoManUserBundle(),
            new Tiloweb\Base64Bundle\TilowebBase64Bundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
//            new TangoMan\JsonDecodeBundle\TangoManJsonDecodeBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new TangoMan\TestBundle\TangoManTestBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
