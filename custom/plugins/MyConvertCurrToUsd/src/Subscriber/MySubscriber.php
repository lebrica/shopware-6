<?php declare(strict_types=1);


namespace My\ConvertCurrToUsd\Subscriber;


use My\ConvertCurrToUsd\Service\ConvertService;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class MySubscriber implements EventSubscriberInterface
{
    private $convert;
    private $systemConfigService;


    public function __construct(ConvertService $convert, SystemConfigService $systemConfigService)
    {
        $this->convert = $convert;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductEvents::PRODUCT_LOADED_EVENT => 'onProductsLoaded'
        ];
    }

    public function onProductsLoaded(EntityLoadedEvent $event)
    {
        $currencyFactorUsd = $this->convert->getCurrencyFactor('USD');
        $customCurrencyFactorUsd = $this->systemConfigService->get('MyConvertCurrToUsd.config.customCurrencyFactorUsd');
        $currencyFactor = $customCurrencyFactorUsd ? $customCurrencyFactorUsd : $currencyFactorUsd;

        foreach ($event->getEntities() as $item) {
            $currencyEU = $item->getCurrencyPrice(Defaults::CURRENCY)->getGross();
            $priceUsd = $currencyFactor * $currencyEU;

            $item->addExtension('currency_usd',new ArrayEntity(['currency_usd'=> $priceUsd, 'standart_currency'=> $currencyEU]));
        }
    }
}
