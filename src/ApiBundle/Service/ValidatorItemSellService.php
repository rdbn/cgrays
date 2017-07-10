<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 06.07.17
 * Time: 23:12
 */

namespace ApiBundle\Service;

use ApiBundle\Validator\IdItemConstraint;
use ApiBundle\Validator\PageConstraint;
use ApiBundle\Validator\PriceConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorItemSellService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var array
     */
    private $error;

    /**
     * ValidationItemService constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $itemSell
     * @return $this
     */
    public function handler($itemSell)
    {
        if (!is_array($itemSell)) {
            $this->error[] = true;
            return $this;
        }

        $priceConstraint = new PriceConstraint();
        $errorPriceList = $this->validator->validate($itemSell, $priceConstraint);
        if (count($errorPriceList) > 0) {
            $this->error['price'] = $errorPriceList[0]->getMessage();
        }

        $pageConstraint = new PageConstraint();
        $errorPageList = $this->validator->validate($itemSell, $pageConstraint);
        if (count($errorPageList) > 0) {
            $this->error[] = true;
        }

        $idItemConstraint = new IdItemConstraint();
        $errorIdItemList = $this->validator->validate($itemSell, $idItemConstraint);
        if (count($errorIdItemList) > 0) {
            $this->error[] = true;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (count($this->error) == 0) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        if (isset($this->error['price'])) {
            return $this->error;
        }

        return 'Bad request';
    }
}