<?php
namespace Teknio\LinkGuestOrder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
class LinkGuestOrderObserver implements ObserverInterface
{
    const COOKIE_NAME = 'link_order_value';
    protected CustomerRepositoryInterface $customerRepository;
    protected CookieManagerInterface $cookieManager;
    protected CookieMetadataFactory $cookieMetadataFactory;
    protected SessionManagerInterface $sessionManagerInterface;

    /**
     * LinkGuestOrderObserver constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository;
     * @param CookieManagerInterface $cookieManager;
     * @param CookieMetadataFactory $cookieMetadataFactory;
     * @param SessionManagerInterface $sessionManagerInterface;
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CookieMetadataFactory $cookieMetadataFactory,
        CookieManagerInterface $cookieManager,
        SessionManagerInterface $sessionManagerInterface
    ) {
        $this->customerRepository = $customerRepository;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager = $cookieManager;
        $this->sessionManagerInterface = $sessionManagerInterface;
    }

    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        // Get the cookie value
        $linkOrderValue = $this->cookieManager->getCookie(self::COOKIE_NAME);
        
        if ($linkOrderValue === 'true' && $order->getCustomerIsGuest()) {
            $customerEmail = $order->getCustomerEmail();

            try {
                // Load the customer by email
                $customer = $this->customerRepository->get($customerEmail);
                
                // Link the order to the customer's account
                $order->setCustomerId($customer->getId());
                $order->setCustomerIsGuest(0);
                $order->setCustomerGroupId($customer->getGroupId());
                $order->save();

                $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                    ->setDuration(-250000)
                    ->setPath($this->sessionManagerInterface->getCookiePath())
                    ->setDomain($this->sessionManagerInterface->getCookieDomain())
                    ->setHttpOnly(false);
       
                $this->cookieManager->setPublicCookie('link_order_value', '', $cookieMetadata);               
            } catch (LocalizedException $e) {
                
                // Handle exception if the customer does not exist
            }
        }
    }
 
}
