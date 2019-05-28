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

    public function PromocodeOnceMainAction() {
        $this->logger->setOutputEnvironment(Logger::OUTPUT_ON);
        $this->logger->setOutputType(Logger::TYPE_CONSOLE);

        $request = $this->getRequest();
        $actionType = $request->getParam('actionType');

        $this->logger->log("PromocodeOnceMainAction ".$actionType."\n");
        if($actionType==="new"){
            //php ../public/public/index.php  promocodesonce new promocodesInfoId  qty
            $pciId = intval($request->getParam('param1'));
            $qty = intval($request->getParam('param2'));
            $this->insertNewPromocode($pciId, $qty);

        } elseif($actionType==="use"){
            //php ../public/public/index.php  promocodesonce use email  promocode

            $email = $request->getParam('param1');
            $promocode = $request->getParam('param2');
            $this->usePromocode($email, $promocode);
        }elseif($actionType==="check"){
            //php ../public/public/index.php  promocodesonce check  promocode no-used
            $promocode = $request->getParam('param1');
            if($this->checkPromocode($promocode)){
                $this->logger->log("promocode ".$promocode." is valid\n");
            }else {
                $this->logger->log("promocode ".$promocode." NOT valid\n");
            }
        } else {
            $this->logger->log("command un-know\n");
        }
    }

    private function insertNewPromocode($pciId, $qty) {
        $result= FALSE;
            //php ../public/public/index.php  promocodesonce new promocodesInfoId  qty
//            $pciId = intval($request->getParam('param1'));
//            $qty = intval($request->getParam('param2'));

        try {
            $promocodesInfoId = $this->pciRepository->findById($pciId);

            for ($i = 0; $i < $qty; $i++) {

                do {
                    $promocode = $this->getPromocode4_4();
                } while ($this->pcoService->getByPromoCode($promocode) !== NULL);

                if ($this->pcoService->getByPromoCode($promocode) === NULL) {
                    $this->logger->log($i . " " . $promocode . "\n");

                    $promoCodesOnce = new PromoCodesOnce($promocodesInfoId, $promocode);
                    $this->entityManager->persist($promoCodesOnce);
                    $this->entityManager->flush();
                }
            }

            $result=TRUE;
        } catch (Exception $ex) {
            throwException($ex);
        }

        return $result;
    }

    private function usePromocode($email, $promocode) {
        $result= FALSE;
        try{
            $this->logger->log("usePromocode ".$email." ".$promocode."\n");
            $customersRepository = $this->entityManager->getRepository('SharengoCore\Entity\Customers');
            $customer = $customersRepository->findByCI("email", $email);
            if (empty($customer)) {
                $this->logger->log("update FAIL\n");
            } else {
                $customer = $customer[0];
                if($this->pcoService->isValid($promocode)) {
                    $this->pcoService->usePromoCode($customer, $promocode);
                    $this->logger->log("update success\n");
                    $result=TRUE;
                }else {
                    $this->logger->log("update FAIL\n");
                }
            }
        } catch (Exception $ex) {
            throwException($ex);
        }

        return $result;
    }

    private function checkPromocode($promocode){
        return $this->pcoService->checkPromoCodeOnce($promocode);
    }

    private function getPromocode4_4() {
        return $this->randomString(4) . "-" .
                $this->randomString(4);
    }

    private function randomString($length = 10) {
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
