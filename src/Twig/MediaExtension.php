<?php

namespace App\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class MediaExtension extends AbstractExtension
{
    public function __construct(
        private readonly UploaderHelper $uploader,
        private readonly CacheManager $cacheManager,
        private readonly FilterConfiguration $filterConfig,
    ) {
    }

    public function getFunctions(): array
    {
        $names = [
            'media' => 'getMedia',
            'media_url' => 'getMediaUrl',
        ];

        $functions = [];
        foreach ($names as $twig => $local) {
            $functions[] = new TwigFunction($twig, [$this, $local], ['is_safe' => ['html']]);
        }

        return $functions;
    }

    public function getMedia(
        ?object $obj,
        ?string $fieldName,
        array $attributes = [],
        ?string $filter = 'avatar',
        array $runtimeConfig = [],
        ?string $className = null,
    ): string {
        if (null === $obj) {
            return '';
        }

        $url = $this->getMediaUrl($obj, $fieldName, $filter, $runtimeConfig, $className);
        if (null == $url) {
            return '';
        }

        $filters = null !== $filter ? $this->filterConfig->get($filter)['filters'] : [];
        foreach ($filters as $filter) {
            if (isset($filter['width']) && !isset($attributes['width'])) {
                $attributes['width'] = $filter['width'];
            }
            if (isset($filter['height']) && !isset($attributes['height'])) {
                $attributes['height'] = $filter['height'];
            }
        }

        $attr = '';
        foreach ($attributes as $attribute => $value) {
            $attr .= ' '.$attribute.'="'.$value.'"';
        }

        return '<img src="'.$url.'"'.$attr.'/>';
    }

    public function getMediaUrl(
        object $obj,
        ?string $fieldName,
        ?string $filter = 'cache',
        ?array $runtimeConfig = [],
        ?string $className = null,
    ): ?string {
        $path = $this->uploader->asset($obj, $fieldName, $className);
        if (null === $path) {
            return null;
        }

        if (null === $filter) {
            return $path;
        }

        return $this->cacheManager->getBrowserPath($path, $filter, $runtimeConfig);
    }
}
