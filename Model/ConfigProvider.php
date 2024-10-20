<?php namespace Teknio\LinkGuestOrder\Model;
 
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface; 

class ConfigProvider implements ConfigProviderInterface
{
    const XML_PATH_ENABLE_CHECKBOX = 'checkout/options/enable_link_order';
    
   /** @var ScopeConfigInterface */
    protected $scopeConfig;
 
   public function __construct(
        ScopeConfigInterface $scopeConfig
    )
   {
       $this->scopeConfig = $scopeConfig;
   }
 
   public function getConfig()
   {
        
       return [
           'isEnabledLinkOrder' => $this->getIsEnabledLinkOrder()=='1' ? true : false
       ];
   }

       /**
     * Get dynamic shipping method delivery time from config
     *
     * @return boolean
     */
    public function getIsEnabledLinkOrder()
    {
        // Fetch serialized config value
        $configValue = $this->scopeConfig->getValue(SELF::XML_PATH_ENABLE_CHECKBOX, ScopeInterface::SCOPE_STORE);
        
        return $configValue;
    }
}