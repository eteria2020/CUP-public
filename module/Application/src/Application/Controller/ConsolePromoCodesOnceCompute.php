<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use SharengoCore\Entity\Repository\PromoCodesOnceRepository;
use SharengoCore\Entity\Repository\PromoCodesInfoRepository;
use SharengoCore\Entity\PromoCodesInfo;
use SharengoCore\Entity\PromoCodesOnce;
use SharengoCore\Service\PromoCodesOnceService;
use SharengoCore\Service\SimpleLoggerService as Logger;

class ConsolePromoCodesOnceCompute extends AbstractActionController {

    /**
     * @var $EntityManager
     */
    private $entityManager;

    /**
     * @var $pcoService
     */
    private $pcoService;

    /**
     * @var $Logger
     */
    private $logger;

    /**
     * @var $PromoCodesOnce
     */
    private $repository;

    /**
     * @var $PciRepository
     */
    private $pciRepository;

    /**
     * @param EntityManager $entityManager
     * @param PromoCodesOnce $promoCodesOnce
     * @param Logger $logger
     */
    public function __construct(
        EntityManager $entityManager, 
        PromoCodesOnceRepository $pcoRepository,
        PromoCodesInfoRepository $pciRepository,
        PromoCodesOnceService $pcoService,
        Logger $logger
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $pcoRepository;
        $this->pciRepository = $pciRepository;
        $this->pcoService = $pcoService;
        $this->logger = $logger;
    }

    public function InsertNewPromocodeAction() {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $pciId = intval($request->getParam('promocodesInfoId'));
        $qty = intval($request->getParam('qty'));

        $promocodesInfoId = $this->pciRepository->findById($pciId);

        for ($i = 0; $i < $qty; $i++) {

            do {
                $promocode = $this->GetPromocode4_4();
            } while ($this->pcoService->getByPromoCode($promocode) !== NULL);

            if ($this->pcoService->getByPromoCode($promocode) === NULL) {
                $this->logger->log($i . " " . $promocode . "\n");

                $promoCodesOnce = new PromoCodesOnce($promocodesInfoId, $promocode);
                $this->entityManager->persist($promoCodesOnce);
                $this->entityManager->flush();
            }
        }
    }

    public function UsePromocodeAction() {
        
        $this->logger->log("test use\n");
        
        $customersRepository = $this->entityManager->getRepository('SharengoCore\Entity\Customers');
        //$customer = $customersRepository->findByCI("email","enrico.taddei@gmail.com");
        
        $customer = $customersRepository->getUserByEmailPassword("enrico.taddei@gmail.com","508ee8d3c2a15d9edb22927cfb8c6ff2");

        $promocode = "X0C8-H20M";
        //$promocode = "AAA-BBB";
        //$result = $promoCodesOnce = $this->pcoService->usePromoCode($customer, $promocode);
        var_dump($this->pcoService->isValid($promocode));
//         $promoCodesOnce =$this->pcoService->getByPromoCode($promocode);
//         if($promoCodesOnce!==NULL){   // find promocode once
//            var_dump($promoCodesOnce->getUsedTs());
//            
//            if($promoCodesOnce->getUsedTs()===NULL) { // promocode not used
//                var_dump($promoCodesOnce->getUsedTs());
//                $promoCodesInfo = $promoCodesOnce->getPromoCodesInfo();
//                if($promoCodesInfo->getActive()){       // promocode info is active
//                    $now = new \DateTime();
//
//                    if($now>=$promoCodesInfo->getValidFrom() && 
//                            $now<=$promoCodesInfo->getValidTo()){
//                       $this->logger->log("update\n");
//                    }
//                    else {
//                       $this->logger->log("promo code exiped\n");
//                    }
//                }
//                else {
//                    $this->logger->log("promo code not active\n");
//                }
//            }
//            else {
//                $this->logger->log("promo code once used\n");
//            }
//         } else {
//             $this->logger->log("promo code once not found\n");
//         }
        
    }

    private function GetPromocode4_4() {
        return $this->RandomString(4) . "-" .
                $this->RandomString(4);
    }

    private function RandomString($length = 10) {
        //$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
