<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Config Directory currency backend model
 * Allows dispatching before and after events for each controller action
 */
namespace Magento\Config\Model\Config\Backend\Currency;

class Allow extends AbstractCurrency
{
    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Framework\Model\ModelResource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Model\ModelResource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $registry, $config, $scopeConfig, $resource, $resourceCollection, $data);
    }

    /**
     * Check is isset default display currency in allowed currencies
     * Check allowed currencies is available in installed currencies
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterSave()
    {
        $exceptions = [];
        foreach ($this->_getAllowedCurrencies() as $currencyCode) {
            if (!in_array($currencyCode, $this->_getInstalledCurrencies())) {
                $exceptions[] = __(
                    'Selected allowed currency "%1" is not available in installed currencies.',
                    $this->_localeCurrency->getCurrency($currencyCode)->getName()
                );
            }
        }

        if (!in_array($this->_getCurrencyDefault(), $this->_getAllowedCurrencies())) {
            $exceptions[] = __(
                'Default display currency "%1" is not available in allowed currencies.',
                $this->_localeCurrency->getCurrency($this->_getCurrencyDefault())->getName()
            );
        }

        if ($exceptions) {
            throw new \Magento\Framework\Exception\LocalizedException(__(join("\n", $exceptions)));
        }

        return $this;
    }
}
